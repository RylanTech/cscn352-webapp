<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$toppings = $data['toppings'] ?? [];
$creditCard = trim($data['creditCard'] ?? '');
$address = trim($data['address'] ?? '');

if (empty($toppings) || empty($creditCard) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Generate random order ID
$orderId = 'ORD-' . strtoupper(bin2hex(random_bytes(4)));

try {
    $pdo = getDB();
    $stmt = $pdo->prepare(
        "INSERT INTO orders (order_id, toppings, credit_card, address) 
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        $orderId,
        json_encode($toppings),
        $creditCard,
        $address
    ]);

    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'message' => 'Order placed successfully!'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>