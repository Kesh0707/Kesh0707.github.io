<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiDietPro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header-flex">
        <h1>DesiDietPro</h1>
        <div class="header-user">
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="welcome-left">
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                </div>
                <div class="logout-right">
                    <a href="logout.php">Logout</a>
                </div>
            <?php else: ?>
                <div class="welcome-left">
                    <p>Welcome, Guest!</p>
                </div>
                <div class="logout-right">
                    <a href="myaccount.php">Login</a> | <a href="register.php">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="features.php">Features</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="myaccount.php" class="myaccount-button">My Account</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="main-section">
        <!-- Left Section: Meal Builder + Save Meal + Add Food -->
        <div class="left-section">
            <h2>Meal Builder</h2>
            <table id="mealTable" border="1">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Calories</th>
                        <th>Macros</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Items dynamically inserted -->
                </tbody>
            </table>

            <div id="mealTotals" style="margin-top:20px; font-weight:bold;">
                <p><strong>Total Calories:</strong> <span id="totalCals">0</span> kcal</p>
                <p><strong>Total Protein:</strong> <span id="totalProtein">0</span> g</p>
                <p><strong>Total Carbs:</strong> <span id="totalCarbs">0</span> g</p>
                <p><strong>Total Fats:</strong> <span id="totalFats">0</span> g</p>
            </div>

            <hr style="margin: 30px 0;">

            <h2>Save Your Meal</h2>
                <form id="saveMealForm" onsubmit="event.preventDefault(); saveMeal();">
                    <label for="mealName">Meal Name:</label>
                    <input type="text" id="mealName" name="mealName" placeholder="e.g., Healthy Lunch" required>
                    <button id="saveMealBtn" type="submit">Save Meal</button>
                </form>


            <hr style="margin: 30px 0;">

            <h2>Add Your Own Food</h2>
            <form id="createFoodForm" action="addFood.php" method="POST">
                <input type="text" name="description" placeholder="Food Name (e.g., Kidney Beans (1 handful))" required>
                <input type="number" name="protein" step="0.01" placeholder="Protein (g)" required>
                <input type="number" name="carbohydrate" step="0.01" placeholder="Carbohydrates (g)" required>
                <input type="number" name="fat_total" step="0.01" placeholder="Fat (g)" required>
                <button type="submit" id="saveMealBtn">Add Food to Database</button>
            </form>
        </div>

        <!-- Right Section: Search Foods -->
        <div class="right-section">
            <h2>Search Foods</h2>
            <input 
                type="text" 
                id="categoryInput" 
                placeholder="Search foods like Dal, Paneer, Milk..." 
                onkeyup="fetchDescription()"
            />
            <ul id="suggestions"></ul>
        </div>

    </section>

    <footer>
        <p><small>Website created by Keshav Parikh</small></p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
