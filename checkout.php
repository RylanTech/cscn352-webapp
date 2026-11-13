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
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <div>
            <a href="pizza.php">← Back to Pizza Builder</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1>Checkout</h1>

        <div id="cartSummary">
            <h2>Your Cart</h2>
            <ul id="cartItems"></ul>
            <p id="emptyCart" class="hint" style="display:none;">Your cart is empty.</p>
        </div>

        <form id="checkoutForm">
            <h2>Payment & Delivery (Fake Data)</h2>

            <label for="creditCard">Fake Credit Card Number</label>
            <input type="text" id="creditCard" name="creditCard" 
                   placeholder="1234-5678-9012-3456" required maxlength="19">

            <label for="address">Fake Delivery Address</label>
            <textarea id="address" name="address" rows="3" 
                      placeholder="123 Fake Street, Example City, ST 12345" required></textarea>

            <button type="submit" class="primary">Submit Order</button>
        </form>

        <div id="successMessage" class="success" style="display:none;"></div>
    </div>

    <script src="script.js"></script>
</body>
</html>