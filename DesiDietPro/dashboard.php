<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: myaccount.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$host = "localhost";
$dbUser = "root";
$dbPassword = "Era3nile867@";
$database = "food_db";

$conn = new mysqli($host, $dbUser, $dbPassword, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT id, meal_name, totalProtein, totalCarbs, totalFats, totalCalories, created_at FROM meals WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$savedMeals = [];
while ($row = $result->fetch_assoc()){
    $savedMeals[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DesiDietPro</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional styles for drop zones and meal boxes */
        .mealBox {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #fefefe;
            cursor: grab;
            position: relative;
        }
        .mealZone, #savedMealsContainer {
            border: 2px dashed #ffa64d;
            padding: 10px;
            min-height: 100px;
            margin-bottom: 20px;
        }
        .deleteButton, .removeButton {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 12px;
        }
        .deleteButton:hover, .removeButton:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p><a href="logout.php">Logout</a></p>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="features.php">Features</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="myaccount.php">My Account</a></li>
        </ul>
    </nav>
    <section>
        <h2>Your Saved Meals</h2>
        <!-- Saved Meals container (drop zone) -->
        <div id="savedMealsContainer" ondragover="allowDrop(event)" ondrop="drop(event)">
            <?php if (!empty($savedMeals)): ?>
                <?php foreach ($savedMeals as $meal): ?>
                    <div class="mealBox" draggable="true"
                         data-id="<?php echo $meal['id']; ?>"
                         data-protein="<?php echo $meal['totalProtein']; ?>"
                         data-carbs="<?php echo $meal['totalCarbs']; ?>"
                         data-fat="<?php echo $meal['totalFats']; ?>">
                        <h3><?php echo htmlspecialchars($meal['meal_name']); ?></h3>
                        <p><strong>Protein:</strong> <?php echo $meal['totalProtein']; ?> g</p>
                        <p><strong>Carbs:</strong> <?php echo $meal['totalCarbs']; ?> g</p>
                        <p><strong>Fats:</strong> <?php echo $meal['totalFats']; ?> g</p>
                        <p><strong>Calories:</strong> <?php echo $meal['totalCalories']; ?> kcal</p>
                        <p><em>Saved on: <?php echo $meal['created_at']; ?></em></p>
                        <!-- Delete button for meals in the saved section -->
                        <button class="deleteButton" onclick="deleteMeal(this)">Delete</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have not saved any meals yet.</p>
            <?php endif; ?>
        </div>

        <h2>Plan Your Day</h2>
        <!-- Drop zones for meal times -->
        <div class="mealZone" id="breakfastZone" ondragover="allowDrop(event)" ondrop="drop(event)">
            <h3>Breakfast</h3>
        </div>
        <div class="mealZone" id="lunchZone" ondragover="allowDrop(event)" ondrop="drop(event)">
            <h3>Lunch</h3>
        </div>
        <div class="mealZone" id="dinnerZone" ondragover="allowDrop(event)" ondrop="drop(event)">
            <h3>Dinner</h3>
        </div>
        <div class="mealZone" id="snackZone" ondragover="allowDrop(event)" ondrop="drop(event)">
            <h3>Snack</h3>
        </div>

        <h2>Daily Totals</h2>
        <div id="dailyTotals">
            <p><strong>Total Protein:</strong> <span id="dailyProtein">0</span> g</p>
            <p><strong>Total Carbs:</strong> <span id="dailyCarbs">0</span> g</p>
            <p><strong>Total Fats:</strong> <span id="dailyFats">0</span> g</p>
            <p><strong>Total Calories:</strong> <span id="dailyCalories">0</span> kcal</p>
        </div>
    </section>

    <script>
        // ----------------------------
        // Drag and Drop Functions
        // ----------------------------
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            // Set data from the meal box for the drag event
            ev.dataTransfer.setData("mealId", ev.target.dataset.id);
            ev.dataTransfer.setData("protein", ev.target.dataset.protein);
            ev.dataTransfer.setData("carbs", ev.target.dataset.carbs);
            ev.dataTransfer.setData("fat", ev.target.dataset.fat);
            // Extract calories from the second-to-last paragraph (adjust if needed)
            let calText = ev.target.querySelector("p:nth-last-of-type(2)").textContent;
            let calVal = parseFloat(calText.replace("Calories:", "").replace("kcal", "").trim());
            ev.dataTransfer.setData("calories", calVal);
        }

        function drop(ev) {
            ev.preventDefault();
            let mealId = ev.dataTransfer.getData("mealId");
            let protein = parseFloat(ev.dataTransfer.getData("protein")) || 0;
            let carbs = parseFloat(ev.dataTransfer.getData("carbs")) || 0;
            let fat = parseFloat(ev.dataTransfer.getData("fat")) || 0;
            let calories = parseFloat(ev.dataTransfer.getData("calories")) || 0;

            let mealBox = document.querySelector(".mealBox[data-id='" + mealId + "']");
            if (mealBox) {
                // If dropping into a meal zone (not the savedMealsContainer)
                if (ev.target.id !== "savedMealsContainer") {
                    // Remove any existing delete button
                    let delBtn = mealBox.querySelector(".deleteButton");
                    if (delBtn) {
                        delBtn.remove();
                    }
                    // If no remove button exists, add one
                    if (!mealBox.querySelector(".removeButton")) {
                        let removeButton = document.createElement("button");
                        removeButton.textContent = "Remove";
                        removeButton.className = "removeButton";
                        removeButton.style.position = "absolute";
                        removeButton.style.top = "5px";
                        removeButton.style.right = "5px";
                        removeButton.addEventListener("click", function() {
                            // When clicked, return the meal to savedMealsContainer
                            document.getElementById("savedMealsContainer").appendChild(mealBox);
                            // Remove the remove button and add a delete button
                            removeButton.remove();
                            addDeleteButton(mealBox);
                            updateDailyTotals();
                        });
                        mealBox.appendChild(removeButton);
                    }
                } else {
                    // Dropped into the savedMealsContainer: remove any remove button and add a delete button
                    let removeBtn = mealBox.querySelector(".removeButton");
                    if (removeBtn) {
                        removeBtn.remove();
                    }
                    addDeleteButton(mealBox);
                }
                ev.target.appendChild(mealBox);
            }
            updateDailyTotals();
        }

        // Function to add a delete button to a mealBox in the savedMealsContainer
        function addDeleteButton(mealBox) {
            if (!mealBox.querySelector(".deleteButton")) {
                let deleteButton = document.createElement("button");
                deleteButton.textContent = "Delete";
                deleteButton.className = "deleteButton";
                deleteButton.style.position = "absolute";
                deleteButton.style.top = "5px";
                deleteButton.style.right = "5px";
                deleteButton.addEventListener("click", function() {
                    // Delete the meal via AJAX call to deleteMeal.php (you'll need to create that script)
                    let mealId = mealBox.dataset.id;
                    if (confirm("Are you sure you want to delete this meal?")) {
                        fetch("deleteMeal.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ mealId: mealId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                mealBox.remove();
                                updateDailyTotals();
                            } else {
                                alert("Error deleting meal: " + data.error);
                            }
                        })
                        .catch(error => console.error("❌ Error deleting meal:", error));
                    }
                });
                mealBox.appendChild(deleteButton);
            }
        }

        function updateDailyTotals() {
            let totalProtein = 0, totalCarbs = 0, totalFats = 0, totalCalories = 0;
            let mealZones = document.querySelectorAll(".mealZone, #savedMealsContainer");

            mealZones.forEach(zone => {
                let meals = zone.querySelectorAll(".mealBox");
                meals.forEach(meal => {
                    totalProtein += parseFloat(meal.dataset.protein) || 0;
                    totalCarbs += parseFloat(meal.dataset.carbs) || 0;
                    totalFats += parseFloat(meal.dataset.fat) || 0;
                    let calText = meal.querySelector("p:nth-last-of-type(2)").textContent;
                    let calVal = parseFloat(calText.replace("Calories:", "").replace("kcal", "").trim()) || 0;
                    totalCalories += calVal;
                });
            });

            document.getElementById("dailyProtein").textContent = totalProtein.toFixed(2);
            document.getElementById("dailyCarbs").textContent = totalCarbs.toFixed(2);
            document.getElementById("dailyFats").textContent = totalFats.toFixed(2);
            document.getElementById("dailyCalories").textContent = totalCalories.toFixed(2);
        }

        // Attach dragstart event to each mealBox when the page loads
        window.addEventListener("load", function() {
            let mealBoxes = document.querySelectorAll(".mealBox");
            mealBoxes.forEach(box => {
                box.setAttribute("draggable", "true");
                box.addEventListener("dragstart", drag);
            });
        });
    </script>
</body>
</html>
