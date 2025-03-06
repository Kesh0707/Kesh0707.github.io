<?php
$host = "localhost";
$user = "root"; 
$password = "Era3nile867@"; 
$database = "food_db";

// Connection to MySQL
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Get search query
if (isset($_GET['q'])) {
    $query = "%" . $_GET['q'] . "%";
    error_log("🔍 Search Query: " . $_GET['q']); 
   
    $sql = $conn->prepare("
    SELECT 
        description, carbohydrate, protein, fat_total, fiber, sugar_total, cholesterol, calcium, iron, potassium
    FROM general_foods 
    WHERE description LIKE ?
");
    
    // debugging
if (!$sql) {
    die(json_encode(["error" => "SQL preparation failed: " . $conn->error]));
}

$sql->bind_param("s", $query);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    error_log("✅ Search Result: " . json_encode($data));
    echo json_encode($data);
} else {
    error_log("❌ No results found for: " . $_GET['q']);
    echo json_encode(["error" => "No results found"]);
}

$sql->close();
}

$conn->close();
?>