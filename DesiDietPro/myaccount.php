<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login - DesiDietPro</title>
</head>
<body>
        <header>
        <h1>Login to DesiDietPro</h1>
        </header>
        <nav>
            <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="features.php">Features</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="myaccount.php" class="myaccount-button">My Account</a></li>
            </ul>
        </nav>
        
    <Section>
        <h2>Log in to DesiDietPro!</h2>
        <form action="#">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="#">Sign up here</a></p>
    </Section>
</body>
</html>