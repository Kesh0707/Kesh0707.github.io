<?php
// login.php: Handles user login and session creation

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables to avoid undefined variable warnings
$loginSuccess = false;
$errorMsg = "";

// Process form submission if POST method is used
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection settings
    $host = "localhost";
    $dbUser = "root";
    $dbPassword = "Era3nile867@";
    $database = "food_db";

    $conn = new mysqli($host, $dbUser, $dbPassword, $database);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Sanitize inputs
    $username = trim($_POST['username']);
    $passwordPlain = trim($_POST['password']);

    // Prepare SQL to fetch user data by username
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user exists
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // Verify the password
            if (password_verify($passwordPlain, $row['password_hash'])) {
                // Login successful: store user info in session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $username;
                $loginSuccess = true;
                // Redirect to a dashboard or My Account page
                header("Location: dashboard.php");
                exit();
            } else {
                $errorMsg = "Incorrect password!";
            }
        } else {
            $errorMsg = "User not found!";
        }
        $stmt->close();
    } else {
        $errorMsg = "SQL error: " . $conn->error;
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DesiDietPro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Login to DesiDietPro</h1>
    </header>
    <!--Navigation Bar-->
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="features.php">Features</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
    </nav>
    <section>
        <?php if ($loginSuccess): ?>
            <p>Login successful! Redirecting...</p>
        <?php else: ?>
            <?php if (!empty($errorMsg)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($errorMsg); ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        <?php endif; ?>
    </section>
</body>
</html>
