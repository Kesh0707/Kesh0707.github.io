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
if (isset($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    
    error_log("🔍 User searched for: " . $_GET['search']);

    // First, check if the search matches a DESCRIPTION (i.e., full food name)
    $sql = $conn->prepare("
        SELECT category, description, carbohydrate, protein, fat_total, fiber, sugar_total, cholesterol, calcium, iron, potassium
        FROM general_foods 
        WHERE description LIKE ?
        LIMIT 1
    ");
    
    $sql->bind_param("s", $search);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        error_log("Found detailed food info: " . json_encode($data));
        echo json_encode($data);
    } else {
        // If no description matches, try searching by CATEGORY to return a list of descriptions
        $sql = $conn->prepare("
            SELECT DISTINCT description FROM general_foods WHERE category LIKE ?
        ");
        
        $sql->bind_param("s", $search);
        $sql->execute();
        $result = $sql->get_result();

        $descriptions = [];
        while ($row = $result->fetch_assoc()) {
            $descriptions[] = $row['description'];
        }

        if (count($descriptions) > 0) {
            error_log("Found descriptions for category: " . json_encode($descriptions));
            echo json_encode(["descriptions" => $descriptions]);
        } else {
            error_log("No results found for: " . $_GET['search']);
            echo json_encode(["error" => "No results found"]);
        }
    }

    $sql->close();


}

$conn->close();
?> 

