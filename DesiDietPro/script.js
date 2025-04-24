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
