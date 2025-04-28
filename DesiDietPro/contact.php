<?php
// contact.php
// basic contact page for users to send a message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - DesiDietPro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="header-flex">
    <h1>Contact Us</h1>
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

<section class="contact-section">
    <h2>Send us a message</h2>

    <!-- basic form - doesn't actually send email yet -->
    <form method="POST" action="#">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" required></textarea>

        <button type="submit">Send Message</button>
    </form>

    <!-- TODO: hook this up to a real email handler later -->
</section>

<footer>
    <p><small>Website created by Keshav Parikh</small></p>
</footer>

</body>
</html>
