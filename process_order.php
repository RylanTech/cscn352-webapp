<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
    exit;
}

$allowedToppings = [
    'Pepperoni', 'Mushrooms', 'Onions', 'Sausage', 'Bacon', 'Extra Cheese',
    'Green Peppers', 'Olives', 'Pineapple', 'Jalapeños',
];
$pizzas = $data['pizzas'] ?? null;
$address = is_string($data['address'] ?? null) ? trim($data['address']) : '';

if (!is_array($pizzas) || count($pizzas) < 1 || count($pizzas) > 20 ||
    $address === '' || strlen($address) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide pizzas and a valid delivery address.']);
    exit;
}

$validatedPizzas = [];
foreach ($pizzas as $pizza) {
    if (!is_array($pizza) || !isset($pizza['toppings']) ||
        !is_array($pizza['toppings']) || count($pizza['toppings']) < 1 ||
        count($pizza['toppings']) > count($allowedToppings)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Each pizza must have valid toppings.']);
        exit;
    }

    $selectedToppings = $pizza['toppings'];
    $hasInvalidTopping = array_filter(
        $selectedToppings,
        static fn ($topping): bool => !is_string($topping) ||
            !in_array($topping, $allowedToppings, true)
    );
    if ($hasInvalidTopping ||
        count(array_unique($selectedToppings, SORT_STRING)) !== count($selectedToppings)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'One or more toppings are invalid.']);
        exit;
    }
    $validatedPizzas[] = ['toppings' => array_values($selectedToppings)];
}

try {
    $pdo = getDB();
    $orderId = 'ORD-' . strtoupper(bin2hex(random_bytes(6)));
    $stmt = $pdo->prepare(
        'INSERT INTO orders (order_id, user_id, pizzas, address) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([
        $orderId,
        (int) $_SESSION['user_id'],
        json_encode($validatedPizzas, JSON_THROW_ON_ERROR),
        $address,
    ]);

    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'message' => 'Order placed successfully!'
    ]);
} catch (Throwable $e) {
    error_log('Order submission failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to place the order. Please try again.']);
}
?>