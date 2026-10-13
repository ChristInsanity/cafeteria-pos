<?php
require_once __DIR__ . '/config/db.php';

$errors  = [];
$success = false;
$name    = '';
$price   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $price = trim($_POST['price'] ?? '');

    if ($name === '') {
        $errors[] = 'Product name is required.';
    } elseif (mb_strlen($name) > 100) {
        $errors[] = 'Product name must be 100 characters or less.';
    }

    if ($price === '') {
        $errors[] = 'Price is required.';
    } elseif (!is_numeric($price)) {
        $errors[] = 'Price must be a valid number.';
    } elseif ((float) $price <= 0) {
        $errors[] = 'Price must be greater than zero.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO products (name, price, is_active) VALUES (?, ?, 1)"
            );
            $stmt->execute([$name, (float) $price]);
            $success = true;
            $name  = '';
            $price = '';
        } catch (Throwable $e) {
            $errors[] = 'Could not save the product. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product — Cafeteria POS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <nav class="nav">
    <div class="nav-inner">
      <a href="index.php" class="nav-logo">Cafeteria POS</a>
      <ul class="nav-links">
        <li><a href="index.php">Menu</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="add_product.php">Add Product</a></li>
      </ul>
    </div>
  </nav>

  <section class="hero">
    <h1>Add Product</h1>
    <p class="hero-tag">Add a new item to the menu.</p>
  </section>

  <main class="layout-single">
    <section class="panel">

      <?php if ($success): ?>
        <p class="message success">Product added successfully.</p>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $e): ?>
          <p class="message error"><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
      <?php endif; ?>

      <form method="POST" action="add_product.php" class="form">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" placeholder="e.g. Halo-halo"
               value="<?= htmlspecialchars($name) ?>" required maxlength="100">

        <label for="price">Price (₱)</label>
        <input type="number" id="price" name="price" placeholder="0.00"
               step="0.01" min="0.01" value="<?= htmlspecialchars($price) ?>" required>

        <button type="submit" class="btn btn-primary">Add Product</button>
      </form>
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