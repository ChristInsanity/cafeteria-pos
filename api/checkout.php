<?php
// ============================================================
// API: Process checkout / payment
// POST /api/checkout.php
// Body (JSON): { amount_paid: number, items: [{ product_id, quantity }] }
// ============================================================

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

// ---------- Read input ----------
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid request body.']);
    exit;
}

$amountPaid = $payload['amount_paid'] ?? null;
$items      = $payload['items'] ?? null;

// ---------- Validate payment amount ----------
if ($amountPaid === null || !is_numeric($amountPaid) || $amountPaid < 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Please enter a valid payment amount.']);
    exit;
}
$amountPaid = (float) $amountPaid;

// ---------- Validate items ----------
if (!is_array($items) || count($items) === 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Cart is empty.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // ---------- Fetch product details from DB (source of truth) ----------
    $ids = array_map(fn($i) => (int) $i['product_id'], $items);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders) AND is_active = 1");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();

    $productMap = [];
    foreach ($rows as $r) {
        $productMap[(int) $r['id']] = [
            'id'    => (int) $r['id'],
            'name'  => $r['name'],
            'price' => (float) $r['price'],
        ];
    }

    // ---------- Build line items + compute total ----------
    $lineItems = [];
    $total     = 0.0;

    foreach ($items as $i) {
        $pid = (int) ($i['product_id'] ?? 0);
        $qty = (int) ($i['quantity']   ?? 0);

        if (!isset($productMap[$pid]) || $qty <= 0) {
            throw new RuntimeException('Invalid product or quantity.');
        }

        $p        = $productMap[$pid];
        $subtotal = $p['price'] * $qty;
        $total   += $subtotal;

        $lineItems[] = [
            'product_id'   => $p['id'],
            'product_name' => $p['name'],
            'unit_price'   => $p['price'],
            'quantity'     => $qty,
            'subtotal'     => $subtotal,
        ];
    }

    // ---------- Validate payment >= total ----------
    if ($amountPaid < $total) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode([
            'ok'    => false,
            'error' => 'Insufficient payment. Please enter at least ' . number_format($total, 2) . '.',
        ]);
        exit;
    }

    $change = $amountPaid - $total;

    // ---------- Generate reference number ----------
    $referenceNo = 'TXN-' . date('Ymd-His') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

    // ---------- Insert transaction ----------
    $stmt = $pdo->prepare(
        "INSERT INTO transactions (reference_no, total_amount, amount_paid, change_amount)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$referenceNo, $total, $amountPaid, $change]);
    $txnId = (int) $pdo->lastInsertId();

    // ---------- Insert line items ----------
    $stmt = $pdo->prepare(
        "INSERT INTO transaction_items
         (transaction_id, product_id, product_name, unit_price, quantity, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    foreach ($lineItems as $li) {
        $stmt->execute([
            $txnId,
            $li['product_id'],
            $li['product_name'],
            $li['unit_price'],
            $li['quantity'],
            $li['subtotal'],
        ]);
    }

    $pdo->commit();

    // ---------- Respond ----------
    echo json_encode([
        'ok' => true,
        'receipt' => [
            'reference_no'  => $referenceNo,
            'created_at'    => date('Y-m-d H:i:s'),
            'items'         => $lineItems,
            'total_amount'  => $total,
            'amount_paid'   => $amountPaid,
            'change_amount' => $change,
        ],
    ]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Checkout failed: ' . $e->getMessage()]);
}