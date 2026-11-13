<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize Your Pizza</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <div>
            <button id="cartBtn" class="cart-btn">🛒 Cart (<span id="cartCount">0</span>)</button>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1>Build Your Pizza</h1>
        <p>Toggle the toppings you want, then add the pizza to your cart.</p>

        <div class="toppings">
            <h2>Toppings</h2>
            <div class="toggle-grid">
                <label class="toggle">
                    <input type="checkbox" value="Pepperoni">
                    <span>Pepperoni</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Mushrooms">
                    <span>Mushrooms</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Onions">
                    <span>Onions</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Sausage">
                    <span>Sausage</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Bacon">
                    <span>Bacon</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Extra Cheese">
                    <span>Extra Cheese</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Green Peppers">
                    <span>Green Peppers</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Olives">
                    <span>Olives</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Pineapple">
                    <span>Pineapple</span>
                </label>
                <label class="toggle">
                    <input type="checkbox" value="Jalapeños">
                    <span>Jalapeños</span>
                </label>
            </div>
        </div>

        <button id="addToCartBtn" class="primary">Add Pizza to Cart</button>
    </div>

    <script src="script.js"></script>
</body>
</html>