<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Contact Us - DesiDietPro</title>
</head>
<body>
    <header>
        <h1>Contact Us</h1>
    </header>

    <!-- Navigation Bar -->
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
        <h2>Contact us</h2>
        <form>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="4" required></textarea><br><br>
            <button type="submit">Send Message</button>
        </form>
    </section>

    <footer>
        <p><small>Website created by Keshav Parikh</small></p>
    </footer>
</body>
</html>