<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "Unauthorized"]));
}
$user_id = $_SESSION['user_id'];

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if (!isset($data['mealId'])) {
    die(json_encode(["error" => "No meal ID provided"]));
}

$mealId = intval($data['mealId']);

$host = "localhost";
$dbUser = "root";
$dbPassword = "Era3nile867@";
$database = "food_db";

$conn = new mysqli($host, $dbUser, $dbPassword, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// First, delete any associated meal_items for this meal.
// This prevents foreign key constraint issues if ON DELETE CASCADE isn't set.
$stmtItems = $conn->prepare("DELETE FROM meal_items WHERE meal_id = ?");
$stmtItems->bind_param("i", $mealId);
$stmtItems->execute();
$stmtItems->close();

// Now, delete the meal record only if it belongs to the current user.
$stmt = $conn->prepare("DELETE FROM meals WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $mealId, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to delete meal. It may not belong to you."]);
}

$stmt->close();
$conn->close();
?>
