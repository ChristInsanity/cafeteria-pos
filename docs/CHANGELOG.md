# CHANGELOG

All notable changes to Cafeteria POS are recorded here.
See `agent.md` for the modification protocol.

---

## [Unreleased]

### Added
- `agent.md` — AI restriction contract
- `knowledge.md` — project knowledge base
- `README.md` — submission documentation
- `docs/ai-usage.md` — AI-assisted development log
- `docs/CHANGELOG.md` — this file

---

## [1.2.0] — 2026-10-14

### Added
- `products.php` — product management page with inline edit + soft delete
- `docs/screenshots/` — 8 submission screenshots
  (product list, cart, payment error, receipt modal, GitHub history,
  transaction history, add product, products management)

### Changed
- `assets/js/app.js` — receipt now displays in a modal overlay instead of
  an inline panel; body scroll locks while modal is open
- `assets/css/style.css` — added modal styles, products-table styles,
  inline-edit inputs, badges, action buttons
- `index.php`, `history.php`, `add_product.php` — nav updated to include
  a **Products** link (uniform across all pages)
- `docs/CHANGELOG.md` — this file
- `README.md` — documented new pages and features

### MUTATE notes
Restricted files modified in this release (per `agent.md`):
- `assets/js/app.js` — approved by jhonpatrickacedo (2026-10-14)
  Reason: receipt UX change (inline panel → modal)

---

## [1.1.0] — 2026-10-14

### Added
- `history.php` — transaction history page (all past sales, newest first)
- `add_product.php` — form to add new products to the menu
- Nav links across all pages: **Menu · History · Add Product**
- Footer branding: terminal ID, cashier name, institution

### Changed
- `index.php` — added "+ Add product" link in Menu panel, removed decorative nav
- `assets/css/style.css` — added form styles, history table styles,
  wide layout, panel link, footer polish
- Removed unused `api/add_product.php` (form handles POST directly)

### Scope note
Extended the app beyond the IT 415 exam's minimum requirements.
Core exam features (Section V) remain unchanged and verified.

---

## [1.0.0] — 2026-10-13

### Added
- Initial project scaffold (folder structure, `.gitignore`)
- MySQL database `cafeteria_pos` with 3 tables:
  - `products` (6 seeded items for Scenario 1)
  - `transactions`
  - `transaction_items`
- `config/db.php` — PDO connection (gitignored)
- `config/db.example.php` — template for other machines
- `api/products.php` — GET endpoint returning active menu items as JSON
- `api/checkout.php` — POST endpoint processing payment and saving the
  transaction (with server-side validation + DB transaction + rollback)
- `index.php` — single-page UI (Apple-inspired)
- `assets/css/style.css` — Apple-inspired styling
- `assets/js/app.js` — cart, totals, validation, receipt logic
- `docs/cafeteria_pos.sql` — schema + seed data

### Verified
- End-to-end transaction flow tested with Scenario 1 worked example:
  - 2× Fried Chicken + 1× Rice + 1× Iced Tea = ₱165.00
  - Paid ₱200.00 → Change ₱35.00
- All Section V required features pass
- Validation cases pass: blank, non-numeric, negative, insufficient
- "Start New Transaction" resets cart, payment input, and receipt

---

## How to use this file

When a **restricted** file (see `agent.md`) is modified, add an entry here
**before** committing, with:

- Date
- File modified
- Reason (why the change was necessary)
- Approval (who authorized it)

Example entry for a future `MUTATE:` commit:

```
### MUTATE — 2026-10-15
- File: api/checkout.php
- Reason: Fixed rare case where a 0-quantity item was accepted
- Approved by: jhonpatrickacedo
```