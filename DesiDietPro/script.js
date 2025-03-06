function searchFood() {
    let query = document.getElementById("searchBox").value;

    if (query.length < 1) {
        document.getElementById("result").innerHTML = "";
        return;
    }

    console.log("Searching for:", query); // to check if what the user types is being read

    fetch(`search.php?q=${query}`)
        .then(response => response.json())
        .then(data => {
            console.log("📜 JSON data received:", data);

            if (data.error) {
                document.getElementById("result").innerHTML = "<p>Food not found.</p>";
            } else {
                document.getElementById("result").innerHTML = `
                    <h3>${data.description}</h3>
                    <p>Carbohydrate: ${data.carbohydrate} g</p>
                    <p>Protein: ${data.protein} g</p>
                    <p>Fat: ${data.fat_total} g</p>
                    <p>Fiber: ${data.fiber} g</p>
                    <p>Sugar: ${data.sugar_total} g</p>
                    <p>Cholesterol: ${data.cholesterol} mg</p>
                    <p>Calcium: ${data.calcium} mg</p>
                    <p>Iron: ${data.iron} mg</p>
                    <p>Potassium: ${data.potassium} mg</p>
                `;
            }
        })
        .catch(error => console.error(" Error fetching data:", error));
}

// updated column names and added logging
