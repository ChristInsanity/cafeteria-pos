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
