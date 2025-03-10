/* 
 * script.js
 * Handles fetching category items, adding them to a meal, and saving the meal with calorie calculations.
 */

// ----------------------------
// 1. FETCH CATEGORY SUGGESTIONS
// ----------------------------
function fetchCategory() {
    // Get the user input from the category input field
    let category = document.getElementById("categoryInput").value.trim();

    // If input is empty, clear suggestions and exit
    if (category.length === 0) {
        document.getElementById("suggestions").innerHTML = "";
        return;
    }

    console.log("🔍 Fetching category suggestions for:", category);

    // Send the GET request with the "search" parameter (using encodeURIComponent for safety)
    fetch(`search.php?search=${encodeURIComponent(category)}`)
        .then(response => response.json())
        .then(data => {
            console.log("📜 Received Data:", data);

            let suggestionsList = document.getElementById("suggestions");
            suggestionsList.innerHTML = ""; // Clear previous suggestions

            // Check if an error was returned
            if (data.error) {
                let li = document.createElement("li");
                li.textContent = data.error;
                suggestionsList.appendChild(li);
                return;
            }

            // We now expect data.items to be an array of objects with nutritional info
            if (data.items) {
                data.items.forEach(item => {
                    // Calculate calories using the standard formula:
                    // calories = (protein * 4) + (carbohydrate * 4) + (fat_total * 9)
                    let protein = parseFloat(item.protein) || 0;
                    let carbs = parseFloat(item.carbohydrate) || 0;
                    let fat = parseFloat(item.fat_total) || 0;
                    let cals = (protein * 4) + (carbs * 4) + (fat * 9);

                    // Create a list item showing the description and calories
                    let li = document.createElement("li");
                    li.textContent = `${item.description} (~${cals.toFixed(0)} cal)`;

                    // Create an "Add" button to add this item to the meal table
                    let addButton = document.createElement("button");
                    addButton.textContent = "Add";
                    addButton.style.marginLeft = "10px";
                    // Pass the nutritional info to addToMeal()
                    addButton.onclick = function() {
                        addToMeal(item.description, protein, carbs, fat, cals);
                    };

                    li.appendChild(addButton);
                    suggestionsList.appendChild(li);
                });
            } else {
                // Fallback if "items" is not present
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
function addToMeal(description, protein, carbs, fat, cals) {
    // Get the meal table's tbody element
    let mealTableBody = document.getElementById("mealTable").querySelector("tbody");

    // Ensure numerical values are correctly formatted (fallback to 0 if NaN)
    protein = parseFloat(protein) || 0;
    carbs = parseFloat(carbs) || 0;
    fat = parseFloat(fat) || 0;
    cals = parseFloat(cals) || 0;

    // Create a new row
    let newRow = mealTableBody.insertRow();

    // Cell 1: Description
    let descCell = newRow.insertCell(0);
    descCell.textContent = description;

    // Cell 2: Calories for this item
    let calCell = newRow.insertCell(1);
    calCell.textContent = cals.toFixed(2);

    // Cell 3: Store macros using `data-*` attributes for easier extraction
    let macroCell = newRow.insertCell(2);
    macroCell.setAttribute("data-protein", protein.toFixed(2));
    macroCell.setAttribute("data-carbs", carbs.toFixed(2));
    macroCell.setAttribute("data-fat", fat.toFixed(2));
    macroCell.innerHTML = `
        Protein: ${protein.toFixed(2)} g<br>
        Carbs: ${carbs.toFixed(2)} g<br>
        Fat: ${fat.toFixed(2)} g
    `;

    // Cell 4: Remove button
    let actionCell = newRow.insertCell(3);
    let removeButton = document.createElement("button");
    removeButton.textContent = "Remove";
    removeButton.onclick = function() {
        mealTableBody.removeChild(newRow);
        calculateMealTotal(); // ✅ Recalculate totals after removal
    };
    actionCell.appendChild(removeButton);

    // ✅ Recalculate meal totals after adding an item
    calculateMealTotal();
}


// ----------------------------
// 3. CALCULATE TOTAL CALORIES FOR THE MEAL
// ----------------------------
function calculateMealTotal() {
    let mealTableBody = document.getElementById("mealTable").querySelector("tbody");
    let totalCals = 0, totalProtein = 0, totalCarbs = 0, totalFats = 0;

    for (let i = 0; i < mealTableBody.rows.length; i++) {
        let row = mealTableBody.rows[i];

        // ✅ Extract Calories from cell index 1
        let rowCals = parseFloat(row.cells[1].textContent) || 0;
        totalCals += rowCals;

        // ✅ Extract macros from `data-*` attributes in cell index 2
        let protein = parseFloat(row.cells[2].getAttribute("data-protein")) || 0;
        let carbs = parseFloat(row.cells[2].getAttribute("data-carbs")) || 0;
        let fat = parseFloat(row.cells[2].getAttribute("data-fat")) || 0;

        totalProtein += protein;
        totalCarbs += carbs;
        totalFats += fat;
    }

    // ✅ Update the UI with new totals
    document.getElementById("totalCals").textContent = totalCals.toFixed(2);
    document.getElementById("totalProtein").textContent = totalProtein.toFixed(2);
    document.getElementById("totalCarbs").textContent = totalCarbs.toFixed(2);
    document.getElementById("totalFats").textContent = totalFats.toFixed(2);
}


// ----------------------------
// 4. SAVE THE ENTIRE MEAL
// ----------------------------
function saveMeal() {
    let mealName = document.getElementById("mealName").value.trim();

    if (mealName.length === 0) {
        alert("⚠️ Please enter a meal name.");
        return;
    }

    // Collect meal items (descriptions only for saving; you could also include macros if desired)
    let mealItems = [];
    let mealTableBody = document.getElementById("mealTable").querySelector("tbody");
    for (let i = 0; i < mealTableBody.rows.length; i++) {
        let desc = mealTableBody.rows[i].cells[0].textContent;
        mealItems.push(desc);
    }

    if (mealItems.length === 0) {
        alert("⚠️ Please add at least one item to your meal before saving.");
        return;
    }

    // Optionally, also send the total calories calculated
    let totalDiv = document.getElementById("mealTotals");
    let totalCals = totalDiv ? totalDiv.textContent.replace("Total Meal Calories: ", "") : "";

    let postData = {
        mealName: mealName,
        items: mealItems,
        totalCalories: totalCals // optional
    };

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
                document.getElementById("mealName").value = "";
                mealTableBody.innerHTML = "";
                calculateMealTotal();
            } else {
                alert("❌ Error saving meal: " + data.error);
            }
        })
        .catch(error => console.error("❌ Error saving meal:", error));
}