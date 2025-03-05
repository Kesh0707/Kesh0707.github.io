<?php
$host = "localhost";
$user = "root"; 
$password = "Era3nile867@"; 
$database = "food_db";

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Get search query
if (isset($_GET['q'])) {
    $query = "%" . $_GET['q'] . "%";
    $sql = $conn->prepare("SELECT * FROM foods WHERE name LIKE ?");
    $sql->bind_param("s", $query);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "No results found"]);
    }

    $sql->close();
}

$conn->close();
?>