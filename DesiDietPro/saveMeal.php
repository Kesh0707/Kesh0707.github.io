<?php
// saveMeal.php
// PURPOSE: Save the user's custom meal (meal name + items) to the database, associated with their user account.

// Start session to access user data
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "Unauthorized: User not logged in"]));
}
$user_id = $_SESSION['user_id'];

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$password = "Era3nile867@";
$database = "food_db";

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Read the POST data (JSON)
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// Validate the data
if (!isset($data['mealName']) || !isset($data['items'])) {
    echo json_encode(["error" => "Invalid data format"]);
    exit();
}

$mealName = trim($data['mealName']);
$items = $data['items'];

// 1) Insert the meal name along with the user's ID into the "meals" table
$stmt = $conn->prepare("INSERT INTO meals (user_id, meal_name) VALUES (?, ?)");
if (!$stmt) {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit();
}
$stmt->bind_param("is", $user_id, $mealName);
$stmt->execute();
$mealId = $stmt->insert_id;
$stmt->close();

// 2) Insert each item (description) into the "meal_items" table
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

// If everything worked, return success
echo json_encode(["success" => true]);
?>
