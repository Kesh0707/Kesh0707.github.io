<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--
      Keshav Parikh
      DesiDietPro
      Final Example
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiDietPro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>DesiDietPro</h1>
        <?php if(isset($_SESSION['user_id'])): ?>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! <a href="logout.php">Logout</a></p>
        <?php else: ?>
            <p>Welcome, Guest! <a href="myaccount.php">Login</a> or <a href="register.php">Register</a></p>
        <?php endif; ?>
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
    <section>
        <h2>Search by Category</h2>
        <!-- User types the category here (e.g. "milk") -->
        <input 
            type="text" 
            id="categoryInput" 
            placeholder="Type a category (e.g. Milk, Rice, Lentils...)" 
            onkeyup="fetchCategory()"
        />

        <!-- Unordered list to display the matching items in that category -->
        <ul id="suggestions"></ul>

        <h2>Create Your Meal</h2>
        <!-- Table that lists the items the user has added to their meal -->
        <table id="mealTable" border="1">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount (g/ml)</th>
                    <th>Calories</th>
                    <th>Macros</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Items will be dynamically inserted here -->
            </tbody>
        </table>

        <div id="mealTotals" style="margin-top:10px; font-weight:bold;">
            <p><strong>Total Calories:</strong> <span id="totalCals">0</span> kcal</p>
            <p><strong>Total Protein:</strong> <span id="totalProtein">0</span> g</p>
            <p><strong>Total Carbs:</strong> <span id="totalCarbs">0</span> g</p>
            <p><strong>Total Fats:</strong> <span id="totalFats">0</span> g</p>
        </div>

        <!-- User can name the meal, then click "Save Meal" -->
        <label for="mealName">Meal Name:</label>
        <input type="text" id="mealName" placeholder="e.g., My Healthy Meals">
        <button id="saveMealBtn" onclick="saveMeal()">Save Meal</button>
    </section>

    <footer>
        <p><small>Website created by Keshav Parikh</small></p>
    </footer>

    <!-- Link to external JavaScript file -->
    <script src="script.js"></script>
</body>
</html>
