<?php
// deleteMeal.php
// lets user delete a saved dish

session_start();
header("Content-Type: application/json");

// make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

// grab the meal id from request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['mealId'])) {
    echo json_encode(["error" => "No meal ID given"]);
    exit;
}

// connect to db
$conn = new mysqli("localhost", "root", "Era3nile867@", "food_db");

if ($conn->connect_error) {
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

// first delete any related meal items (if meal_items table exists)
// note: not handling foreign key constraints or transactions here (simple version)

$stmt = $conn->prepare("DELETE FROM meal_items WHERE meal_id = ?");
$stmt->bind_param("i", $data['mealId']);
$stmt->execute();
$stmt->close();

// then delete the actual meal
$stmt2 = $conn->prepare("
    DELETE FROM meals
    WHERE id = ? AND user_id = ?
");
$stmt2->bind_param("ii", $data['mealId'], $_SESSION['user_id']);
$stmt2->execute();

if ($stmt2->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Meal not found or not owned by user"]);
}

$stmt2->close();
$conn->close();
?>
