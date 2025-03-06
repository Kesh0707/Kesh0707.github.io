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
        <p>This application helps you understand the benefits of Indian meals and how to balance them!</p>
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
        <h1>Search for Ingredients or Dishes</h1>

        <!-- Updated Search Form with Drop-down Suggestions -->
        <label for="searchInput">Search by Category or Description:</label>
        <input type="text" id="searchInput" onkeyup="fetchSearchResults()" list="searchResults">
        
        <!-- Drop-down List for Search Suggestions -->
        <datalist id="searchResults"></datalist>

        <!-- Display Nutritional Data -->
        <h2>Food Details:</h2>
        <div id="foodDetails"></div>

        <!-- Section to Display Selected Dishes -->
        <div id="selectedDishes">
            <h2>Selected Dishes</h2>
            <ul id="selectedList"></ul>
        </div> 
    </section>

    <footer>
        <p><small>Website created by Keshav Parikh</small></p>
    </footer>

    <script src="script.js"></script> <!-- Link to JavaScript -->
</body>
</html>