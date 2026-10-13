# Cafeteria POS

A computer-based food ordering and Point-of-Sale (POS) system for a campus cafeteria counter.

Built for the **IT 415 Midterm Practical Examination**
(First Semester, SY 2026–2027) — **Scenario 1: Campus Cafeteria POS**.

---

## Live Demo

Local only (no deployment required):

```
http://localhost/cafeteria-pos/
```

---

## Features

### Required (exam Section V)

- Display at least 5 products with names and prices
- Add products to cart via on-screen buttons
- Increase / decrease item quantities
- Remove items from cart
- Auto-compute subtotals and total on every cart change
- Clear order summary with items, quantities, subtotals, total
- Numeric cash payment input
- Payment validation: blank, non-numeric, negative, insufficient
- Correct change computation (`Change = Amount Paid − Total`)
- Payment confirmation screen
- Digital receipt with items, quantities, subtotals, total, amount paid, change, transaction reference
- "Start New Transaction" — clears cart, payment, receipt

### Extra (not required, but included)

- Double validation (client + server)
- Persistent transaction history in MySQL
- Apple-inspired clean UI

---

## Tech Stack

| Layer        | Technology                    |
|--------------|-------------------------------|
| Frontend     | HTML, CSS, vanilla JavaScript |
| Backend      | PHP 8 (procedural, PDO)       |
| Database     | MySQL (via XAMPP)             |
| Version Ctrl | Git + GitHub                  |

No frameworks. No build tools. No dependencies.

---

## Project Structure

```
cafeteria-pos/
├── index.php                  ← Main UI
├── api/
│   ├── products.php           ← GET — menu as JSON
│   └── checkout.php           ← POST — processes payment, saves txn
├── assets/
│   ├── css/style.css          ← Styling (Apple-inspired)
│   └── js/app.js              ← Front-end logic
├── config/
│   ├── db.php                 ← LIVE DB credentials (gitignored)
│   └── db.example.php         ← Template for other machines
├── docs/
│   ├── cafeteria_pos.sql      ← DB schema + seed data
│   └── ai-usage.md            ← AI-assisted dev log (exam req.)
├── agent.md                   ← AI restriction contract
├── knowledge.md               ← Project knowledge base
└── README.md                  ← This file
```

---

## Setup Instructions

### 1. Prerequisites
- XAMPP installed (Apache + MySQL)
- A browser (Chrome, Firefox, Safari)
- Git (only if you're cloning)

### 2. Get the code

```
cd /Applications/XAMPP/xamppfiles/htdocs
git clone https://github.com/pejays/cafeteria-pos.git
```

Or download the ZIP and extract it into `htdocs/`.

### 3. Start XAMPP
Launch XAMPP Manager → start **Apache** and **MySQL**.

### 4. Create the database
Open `http://localhost/phpmyadmin` → **Import** → choose
`docs/cafeteria_pos.sql` → **Import**.

This creates the `cafeteria_pos` database with the schema and 6 seeded
products.

### 5. Configure DB credentials
Copy `config/db.example.php` → `config/db.php`, then edit if needed.
Default XAMPP values are already correct.

### 6. Run it
Open `http://localhost/cafeteria-pos/` in your browser.

---

## How to Use (Transaction Flow)

1. **Browse** — product cards load on the left
2. **Add to cart** — click a card, it appears on the right
3. **Adjust** — use `−` / `+` buttons or **Remove**
4. **Review** — total updates live at the bottom of the cart
5. **Enter cash** — type the amount paid
6. **Confirm payment** — validation runs; errors show if invalid
7. **View receipt** — reference no., items, total, paid, change
8. **New transaction** — resets everything for the next customer

### Worked Example

Order: 2× Fried Chicken, 1× Rice, 1× Iced Tea. Cash: ₱200.00.

| Item                | Qty | Price   | Subtotal |
|---------------------|-----|---------|----------|
| Fried Chicken (1pc) | 2   | ₱65.00  | ₱130.00  |
| Rice (Plain)        | 1   | ₱15.00  | ₱15.00   |
| Iced Tea (cup)      | 1   | ₱20.00  | ₱20.00   |

- **Total:** ₱165.00
- **Amount Paid:** ₱200.00
- **Change:** ₱35.00

---

## Database Schema

Three tables (see `docs/cafeteria_pos.sql`):

- **`products`** — menu items (name, price, active flag)
- **`transactions`** — one row per completed sale (reference no., totals)
- **`transaction_items`** — line items per transaction (qty, price, subtotal)

Full schema is documented in `knowledge.md`.

---

## Validation Rules

| Case               | Message                                                |
|--------------------|--------------------------------------------------------|
| Blank amount       | "Please enter a valid payment amount."                 |
| Non-numeric        | "Please enter a valid payment amount."                 |
| Negative           | "Please enter a valid payment amount."                 |
| Less than total    | "Insufficient payment. Please enter at least X.XX."    |
| Empty cart         | "Cart is empty. Add products first."                   |

Validation runs **both** in JavaScript (instant feedback) and PHP
(source of truth; cannot be bypassed by tampering with the DOM).

---

## Version Control

See `agent.md` for commit message conventions. Summary:

- `feat:` — new feature
- `fix:` — bug fix
- `style:` — CSS only
- `docs:` — documentation
- `chore:` — setup / tooling
- `MUTATE:` — modifying a **restricted** file (requires justification)

### Restricted files (do not modify without documented reason)

- `config/db.php`
- `docs/cafeteria_pos.sql`
- `api/checkout.php`
- `api/products.php`
- `assets/js/app.js`

---

## AI-Assisted Development

This project was built with AI assistance, as required by the exam.
Full prompt log: **`docs/ai-usage.md`**.

Policy highlights (see `agent.md`):

- Every AI output was reviewed, tested, and modified before use
- Every prompt is logged with purpose, evaluation, and modifications
- No AI-generated code was committed unreviewed

---

## Known Limitations

- No login / authentication (not required by exam)
- Single terminal / cashier (no concurrency handling)
- On-screen receipt only (no physical printer)
- Menu is seeded via SQL (no in-app product editor)

---

## Author

**Jhon Patrick Acedo**
BS Information Technology
Davao del Norte State College — Institute of Computing
IT 415 — First Semester, SY 2026–2027

---

## License

Academic project — not licensed for redistribution.