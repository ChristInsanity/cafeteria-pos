<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cafeteria POS — Scenario 1</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- ================= NAV ================= -->
  <nav class="nav">
    <div class="nav-inner">
      <a href="/cafeteria-pos/" class="nav-logo">Cafeteria POS</a>
      <ul class="nav-links">
        <li><a href="#products-panel">Menu</a></li>
        <li><a href="#cart-panel">Order</a></li>
        <li><a href="#receipt-panel">Receipt</a></li>
        <li><a href="history.php">History</a></li>
      </ul>
    </div>
  </nav>

  <!-- ================= HERO ================= -->
  <section class="hero">
    <h1>Cafeteria POS</h1>
    <p class="hero-tag">Fast. Simple. Ready for lunch.</p>
  </section>

  <!-- ================= MAIN ================= -->
  <main class="layout">

    <!-- LEFT: Product list -->
    <section class="panel" id="products-panel">
      <div class="panel-head">
        <h2>Menu</h2>
        <a href="add_product.php" class="panel-link">+ Add product</a>
      </div>
      <div id="product-list" class="product-grid">
        <!-- Products injected by app.js -->
      </div>
    </section>

    <!-- RIGHT: Cart -->
    <section class="panel" id="cart-panel">
      <div class="panel-head">
        <h2>Order Summary</h2>
      </div>

      <div id="cart-items" class="cart-items">
        <p class="empty-msg">Cart is empty.</p>
      </div>

      <div class="totals">
        <div class="total-row total-due">
          <span>Total Amount Due</span>
          <span id="total-amount">₱0.00</span>
        </div>
      </div>

      <div class="payment-section">
        <label for="amount-paid">Amount Paid (Cash)</label>
        <input type="number" id="amount-paid" placeholder="0.00" step="0.01" min="0">

        <button id="pay-btn" class="btn btn-primary">Confirm Payment</button>

        <p id="message" class="message"></p>
      </div>
    </section>

    <!-- Receipt (hidden until payment succeeds) -->
    <section class="panel receipt-panel" id="receipt-panel" hidden>
      <div class="panel-head">
        <h2>Payment Confirmation</h2>
      </div>
      <div id="receipt-content"></div>
      <button id="new-txn-btn" class="btn btn-secondary">Start New Transaction</button>
    </section>

  </main>

  <!-- ================= FOOTER ================= -->
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

  <script src="assets/js/app.js"></script>
</body>
</html>