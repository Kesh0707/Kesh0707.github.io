// script.js
// Dish builder and meal planner stuff

// gets food suggestions when typing
function fetchDescription() {
    var input = document.getElementById("categoryInput");
    var suggestions = document.getElementById("suggestions");

    if (!input || !suggestions) return; // just bail if missing

    var query = input.value.trim();
    if (query.length === 0) {
        suggestions.innerHTML = "";
        return;
    }

    fetch("search.php?search=" + encodeURIComponent(query))
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        suggestions.innerHTML = "";

        if (data.error) {
            var li = document.createElement("li");
            li.textContent = data.error;
            suggestions.appendChild(li);
            return;
        }

        if (data.items) {
            data.items.forEach(function(item) {
                var protein = parseFloat(item.protein) || 0;
                var carbs = parseFloat(item.carbohydrate) || 0;
                var fat = parseFloat(item.fat_total) || 0;
                var cals = protein*4 + carbs*4 + fat*9;

                var li = document.createElement("li");
                li.textContent = item.description + " (~" + Math.round(cals) + " cal)";

                var btn = document.createElement("button");
                btn.textContent = "Add";
                btn.style.marginLeft = "8px";
                btn.addEventListener("click", function() {
                    addToMeal(item.description, protein, carbs, fat, cals);
                });

                li.appendChild(btn);
                suggestions.appendChild(li);
            });
        }
    })
    .catch(function(err) {
        console.log("fetch failed?", err); // didn't bother with fancy error here
    });
}

// adds item to meal table
function addToMeal(description, proteinPer100, carbsPer100, fatPer100, calsPer100) {
    let table = document.getElementById("mealTable");
    if (!table) return;
    let tbody = table.querySelector("tbody");
    let row = tbody.insertRow();

    // quick unit map (could be expanded later maybe)
    const units = {
        "g": 1, "ml": 1, "pinch": 0.36, "dash": 0.6,
        "handful": 35, "fistful": 28, "katori": 200,
        "chammach": 15, "chhoti chammach": 5, "cup": 240
    };

    row.insertCell(0).textContent = description;

    var amtCell = row.insertCell(1);
    var input = document.createElement("input");
    input.type = "number";
    input.min = "1";
    input.value = "1";
    input.style.width = "45px";

    var unitSelect = document.createElement("select");
    for (let u in units) {
        var opt = document.createElement("option");
        opt.value = u;
        opt.textContent = u;
        unitSelect.appendChild(opt);
    }

    amtCell.appendChild(input);
    amtCell.appendChild(unitSelect);

    var calCell = row.insertCell(2);
    var macroCell = row.insertCell(3);
    var actionCell = row.insertCell(4);

    var removeBtn = document.createElement("button");
    removeBtn.textContent = "Remove";
    removeBtn.addEventListener("click", function() {
        tbody.removeChild(row);
        calculateMealTotal();
    });

    actionCell.appendChild(removeBtn);

    function updateMacros() {
        let amt = parseFloat(input.value) || 0;
        let unit = unitSelect.value;
        let factor = (amt * units[unit]) / 100;

        let p = proteinPer100 * factor;
        let c = carbsPer100 * factor;
        let f = fatPer100 * factor;
        let k = calsPer100 * factor;

        macroCell.dataset.protein = p.toFixed(2);
        macroCell.dataset.carbs = c.toFixed(2);
        macroCell.dataset.fat = f.toFixed(2);
        calCell.dataset.calories = k.toFixed(2);

        calCell.textContent = k.toFixed(2) + " kcal";

        macroCell.innerHTML = "Protein: " + p.toFixed(2) + "g<br>" +
                              "Carbs: " + c.toFixed(2) + "g<br>" +
                              "Fat: " + f.toFixed(2) + "g";

        calculateMealTotal();
    }

    input.addEventListener("input", updateMacros);
    unitSelect.addEventListener("change", updateMacros);

    updateMacros(); // setup once
}

// totals up all the items
function calculateMealTotal() {
    var table = document.getElementById("mealTable");
    if (!table) return;

    var tbody = table.querySelector("tbody");
    var totalCals = 0, totalP = 0, totalC = 0, totalF = 0;

    for (var i = 0; i < tbody.rows.length; i++) {
        var row = tbody.rows[i];
        totalCals += parseFloat(row.cells[2].textContent) || 0;
        totalP += parseFloat(row.cells[3].dataset.protein) || 0;
        totalC += parseFloat(row.cells[3].dataset.carbs) || 0;
        totalF += parseFloat(row.cells[3].dataset.fat) || 0;
    }

    document.getElementById("totalCals").textContent = totalCals.toFixed(2);
    document.getElementById("totalProtein").textContent = totalP.toFixed(2);
    document.getElementById("totalCarbs").textContent = totalC.toFixed(2);
    document.getElementById("totalFats").textContent = totalF.toFixed(2);
}

