<!DOCTYPE html>
<html lang="en">
<head>
    <!--
    Keshav Parikh
    HTML page created 
    DesiDietPro
    20/11/2024
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>DesiDietPro</title>
</head>
<body>
    <header>
        <h1>DesiDietPro</h1>
        <p>This application is useful for you to help you understand the benefits of indian meals and how to balance it!</p>
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
            <h1>Search for Indian Dishes</h1>
    <form action="search.php" method="GET">
        <input type="text" name="dish" placeholder="Enter dish name" required>
        <button type="submit">Search</button>
    </form>
    <div id="results">
        <!-- Nutritional data will be displayed here -->
          <!-- Section to display selected dishes -->
    <div id="selectedDishes">
      <h2>Selected Dishes</h2>
      <ul id="selectedList"></ul>
    </div> 
        </section>

   
    <footer>
    <p><small>Website created by Keshav Parikh</small></p>
    </footer>
</body>
</html>