<?php
// ============================================================
// API: Return active products as JSON
// GET /api/products.php
// ============================================================

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT id, name, price FROM products WHERE is_active = 1 ORDER BY id");
    $products = $stmt->fetchAll();

    // Cast price to float so JSON sends numbers, not strings
    foreach ($products as &$p) {
        $p['id']    = (int) $p['id'];
        $p['price'] = (float) $p['price'];
    }
    unset($p);

    echo json_encode([
        'ok'       => true,
        'products' => $products,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'Failed to load products.',
    ]);
}