// saves the dish
function saveMeal() {
    const nameInput = document.getElementById("mealName");
    if (!nameInput) return;

    var mealName = nameInput.value.trim();
    if (mealName === "") {
        alert("Please name your dish first.");
        return;
    }

    var table = document.getElementById("mealTable");
    var tbody = table.querySelector("tbody");

    if (tbody.rows.length === 0) {
        alert("Can't save an empty dish.");
        return;
    }

    var items = [];
    for (let row of tbody.rows) {
        items.push(row.cells[0].textContent); // maybe later save amounts too
    }

    var protein = parseFloat(document.getElementById("totalProtein").textContent) || 0;
    var carbs = parseFloat(document.getElementById("totalCarbs").textContent) || 0;
    var fats = parseFloat(document.getElementById("totalFats").textContent) || 0;
    var calories = parseFloat(document.getElementById("totalCals").textContent) || 0;

    fetch("saveMeal.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            mealName: mealName,
            items: items,
            totalProtein: protein,
            totalCarbs: carbs,
            totalFats: fats,
            totalCalories: calories
        })
    })
    .then(function(res) {
        return res.json();
    })
    .then(function(data) {
        if (data.success) {
            alert("Dish saved!");
            nameInput.value = "";
            tbody.innerHTML = "";
            calculateMealTotal();
        } else {
            alert("Error saving dish.");
        }
    })
    .catch(function(err) {
        console.error("save error", err);
    });
}

// drag and drop stuff (dashboard)

// allow dropping
function allowDrop(ev) {
    ev.preventDefault();
}

// start dragging
function drag(ev) {
    ev.dataTransfer.setData("text/plain", ev.target.dataset.id);
}

// drop into a zone
function drop(ev) {
    ev.preventDefault();
    var id = ev.dataTransfer.getData("text/plain");
    var mealBox = document.querySelector(".mealBox[data-id='" + id + "']");
    var zone = ev.target.closest(".mealZone");

    if (mealBox && zone) {
        zone.appendChild(mealBox);

        var oldBtn = mealBox.querySelector("button");
        if (oldBtn) oldBtn.remove();

        var removeBtn = document.createElement("button");
        removeBtn.textContent = "Remove";
        removeBtn.className = "removeButton";
        removeBtn.style.position = "absolute";
        removeBtn.style.top = "5px";
        removeBtn.style.right = "5px";

        removeBtn.addEventListener("click", function() {
            document.getElementById("savedMealsContainer").appendChild(mealBox);
            removeBtn.remove();
            addDeleteButton(mealBox);
            updateDailyTotals();
        });

        mealBox.appendChild(removeBtn);

        updateDailyTotals();
    }
}

// recalculate daily totals
function updateDailyTotals() {
    var protein = 0, carbs = 0, fats = 0, calories = 0;

    document.querySelectorAll(".mealZone").forEach(function(zone) {
        zone.querySelectorAll(".mealBox").forEach(function(meal) {
            protein += parseFloat(meal.dataset.protein) || 0;
            carbs += parseFloat(meal.dataset.carbs) || 0;
            fats += parseFloat(meal.dataset.fat) || 0;
            calories += parseFloat(meal.dataset.calories) || 0;
        });
    });

    document.getElementById("dailyProtein").textContent = protein.toFixed(2);
    document.getElementById("dailyCarbs").textContent = carbs.toFixed(2);
    document.getElementById("dailyFats").textContent = fats.toFixed(2);
    document.getElementById("dailyCalories").textContent = calories.toFixed(2);
}

// adds delete button back
function addDeleteButton(mealBox) {
    var delBtn = document.createElement("button");
    delBtn.textContent = "Delete";
    delBtn.className = "deleteButton";
    delBtn.style.position = "absolute";
    delBtn.style.top = "5px";
    delBtn.style.right = "5px";

    delBtn.addEventListener("click", function() {
        mealBox.remove();
        updateDailyTotals();
    });

    mealBox.appendChild(delBtn);
}

// make saved meals draggable again
window.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".mealBox").forEach(function(box) {
        box.setAttribute("draggable", "true");
        box.addEventListener("dragstart", drag);
    });
});
