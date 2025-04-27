// ----------------------------
// 1. FETCH DESCRIPTION SUGGESTIONS
// ----------------------------
function fetchDescription() {
    let query = document.getElementById("categoryInput").value.trim();

    if (query.length === 0) {
        document.getElementById("suggestions").innerHTML = "";
        return;
    }

    fetch(`search.php?search=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            let suggestionsList = document.getElementById("suggestions");
            suggestionsList.innerHTML = "";

            if (data.error) {
                let li = document.createElement("li");
                li.textContent = data.error;
                suggestionsList.appendChild(li);
                return;
            }

            if (data.items) {
                data.items.forEach(item => {
                    let protein = parseFloat(item.protein) || 0;
                    let carbs = parseFloat(item.carbohydrate) || 0;
                    let fat = parseFloat(item.fat_total) || 0;
                    let cals = (protein * 4) + (carbs * 4) + (fat * 9);

                    let li = document.createElement("li");
                    li.textContent = `${item.description} (~${cals.toFixed(0)} cal)`;

                    let addButton = document.createElement("button");
                    addButton.textContent = "Add";
                    addButton.style.marginLeft = "10px";
                    addButton.onclick = function () {
                        addToMeal(item.description, protein, carbs, fat, cals);
                    };

                    li.appendChild(addButton);
                    suggestionsList.appendChild(li);
                });
            } else {
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
function addToMeal(description, proteinPer100, carbsPer100, fatPer100, calsPer100) {
    const mealTableBody = document.getElementById("mealTable").querySelector("tbody");
    const newRow = mealTableBody.insertRow();

    const unitConversions = {
        "g": 1,
        "ml": 1,
        "pinch": 0.36,
        "dash": 0.6,
        "handful": 35,
        "fistful": 28,
        "katori": 200,
        "chammach": 15,
        "chhoti chammach": 5,
        "cup": 240
    };

    newRow.insertCell(0).textContent = description;

    const amountCell = newRow.insertCell(1);
    const amountInput = document.createElement("input");
    amountInput.type = "number";
    amountInput.min = "1";
    amountInput.value = "1";
    amountInput.style.width = "50px";

    const unitSelect = document.createElement("select");
    ["g", "ml", "pinch", "dash", "handful", "fistful", "katori", "chammach", "chhoti chammach", "cup"].forEach(unit => {
        const option = document.createElement("option");
        option.value = unit;
        option.textContent = unit;
        unitSelect.appendChild(option);
    });

    amountCell.appendChild(amountInput);
    amountCell.appendChild(unitSelect);

    const calCell = newRow.insertCell(2);
    const macroCell = newRow.insertCell(3);
    const actionCell = newRow.insertCell(4);

    const removeButton = document.createElement("button");
    removeButton.textContent = "Remove";
    removeButton.onclick = function () {
        mealTableBody.removeChild(newRow);
        calculateMealTotal();
    };
    actionCell.appendChild(removeButton);

    function updateMacros() {
        const amount = parseFloat(amountInput.value) || 0;
        const selectedUnit = unitSelect.value;
        const unitFactor = unitConversions[selectedUnit] || 1;
        const adjustedAmount = amount * unitFactor; // in g/ml
        const factor = adjustedAmount / 100;

        const protein = proteinPer100 * factor;
        const carbs = carbsPer100 * factor;
        const fat = fatPer100 * factor;
        const cals = calsPer100 * factor;

        macroCell.setAttribute("data-protein", protein.toFixed(2));
        macroCell.setAttribute("data-carbs", carbs.toFixed(2));
        macroCell.setAttribute("data-fat", fat.toFixed(2));
        calCell.setAttribute("data-calories", cals.toFixed(2));

        calCell.textContent = cals.toFixed(2) + " kcal";
        macroCell.innerHTML = `
            Protein: ${protein.toFixed(2)} g<br>
            Carbs: ${carbs.toFixed(2)} g<br>
            Fat: ${fat.toFixed(2)} g
        `;

        calculateMealTotal();
    }

    amountInput.addEventListener("input", updateMacros);
    unitSelect.addEventListener("change", updateMacros);

    updateMacros();
}

// ----------------------------
// 3. CALCULATE TOTAL CALORIES FOR THE MEAL
// ----------------------------
function calculateMealTotal() {
    const mealTableBody = document.getElementById("mealTable").querySelector("tbody");
    let totalCals = 0, totalProtein = 0, totalCarbs = 0, totalFats = 0;

    for (let i = 0; i < mealTableBody.rows.length; i++) {
        const row = mealTableBody.rows[i];
        const cals = parseFloat(row.cells[2].textContent) || 0;
        const protein = parseFloat(row.cells[3].getAttribute("data-protein")) || 0;
        const carbs = parseFloat(row.cells[3].getAttribute("data-carbs")) || 0;
        const fat = parseFloat(row.cells[3].getAttribute("data-fat")) || 0;

        totalCals += cals;
        totalProtein += protein;
        totalCarbs += carbs;
        totalFats += fat;
    }

    document.getElementById("totalCals").textContent = totalCals.toFixed(2);
    document.getElementById("totalProtein").textContent = totalProtein.toFixed(2);
    document.getElementById("totalCarbs").textContent = totalCarbs.toFixed(2);
    document.getElementById("totalFats").textContent = totalFats.toFixed(2);
}

// ----------------------------
// 4. SAVE MEAL FUNCTION
// ----------------------------
function saveMeal() {
    const mealName = document.getElementById("mealName").value.trim();

    if (mealName.length === 0) {
        alert("⚠️ Please enter a meal name.");
        return;
    }

    const mealTableBody = document.getElementById("mealTable").querySelector("tbody");
    if (mealTableBody.rows.length === 0) {
        alert("⚠️ Please add at least one item to your meal before saving.");
        return;
    }

    let mealItems = [];
    for (let i = 0; i < mealTableBody.rows.length; i++) {
        const desc = mealTableBody.rows[i].cells[0].textContent;
        mealItems.push(desc);
    }

    // ✅ Capture macros properly
    const totalProtein = parseFloat(document.getElementById("totalProtein").textContent) || 0;
    const totalCarbs = parseFloat(document.getElementById("totalCarbs").textContent) || 0;
    const totalFats = parseFloat(document.getElementById("totalFats").textContent) || 0;
    const totalCalories = parseFloat(document.getElementById("totalCals").textContent) || 0;

    let postData = {
        mealName: mealName,
        items: mealItems,
        totalProtein: totalProtein,
        totalCarbs: totalCarbs,
        totalFats: totalFats,
        totalCalories: totalCalories
    };

    fetch("saveMeal.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
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
    .catch(error => console.error("❌ Error:", error));
}
