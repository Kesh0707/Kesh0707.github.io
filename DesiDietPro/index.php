<?php
// index.php
// main page where user builds their dish
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiDietPro - Build Your Dish</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="header-flex">
    <h1>DesiDietPro</h1>
    <div class="header-user">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="welcome-left">
                <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
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
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section class="main-section">
    <div class="left-section">
        <h2>Dish Builder</h2>

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
                <!-- dish items will appear here -->
            </tbody>
        </table>

        <div id="mealTotals" style="margin-top:20px;font-weight:bold;">
            <p><strong>Total Calories:</strong> <span id="totalCals">0</span> kcal</p>
            <p><strong>Total Protein:</strong> <span id="totalProtein">0</span> g</p>
            <p><strong>Total Carbs:</strong> <span id="totalCarbs">0</span> g</p>
            <p><strong>Total Fats:</strong> <span id="totalFats">0</span> g</p>
        </div>

        <hr style="margin:30px 0;">

        <h2>Save Your Dish</h2>

        <form id="saveMealForm" onsubmit="event.preventDefault(); saveMeal();">
            <label for="mealName">Dish Name:</label>
            <input type="text" id="mealName" name="mealName" placeholder="eg. Dal Chawal" required>
            <button id="saveMealBtn" type="submit">Save Dish</button>
        </form>

        <hr style="margin:30px 0;">

        <h2>Add Custom Food</h2>

        <form id="createFoodForm" action="addFood.php" method="POST">
            <input type="text" name="description" placeholder="Food Name" required>
            <input type="number" step="0.01" name="protein" placeholder="Protein (g)" required>
            <input type="number" step="0.01" name="carbohydrate" placeholder="Carbs (g)" required>
            <input type="number" step="0.01" name="fat_total" placeholder="Fat (g)" required>
            <button type="submit">Add Food</button>
        </form>
    </div>

    <div class="right-section">
        <h2>Search Foods</h2>

        <input type="text" id="categoryInput" placeholder="Type like Paneer, Dal, etc..." onkeyup="fetchDescription()">

        <ul id="suggestions">
            <!-- suggestions will pop up here -->
        </ul>

    </div>
</section>

<footer>
    <p><small>Website created by Keshav Parikh</small></p>
</footer>

<script src="script.js"></script>
</body>
</html>
