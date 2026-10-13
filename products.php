<?php
// ============================================================
// Products Management Page — v3
// Row form via `form` attribute (no nested forms)
// ============================================================

require_once __DIR__ . '/config/db.php';

$message   = '';
$msgType   = '';
$editingId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        $message = 'Invalid product.';
        $msgType = 'error';
    } elseif ($action === 'deactivate') {
        try {
            $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?")->execute([$id]);
            $message = 'Product hidden from the menu.';
            $msgType = 'success';
        } catch (Throwable $e) {
            $message = 'Could not hide the product.';
            $msgType = 'error';
        }
    } elseif ($action === 'activate') {
        try {
            $pdo->prepare("UPDATE products SET is_active = 1 WHERE id = ?")->execute([$id]);
            $message = 'Product restored to the menu.';
            $msgType = 'success';
        } catch (Throwable $e) {
            $message = 'Could not restore the product.';
            $msgType = 'error';
        }
    } elseif ($action === 'update') {
        $name  = trim($_POST['name']  ?? '');
        $price = trim($_POST['price'] ?? '');

        if ($name === '' || mb_strlen($name) > 100) {
            $message = 'Product name is required (max 100 chars).';
            $msgType = 'error';
            $editingId = $id;
        } elseif ($price === '' || !is_numeric($price) || (float) $price <= 0) {
            $message = 'Price must be a valid number greater than zero.';
            $msgType = 'error';
            $editingId = $id;
        } else {
            try {
                $pdo->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?")
                    ->execute([$name, (float) $price, $id]);
                $message = 'Product updated.';
                $msgType = 'success';
            } catch (Throwable $e) {
                $message = 'Could not update the product.';
                $msgType = 'error';
            }
        }
    }
}

$products = $pdo->query("SELECT id, name, price, is_active FROM products ORDER BY is_active DESC, id ASC")->fetchAll();

function peso($n) {
    return '₱' . number_format((float) $n, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products — Cafeteria POS</title>
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
    <h1>Products</h1>
    <p class="hero-tag">Edit names and prices, or hide items from the menu.</p>
  </section>

  <main class="layout-wide">
    <section class="panel">

      <?php if ($message !== ''): ?>
        <p class="message <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>

      <?php if (empty($products)): ?>
        <p class="history-empty">No products yet.</p>
      <?php else: ?>
        <table class="products-table">
          <thead>
            <tr>
              <th>Name</th>
              <th class="num">Price</th>
              <th>Status</th>
              <th class="num">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p):
              $pid       = (int) $p['id'];
              $isEditing = ($editingId === $pid);
              $isActive  = (bool) $p['is_active'];
              $formId    = 'edit-form-' . $pid;
            ?>
              <tr data-id="<?= $pid ?>" class="<?= $isEditing ? 'row-editing' : '' ?>">

                <!-- ============== EDIT FORM (no wrapping, uses `form` attr) ============== -->
                <form id="<?= $formId ?>" method="POST" action="products.php"></form>
                <input type="hidden" name="id"     value="<?= $pid ?>" form="<?= $formId ?>">
                <input type="hidden" name="action" value="update"      form="<?= $formId ?>">

                <td class="cell-name">
                  <span class="view-name"><?= htmlspecialchars($p['name']) ?></span>
                  <input type="text"
                         name="name"
                         form="<?= $formId ?>"
                         class="edit-name"
                         value="<?= htmlspecialchars($p['name']) ?>"
                         maxlength="100"
                         required
                         <?= $isEditing ? '' : 'disabled' ?>>
                </td>

                <td class="num cell-price">
                  <span class="view-price"><?= peso($p['price']) ?></span>
                  <input type="number"
                         name="price"
                         form="<?= $formId ?>"
                         class="edit-price"
                         value="<?= htmlspecialchars($p['price']) ?>"
                         step="0.01"
                         min="0.01"
                         required
                         <?= $isEditing ? '' : 'disabled' ?>>
                </td>

                <td>
                  <?php if ($isActive): ?>
                    <span class="badge badge-active">Active</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Hidden</span>
                  <?php endif; ?>
                </td>

                <td class="num">
                  <div class="row-actions">

                    <?php if ($isActive): ?>
                      <!-- Read mode buttons -->
                      <button type="button"
                              class="action-btn action-edit btn-start-edit"
                              data-id="<?= $pid ?>">Edit</button>

                      <!-- Soft delete -->
                      <form method="POST" action="products.php" class="inline-form"
                            onsubmit="return confirm('Hide this product from the menu?');">
                        <input type="hidden" name="id"     value="<?= $pid ?>">
                        <input type="hidden" name="action" value="deactivate">
                        <button type="submit" class="action-btn action-delete btn-delete">Delete</button>
                      </form>

                      <!-- Edit mode buttons -->
                      <button type="submit"
                              form="<?= $formId ?>"
                              class="action-btn action-save btn-save">Save</button>
                      <button type="button"
                              class="action-btn action-cancel btn-cancel-edit">Cancel</button>

                    <?php else: ?>
                      <form method="POST" action="products.php" class="inline-form">
                        <input type="hidden" name="id"     value="<?= $pid ?>">
                        <input type="hidden" name="action" value="activate">
                        <button type="submit" class="action-btn action-restore">Restore</button>
                      </form>
                    <?php endif; ?>

                  </div>
                </td>
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

  <script>
    // Read mode: hide save/cancel + edit fields, show edit/delete buttons
    function setReadMode(row) {
      row.classList.remove('row-editing');
      row.querySelector('.btn-start-edit').style.display = '';
      const del = row.querySelector('.btn-delete');
      if (del) del.style.display = '';
      row.querySelector('.btn-save').style.display = 'none';
      row.querySelector('.btn-cancel-edit').style.display = 'none';
      row.querySelector('.edit-name').disabled  = true;
      row.querySelector('.edit-price').disabled = true;
    }

    // Edit mode
    function setEditMode(row) {
      row.classList.add('row-editing');
      row.querySelector('.btn-start-edit').style.display = 'none';
      const del = row.querySelector('.btn-delete');
      if (del) del.style.display = 'none';
      row.querySelector('.btn-save').style.display = '';
      row.querySelector('.btn-cancel-edit').style.display = '';
      const n = row.querySelector('.edit-name');
      const p = row.querySelector('.edit-price');
      n.disabled = false;
      p.disabled = false;
      n.focus();
      n.select();
    }

    document.querySelectorAll('.btn-start-edit').forEach(btn => {
      btn.addEventListener('click', () => setEditMode(btn.closest('tr')));
    });

    document.querySelectorAll('.btn-cancel-edit').forEach(btn => {
      btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        // restore original values
        const n = row.querySelector('.edit-name');
        const p = row.querySelector('.edit-price');
        n.value = row.querySelector('.view-name').textContent;
        const priceText = row.querySelector('.view-price').textContent.replace(/[^\d.]/g, '');
        p.value = priceText;
        setReadMode(row);
      });
    });

    // On first paint, hide save/cancel for every row
    document.querySelectorAll('.products-table tr').forEach(row => {
      const save   = row.querySelector('.btn-save');
      const cancel = row.querySelector('.btn-cancel-edit');
      if (save)   save.style.display = 'none';
      if (cancel) cancel.style.display = 'none';
    });

    // If PHP says this row had a validation error, reopen it in edit mode
    <?php if ($editingId > 0): ?>
      (function () {
        const row = document.querySelector('tr[data-id="<?= $editingId ?>"]');
        if (row) setEditMode(row);
      })();
    <?php endif; ?>
  </script>

</body>
</html>