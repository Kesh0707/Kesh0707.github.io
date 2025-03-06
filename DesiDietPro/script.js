function fetchSearchResults() {
    let searchQuery = document.getElementById("searchInput").value;

    if (searchQuery.length < 1) {
        return;
    }

    console.log("🔍 Searching for:", searchQuery);

    fetch(`search.php?search=${searchQuery}`)
        .then(response => response.json())
        .then(data => {
            console.log("📜 Received Data:", data);

            let dataList = document.getElementById("searchResults");
            let detailsDiv = document.getElementById("foodDetails");

            dataList.innerHTML = ""; // Clear previous suggestions
            detailsDiv.innerHTML = ""; // Clear previous details

            // If we received full food details, display them
            if (data.category) {
                detailsDiv.innerHTML = `
                    <h3>${data.description}</h3>
                    <p><strong>Category:</strong> ${data.category}</p>
                    <p><strong>Carbohydrate:</strong> ${data.carbohydrate} g</p>
                    <p><strong>Protein:</strong> ${data.protein} g</p>
                    <p><strong>Fat:</strong> ${data.fat_total} g</p>
                    <p><strong>Fiber:</strong> ${data.fiber} g</p>
                    <p><strong>Sugar:</strong> ${data.sugar_total} g</p>
                    <p><strong>Cholesterol:</strong> ${data.cholesterol} mg</p>
                    <p><strong>Calcium:</strong> ${data.calcium} mg</p>
                    <p><strong>Iron:</strong> ${data.iron} mg</p>
                    <p><strong>Potassium:</strong> ${data.potassium} mg</p>
                `;
            }
            // If we received a list of descriptions, populate the drop-down list
            else if (data.descriptions) {
                data.descriptions.forEach(description => {
                    let option = document.createElement("option");
                    option.value = description;
                    dataList.appendChild(option);
                });
            }
            // If no results were found, show an error message
            else if (data.error) {
                detailsDiv.innerHTML = `<p>${data.error}</p>`;
            }
        })
        .catch(error => console.error(" Fetch Error:", error));
}
