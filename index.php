<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cafeteria POS — Scenario 1</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- ================= HEADER ================= -->
  <header class="app-header">
    <h1>Cafeteria POS</h1>
    <p class="subtitle">IT 415 Practical Exam — Scenario 1</p>
  </header>

  <!-- ================= MAIN LAYOUT ================= -->
  <main class="layout">

    <!-- LEFT: Product list -->
    <section class="panel" id="products-panel">
      <h2>Menu</h2>
      <div id="product-list" class="product-grid">
        <!-- Products injected by app.js -->
      </div>
    </section>

    <!-- RIGHT: Cart / Order summary -->
    <section class="panel" id="cart-panel">
      <h2>Order Summary</h2>

      <div id="cart-items" class="cart-items">
        <!-- Cart rows injected by app.js -->
        <p class="empty-msg">Cart is empty.</p>
      </div>

      <div class="totals">
        <div class="total-row total-due">
          <span>Total Amount Due</span>
          <span id="total-amount">₱0.00</span>
        </div>
      </div>

      <!-- Payment section -->
      <div class="payment-section">
        <label for="amount-paid">Amount Paid (Cash)</label>
        <input type="number" id="amount-paid" placeholder="0.00" step="0.01" min="0">

        <button id="pay-btn" class="btn btn-primary">Confirm Payment</button>

        <p id="message" class="message"></p>
      </div>
    </section>

    <!-- Receipt (hidden until payment succeeds) -->
    <section class="panel receipt-panel" id="receipt-panel" hidden>
      <h2>Payment Confirmation</h2>
      <div id="receipt-content"></div>
      <button id="new-txn-btn" class="btn btn-secondary">Start New Transaction</button>
    </section>

  </main>

  <script src="assets/js/app.js"></script>
</body>
</html>