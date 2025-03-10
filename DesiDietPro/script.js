/* 
 * script.js
 * Handles fetching category items, adding them to a meal, and saving the meal.
 */

// ----------------------------
// 1. FETCH CATEGORY SUGGESTIONS
// ----------------------------
function fetchCategory() {
    // Get the user input (the category) and remove extra spaces
    let category = document.getElementById("categoryInput").value.trim();

    // If empty, clear suggestions and exit
    if (category.length === 0) {
        document.getElementById("suggestions").innerHTML = "";
        return;
    }

    console.log("🔍 Fetching category suggestions for:", category);

    // Make a GET request to search.php with "search=..."
    fetch(`search.php?search=${encodeURIComponent(category)}`)
        .then(response => response.json())
        .then(data => {
            console.log("📜 Received Data:", data);

            let suggestionsList = document.getElementById("suggestions");
            suggestionsList.innerHTML = ""; // Clear old suggestions

            // If there's an error (e.g., no items found), display it
            if (data.error) {
                let li = document.createElement("li");
                li.textContent = data.error;
                suggestionsList.appendChild(li);
                return;
            }

            // Otherwise, data.descriptions should be an array of items in that category
            if (data.descriptions) {
                data.descriptions.forEach(description => {
                    // Create a list item for each matching item
                    let li = document.createElement("li");
                    li.textContent = description;

                    // Add button to let user add this item to the meal
                    let addButton = document.createElement("button");
                    addButton.textContent = "Add";
                    addButton.style.marginLeft = "10px";

                    // When clicked, add the item to the meal table
                    addButton.onclick = function() {
                        addToMeal(description);
                    };

                    li.appendChild(addButton);
                    suggestionsList.appendChild(li);
                });
            } else {
                // Fallback if "descriptions" isn't present
                let li = document.createElement("li");
                li.textContent = "No matching items found.";
                suggestionsList.appendChild(li);
            }
        })
        .catch(error => console.error("❌ Fetch Error:", error));
}

// ----------------------------
// 2. ADD SELECTED ITEM TO MEAL
// ----------------------------
function addToMeal(description) {
    // Get the <tbody> of the meal table
    let mealTableBody = document.getElementById("mealTable").getElementsByTagName("tbody")[0];

    // Create a new row
    let newRow = mealTableBody.insertRow();

    // First cell: description
    let descCell = newRow.insertCell(0);
    descCell.textContent = description;

    // Second cell: Remove button
    let actionCell = newRow.insertCell(1);
    let removeButton = document.createElement("button");
    removeButton.textContent = "Remove";
    removeButton.onclick = function() {
        mealTableBody.removeChild(newRow);
    };
    actionCell.appendChild(removeButton);
}

// ----------------------------
// 3. SAVE THE ENTIRE MEAL
// ----------------------------
function saveMeal() {
    // Get the meal name
    let mealName = document.getElementById("mealName").value.trim();

    if (mealName.length === 0) {
        alert("⚠️ Please enter a meal name.");
        return;
    }

    // Collect all items from the meal table
    let mealItems = [];
    let mealTableBody = document.getElementById("mealTable").getElementsByTagName("tbody")[0];
    for (let i = 0; i < mealTableBody.rows.length; i++) {
        let desc = mealTableBody.rows[i].cells[0].textContent;
        mealItems.push(desc);
    }

    if (mealItems.length === 0) {
        alert("⚠️ Please add at least one item to your meal before saving.");
        return;
    }

    // Prepare data to send
    let postData = {
        mealName: mealName,
        items: mealItems
    };

    // Send a POST request to saveMeal.php
    fetch("saveMeal.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(postData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ Meal saved successfully!");
            // Clear the meal name and the table
            document.getElementById("mealName").value = "";
            mealTableBody.innerHTML = "";
        } else {
            alert("❌ Error saving meal: " + data.error);
        }
    })
    .catch(error => console.error("❌ Error saving meal:", error));
}