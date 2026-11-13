<?php
declare(strict_types=1);

$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
]);
session_start();

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $password = getenv('PIZZA_DB_PASSWORD');
    if ($password === false) {
        throw new RuntimeException('Set the PIZZA_DB_PASSWORD environment variable.');
    }

    $host = getenv('PIZZA_DB_HOST') ?: 'localhost';
    $database = getenv('PIZZA_DB_NAME') ?: 'pizza';
    $username = getenv('PIZZA_DB_USER') ?: 'pizza';

    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}
