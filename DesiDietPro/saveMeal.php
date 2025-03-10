<?php
// saveMeal.php
// PURPOSE: Save the user's custom meal (meal name + items) to the database.

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

$mealName = $data['mealName'];
$items = $data['items'];

// 1) Insert the meal name into a "meals" table
$sqlMeal = $conn->prepare("INSERT INTO meals (meal_name) VALUES (?)");
if (!$sqlMeal) {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit();
}
$sqlMeal->bind_param("s", $mealName);
$sqlMeal->execute();
$mealId = $sqlMeal->insert_id;
$sqlMeal->close();

// 2) Insert each item (description) into a "meal_items" table
$sqlItem = $conn->prepare("INSERT INTO meal_items (meal_id, description) VALUES (?, ?)");
if (!$sqlItem) {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit();
}

foreach ($items as $desc) {
    $sqlItem->bind_param("is", $mealId, $desc);
    $sqlItem->execute();
}

$sqlItem->close();
$conn->close();

// If everything worked, return success
echo json_encode(["success" => true]);
?>
