<?php
// addFood.php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = "localhost";
$user = "root";
$password = "Era3nile867@";
$database = "food_db";

// Create database connection
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description']);
    $protein = floatval($_POST['protein']);
    $carbs = floatval($_POST['carbohydrate']);
    $fat = floatval($_POST['fat_total']);

    // Calculate basic calories
    $calories = ($protein * 4) + ($carbs * 4) + ($fat * 9);

    // Insert the new food into general_food table
    $stmt = $conn->prepare("INSERT INTO general_food (
        Category, Description, `Nutrient Data Bank Number`, carbohydrate, protein, fat_total, fiber, sugar_total
    ) VALUES (?, ?, NULL, ?, ?, ?, 0, 0)");

    if ($stmt) {
        $category = "User Entry"; // Category marked as User Entry
        $stmt->bind_param("ssddd", $category, $description, $carbs, $protein, $fat);

        if ($stmt->execute()) {
            echo "<p>✅ Food item added successfully!</p>";
            echo "<p><a href='index.php'>Back to Home</a></p>";
        } else {
            echo "<p>❌ Error inserting food item: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>❌ SQL Error: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Invalid request method.</p>";
}

$conn->close();
?>
