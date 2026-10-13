<?php
require_once __DIR__ . '/config/db.php';

$stmt = $pdo->query("
    SELECT
        t.id,
        t.reference_no,
        t.total_amount,
        t.amount_paid,
        t.change_amount,
        t.created_at,
        (SELECT COUNT(*) FROM transaction_items ti WHERE ti.transaction_id = t.id) AS item_count
    FROM transactions t
    ORDER BY t.created_at DESC
    LIMIT 200
");
$transactions = $stmt->fetchAll();

function peso($n) {
    return '₱' . number_format((float) $n, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaction History — Cafeteria POS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <nav class="nav">
    <div class="nav-inner">
      <a href="index.php" class="nav-logo">Cafeteria POS</a>
      <ul class="nav-links">
        <li><a href="index.php">Menu</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="add_product.php">Add Product</a></li>
      </ul>
    </div>
  </nav>

  <section class="hero">
    <h1>Transaction History</h1>
    <p class="hero-tag">All completed sales, newest first.</p>
  </section>

  <main class="layout-wide">
    <section class="panel">

      <?php if (empty($transactions)): ?>
        <p class="history-empty">No transactions yet. Complete a sale on the POS to see it here.</p>
      <?php else: ?>
        <table class="history-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Date &amp; Time</th>
              <th class="num">Items</th>
              <th class="num">Total</th>
              <th class="num">Paid</th>
              <th class="num">Change</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($transactions as $txn): ?>
              <tr>
                <td class="ref"><?= htmlspecialchars($txn['reference_no']) ?></td>
                <td><?= htmlspecialchars(date('M j, Y · g:i A', strtotime($txn['created_at']))) ?></td>
                <td class="num"><?= (int) $txn['item_count'] ?></td>
                <td class="num total"><?= peso($txn['total_amount']) ?></td>
                <td class="num"><?= peso($txn['amount_paid']) ?></td>
                <td class="num"><?= peso($txn['change_amount']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    </section>
  </main>

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <span class="footer-logo">Cafeteria POS</span>
        <span class="footer-tag">Point-of-Sale System</span>
      </div>
      <div class="footer-meta">
        <span>Terminal 01 · Main Counter</span>
        <span>Cashier on duty: J. Acedo</span>
        <span>&copy; 2026 Davao del Norte State College</span>
      </div>
    </div>
  </footer>

</body>
</html>