<?php
// dashboard.php
// lets users drag saved dishes into meal zones

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: myaccount.php");
    exit;
}

$conn = new mysqli("localhost", "root", "Era3nile867@", "food_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT id, meal_name, totalProtein, totalCarbs, totalFats, totalCalories, created_at FROM meals WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$savedMeals = $res->fetch_all(MYSQLI_ASSOC);

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
</head>
<body>

<header class="header-flex">
    <h1>Dashboard</h1>
    <div class="header-user">
        <div class="welcome-left">
            <p>Logged in as <?= htmlspecialchars($_SESSION['username']) ?></p>
        </div>
        <div class="logout-right">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="features.php">Features</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
    </ul>
</nav>

<section class="main-section">
    <div class="left-section">
        <h2>Saved Dishes</h2>

        <div id="savedMealsContainer" ondragover="allowDrop(event)" ondrop="drop(event)">
            <?php if (!empty($savedMeals)): ?>
                <?php foreach ($savedMeals as $meal): ?>
                    <div class="mealBox"
                         draggable="true"
                         data-id="<?= $meal['id'] ?>"
                         data-protein="<?= $meal['totalProtein'] ?>"
                         data-carbs="<?= $meal['totalCarbs'] ?>"
                         data-fat="<?= $meal['totalFats'] ?>"
                         data-calories="<?= $meal['totalCalories'] ?>">
                        <h3><?= htmlspecialchars($meal['meal_name']) ?></h3>
                        <p><strong>Protein:</strong> <?= $meal['totalProtein'] ?>g</p>
                        <p><strong>Carbs:</strong> <?= $meal['totalCarbs'] ?>g</p>
                        <p><strong>Fat:</strong> <?= $meal['totalFats'] ?>g</p>
                        <p><strong>Calories:</strong> <?= $meal['totalCalories'] ?> kcal</p>
                        <p><em>Saved on <?= $meal['created_at'] ?></em></p>

                        <!-- 🛠️ Delete button -->
                        <button class="deleteButton" onclick="deleteMeal(this)">Delete</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No dishes saved yet. Go build some!</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="right-section">
        <h2>Plan Your Meals</h2>

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

        <div id="dailyTotals" style="margin-top:20px; font-weight:bold;">
            <p><strong>Total Protein:</strong> <span id="dailyProtein">0</span> g</p>
            <p><strong>Total Carbs:</strong> <span id="dailyCarbs">0</span> g</p>
            <p><strong>Total Fats:</strong> <span id="dailyFats">0</span> g</p>
            <p><strong>Total Calories:</strong> <span id="dailyCalories">0</span> kcal</p>
        </div>
    </div>
</section>

<footer>
    <p><small>Website created by Keshav Parikh</small></p>
</footer>

<script src="script.js"></script>
</body>
</html>
