
function searchFood() {
    let query = document.getElementById("searchBox").value;

    if (query.length < 1) {
        document.getElementById("result").innerHTML = "";
        return;
    }

    fetch(`search.php?q=${query}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById("result").innerHTML = "<p>Food not found.</p>";
            } else {
                document.getElementById("result").innerHTML = `
                    <h3>${data.name}</h3>
                    <p>Calories: ${data.calories} kcal</p>
                    <p>Protein: ${data.protein} g</p>
                    <p>Carbs: ${data.carbs} g</p>
                    <p>Fats: ${data.fats} g</p>
                `;
            }
        })
        .catch(error => console.error("Error:", error));
}