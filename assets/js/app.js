// ============================================================
// Cafeteria POS — Front-end logic
// IT 415 Practical Exam — Scenario 1
// ============================================================

// ---------- State ----------
let products = [];          // loaded from API
let cart = {};              // { productId: { id, name, price, qty } }
let lastReceipt = null;     // stores last completed transaction

// ---------- DOM refs ----------
const $productList  = document.getElementById('product-list');
const $cartItems    = document.getElementById('cart-items');
const $totalAmount  = document.getElementById('total-amount');
const $amountPaid   = document.getElementById('amount-paid');
const $payBtn       = document.getElementById('pay-btn');
const $message      = document.getElementById('message');
const $receiptPanel = document.getElementById('receipt-panel');
const $receiptBody  = document.getElementById('receipt-content');
const $newTxnBtn    = document.getElementById('new-txn-btn');

// ---------- Helpers ----------
const peso = (n) => '₱' + Number(n).toFixed(2);

function setMessage(text, type = '') {
  $message.textContent = text;
  $message.className = 'message ' + type;
}

function clearMessage() {
  $message.textContent = '';
  $message.className = 'message';
}

// ---------- Load products ----------
async function loadProducts() {
  try {
    const res = await fetch('api/products.php');
    const data = await res.json();

    if (!data.ok) throw new Error('API error');
    products = data.products;

    renderProducts();
  } catch (err) {
    $productList.innerHTML = '<p class="empty-msg">Failed to load products.</p>';
    console.error(err);
  }
}

function renderProducts() {
  $productList.innerHTML = '';
  products.forEach(p => {
    const btn = document.createElement('button');
    btn.className = 'product-card';
    btn.innerHTML = `
      <div class="p-name">${p.name}</div>
      <div class="p-price">${peso(p.price)}</div>
    `;
    btn.addEventListener('click', () => addToCart(p.id));
    $productList.appendChild(btn);
  });
}

// ---------- Cart operations ----------
function addToCart(productId) {
  const product = products.find(p => p.id === productId);
  if (!product) return;

  if (cart[productId]) {
    cart[productId].qty += 1;
  } else {
    cart[productId] = {
      id:    product.id,
      name:  product.name,
      price: product.price,
      qty:   1,
    };
  }

  clearMessage();
  renderCart();
}

function increaseQty(productId) {
  if (!cart[productId]) return;
  cart[productId].qty += 1;
  renderCart();
}

function decreaseQty(productId) {
  if (!cart[productId]) return;
  cart[productId].qty -= 1;
  if (cart[productId].qty <= 0) delete cart[productId];
  renderCart();
}

function removeItem(productId) {
  delete cart[productId];
  renderCart();
}

// ---------- Totals ----------
function getTotal() {
  return Object.values(cart).reduce((sum, i) => sum + i.price * i.qty, 0);
}

// ---------- Render cart ----------
function renderCart() {
  const items = Object.values(cart);

  if (items.length === 0) {
    $cartItems.innerHTML = '<p class="empty-msg">Cart is empty.</p>';
    $totalAmount.textContent = peso(0);
    return;
  }

  $cartItems.innerHTML = '';
  items.forEach(item => {
    const row = document.createElement('div');
    row.className = 'cart-row';
    row.innerHTML = `
      <div class="c-info">
        <div class="c-name">${item.name}</div>
        <div class="c-sub">${peso(item.price)} × ${item.qty} = ${peso(item.price * item.qty)}</div>
      </div>
      <div class="qty-controls">
        <button class="qty-btn" data-action="dec" data-id="${item.id}">−</button>
        <span class="qty-value">${item.qty}</span>
        <button class="qty-btn" data-action="inc" data-id="${item.id}">+</button>
      </div>
      <button class="remove-btn" data-action="rm" data-id="${item.id}">Remove</button>
    `;
    $cartItems.appendChild(row);
  });

  // Wire up buttons
  $cartItems.querySelectorAll('[data-action]').forEach(btn => {
    const id = Number(btn.dataset.id);
    const action = btn.dataset.action;
    btn.addEventListener('click', () => {
      if (action === 'inc') increaseQty(id);
      else if (action === 'dec') decreaseQty(id);
      else if (action === 'rm')  removeItem(id);
    });
  });

  $totalAmount.textContent = peso(getTotal());
}

