<?php
// search.php
// looks up foods based on user search input

session_start();
header("Content-Type: application/json");

// connect to database
$conn = new mysqli("localhost", "root", "Era3nile867@", "food_db");

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// check if search term was provided
if (!isset($_GET['search'])) {
    echo json_encode(["error" => "Missing search term"]);
    exit;
}

$search = trim($_GET['search']);

// allow short searches like "dal" — just check if totally empty
if ($search === "") {
    echo json_encode(["error" => "Please enter something to search"]);
    exit;
}

// query the general_food table
$stmt = $conn->prepare("
    SELECT description, protein, carbohydrate, fat_total
    FROM general_food
    WHERE description LIKE ?
    LIMIT 10
");

// use wildcards for basic matching
$searchWildcard = "%" . $search . "%";
$stmt->bind_param("s", $searchWildcard);
$stmt->execute();
$res = $stmt->get_result();

// build results
$foods = [];

while ($row = $res->fetch_assoc()) {
    $foods[] = $row;
}

if (count($foods) > 0) {
    echo json_encode(["items" => $foods]);
} else {
    echo json_encode(["error" => "No foods found for '$search'"]);
}

$stmt->close();
$conn->close();
?>
