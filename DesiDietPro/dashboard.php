<?php
// dashboard.php: Display meals saved by the logged-in user
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$host = "localhost";
$user = "root";
$password = "Era3nile867@";
$database = "food_db";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT id, meal_name, created_at FROM meals WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$savedMeals = [];
while ($row = $result->fetch_assoc()) {
    $savedMeals[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - DesiDietPro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
        <h1>DesiDietPro</h1>
        <p>This application helps you understand the benefits of Indian meals and how to balance them!</p>
    </header>
    
        <!--Navigation Bar-->
        <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="features.php">Features</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="myaccount.php" class="myaccount-button">My Account</a></li>
        </ul>
    </nav>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <h2>Your Saved Meals</h2>
    <?php if (!empty($savedMeals)): ?>
        <ul>
            <?php foreach ($savedMeals as $meal): ?>
                <li>
                    <strong><?php echo htmlspecialchars($meal['meal_name']); ?></strong>
                    (Saved on <?php echo $meal['created_at']; ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You haven't saved any meals yet.</p>
    <?php endif; ?>
</body>
</html>