// ---------- Payment validation ----------
function validatePayment(raw) {
  if (raw === '' || raw === null || raw === undefined) {
    return { ok: false, msg: 'Please enter a valid payment amount.' };
  }
  const paid = Number(raw);
  if (isNaN(paid) || paid < 0) {
    return { ok: false, msg: 'Please enter a valid payment amount.' };
  }
  const total = getTotal();
  if (paid < total) {
    return { ok: false, msg: `Insufficient payment. Please enter at least ${total.toFixed(2)}.` };
  }
  return { ok: true, paid, total, change: paid - total };
}

// ---------- Confirm payment ----------
async function confirmPayment() {
  clearMessage();

  if (Object.keys(cart).length === 0) {
    setMessage('Cart is empty. Add products first.', 'error');
    return;
  }

  const result = validatePayment($amountPaid.value);
  if (!result.ok) {
    setMessage(result.msg, 'error');
    return;
  }

  // Build payload
  const items = Object.values(cart).map(i => ({
    product_id: i.id,
    quantity:   i.qty,
  }));

  try {
    const res = await fetch('api/checkout.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        amount_paid: result.paid,
        items:       items,
      }),
    });
    const data = await res.json();

    if (!data.ok) {
      setMessage(data.error || 'Payment failed.', 'error');
      return;
    }

    // Save receipt for display + new transaction
    lastReceipt = data.receipt;
    showReceipt(lastReceipt);
  } catch (err) {
    console.error(err);
    setMessage('Server error. Please try again.', 'error');
  }
}

// ---------- Receipt rendering ----------
function showReceipt(receipt) {
  const rows = receipt.items.map(it => `
    <tr>
      <td>${it.product_name}</td>
      <td class="num">${it.quantity}</td>
      <td class="num">${peso(it.unit_price)}</td>
      <td class="num">${peso(it.subtotal)}</td>
    </tr>
  `).join('');

  $receiptBody.innerHTML = `
    <div class="receipt-box">
      <div class="r-title">Cafeteria POS</div>
      <div class="r-ref">
        Transaction #${receipt.reference_no} · ${receipt.created_at}
      </div>

      <table class="receipt-table">
        <thead>
          <tr>
            <th>Item</th>
            <th class="num">Qty</th>
            <th class="num">Price</th>
            <th class="num">Subtotal</th>
          </tr>
        </thead>
        <tbody>${rows}</tbody>
      </table>

      <div class="receipt-totals">
        <div class="row grand">
          <span>Total</span><span>${peso(receipt.total_amount)}</span>
        </div>
        <div class="row">
          <span>Amount Paid</span><span>${peso(receipt.amount_paid)}</span>
        </div>
        <div class="row">
          <span>Change</span><span>${peso(receipt.change_amount)}</span>
        </div>
      </div>
    </div>
  `;

  $receiptPanel.hidden = false;
  $receiptPanel.scrollIntoView({ behavior: 'smooth' });
}

// ---------- New transaction ----------
function startNewTransaction() {
  cart = {};
  lastReceipt = null;
  $amountPaid.value = '';
  $receiptPanel.hidden = true;
  $receiptBody.innerHTML = '';
  clearMessage();
  renderCart();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ---------- Wire up ----------
$payBtn.addEventListener('click', confirmPayment);
$newTxnBtn.addEventListener('click', startNewTransaction);

// Enter key in payment input = confirm
$amountPaid.addEventListener('keydown', e => {
  if (e.key === 'Enter') confirmPayment();
});

// ---------- Boot ----------
loadProducts();
renderCart();