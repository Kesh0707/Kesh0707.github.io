<?php
// register.php: Handles user registration

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables to avoid undefined variable warnings.
$registrationSuccess = false;
$errorMsg = "";

$host = "localhost";
$user = "root";
$password = "Era3nile867@";
$database = "food_db";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $passwordPlain = trim($_POST['password']);

    // Hash the password
    $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $passwordHash);
    
    if ($stmt->execute()) {
        $registrationSuccess = true;
    } else {
        $errorMsg = "Registration error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DesiDietPro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Register for DesiDietPro</h1>
    </header>
    <!--Navigation Bar-->
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="features.php">Features</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="myaccount.php" class="myaccount-button">My Account</a></li>
        </ul>
    </nav>
    <section>
        <?php if ($registrationSuccess): ?>
            <p>Registration successful! You can now <a href="login.php">log in</a>.</p>
        <?php else: ?>
            <?php if (!empty($errorMsg)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($errorMsg); ?></p>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <br>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <br>
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Log in here</a>.</p>
        <?php endif; ?>
    </section>
</body>
</html>
