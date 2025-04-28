<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized: User not logged in."]);
    exit();
}
$user_id = $_SESSION['user_id'];

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database settings
$host = "localhost";
$dbUser = "root";
$dbPassword = "Era3nile867@";
$database = "food_db";

$conn = new mysqli($host, $dbUser, $dbPassword, $database);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Read the raw JSON body
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// Correct check: only if keys exist
if (!is_array($data) || 
    !array_key_exists('mealName', $data) || 
    !array_key_exists('items', $data) || 
    !array_key_exists('totalProtein', $data) || 
    !array_key_exists('totalCarbs', $data) || 
    !array_key_exists('totalFats', $data) || 
    !array_key_exists('totalCalories', $data)) {
    echo json_encode(["error" => "Invalid data format"]);
    exit();
}

// Extract values
$mealName = trim($data['mealName']);
$items = $data['items'];
$totalProtein = floatval($data['totalProtein']);
$totalCarbs = floatval($data['totalCarbs']);
$totalFats = floatval($data['totalFats']);
$totalCalories = floatval($data['totalCalories']);

// Insert into database
$stmt = $conn->prepare("INSERT INTO meals (user_id, meal_name, totalProtein, totalCarbs, totalFats, totalCalories) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit();
}
$stmt->bind_param("isdddd", $user_id, $mealName, $totalProtein, $totalCarbs, $totalFats, $totalCalories);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error saving meal: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
