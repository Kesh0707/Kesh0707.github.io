<?php
// search.php
// PURPOSE: Return a list of descriptions that match the typed category (like "milk")

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Database credentials
$host = "localhost";
$user = "root";
$password = "Era3nile867@";
$database = "food_db";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Check if "search" param is provided
if (!isset($_GET['search']) || empty(trim($_GET['search']))) {
    die(json_encode(["error" => "No search term provided"]));
}

// The user typed something like "milk"
$searchTerm = trim($_GET['search']);
$search = "%" . $searchTerm . "%";

// Query for items whose category matches the typed term
$sql = $conn->prepare("
    SELECT DISTINCT description 
    FROM general_food 
    WHERE category LIKE ?
    ORDER BY description
");
if (!$sql) {
    die(json_encode(["error" => "SQL error: " . $conn->error]));
}

$sql->bind_param("s", $search);
$sql->execute();
$result = $sql->get_result();

$descriptions = [];
while ($row = $result->fetch_assoc()) {
    $descriptions[] = $row['description'];
}

if (count($descriptions) > 0) {
    echo json_encode(["descriptions" => $descriptions]);
} else {
    echo json_encode(["error" => "No matching items found."]);
}

$sql->close();
$conn->close();
?>