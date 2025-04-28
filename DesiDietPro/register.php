<?php
// register.php
// page for users to create an account

session_start();
$success = false;
$errorMsg = "";

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "Era3nile867@", "food_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); // basic connection fail
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // insert into users table
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, password_hash)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sss", $username, $email, $passwordHash);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $errorMsg = "Registration failed. Maybe username/email already exists.";
    }

    $stmt->close();
    $conn->close();
}
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

<header class="header-flex">
    <h1>Register</h1>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="features.php">Features</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
    </ul>
</nav>

<section class="login-section">
    <h2>Create Your Account</h2>

    <?php if ($success): ?>
        <p style="color: green;">Registration successful! <a href="myaccount.php">Login here</a>.</p>
    <?php elseif (!empty($errorMsg)): ?>
        <p style="color: red;"><?= htmlspecialchars($errorMsg) ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="myaccount.php">Login here</a></p>
</section>

<footer>
    <p><small>Website created by Keshav Parikh</small></p>
</footer>

</body>
</html>
