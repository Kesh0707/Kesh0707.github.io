<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "Unauthorized: User not logged in"]));
}
$user_id = $_SESSION['user_id'];

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

$host = "localhost";
$dbUser = "root";
$dbPassword = "Era3nile867@";
$database = "food_db";

$conn = new mysqli($host, $dbUser, $dbPassword, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if (!isset($data['mealName']) || !isset($data['items'])) {
    echo json_encode(["error" => "Invalid data format"]);
    exit();
}

$mealName = trim($data['mealName']);
$items = $data['items'];

// Retrieve nutritional totals from POST data (make sure these are calculated on the front end)
$totalProtein  = isset($data['totalProtein']) ? floatval($data['totalProtein']) : 0;
$totalCarbs    = isset($data['totalCarbs']) ? floatval($data['totalCarbs']) : 0;
$totalFats     = isset($data['totalFats']) ? floatval($data['totalFats']) : 0;
$totalCalories = isset($data['totalCalories']) ? floatval($data['totalCalories']) : 0;

// Insert the meal record including the nutritional totals
$stmt = $conn->prepare("INSERT INTO meals (user_id, meal_name, totalProtein, totalCarbs, totalFats, totalCalories) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit();
}
$stmt->bind_param("isdddd", $user_id, $mealName, $totalProtein, $totalCarbs, $totalFats, $totalCalories);
$stmt->execute();
$mealId = $stmt->insert_id;
$stmt->close();

// Insert each meal item into the meal_items table
$stmtItem = $conn->prepare("INSERT INTO meal_items (meal_id, description) VALUES (?, ?)");
if (!$stmtItem) {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit();
}

foreach ($items as $desc) {
    $stmtItem->bind_param("is", $mealId, $desc);
    $stmtItem->execute();
}

$stmtItem->close();
$conn->close();

echo json_encode(["success" => true]);
?>
