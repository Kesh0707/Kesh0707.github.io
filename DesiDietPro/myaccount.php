<?php
// myaccount.php
// login page - checks user credentials

session_start();
$errorMsg = "";

// handle login when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "Era3nile867@", "food_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); // basic error
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // simple query to get user info
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $errorMsg = "Wrong password.";
        }
    } else {
        $errorMsg = "Username not found.";
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
    <title>Login - DesiDietPro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="header-flex">
    <h1>Login</h1>
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
    <h2>Login to Your Account</h2>

    <?php if (!empty($errorMsg)): ?>
        <p style="color: red;"><?= htmlspecialchars($errorMsg) ?></p>
    <?php endif; ?>

    <form method="POST" action="myaccount.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</section>

<footer>
    <p><small>Website created by Keshav Parikh</small></p>
</footer>

</body>
</html>
