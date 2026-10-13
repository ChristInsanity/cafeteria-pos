# knowledge.md — Project Knowledge Base

## Project Identity
- **Name:** Cafeteria POS
- **Course:** IT 415 — Application Development and Emerging Technologies
- **Exam:** Midterm Practical Examination (2.5 hours, 100 points)
- **Scenario:** 1 — Campus Cafeteria POS
- **Author:** jhonpatrickacedo
- **Repo:** https://github.com/pejays/cafeteria-pos
- **Date Started:** 2026-10-13
- **School:** Davao del Norte State College — Institute of Computing

## What This App Is
A desktop-oriented web application for a walk-up campus cafeteria counter.
A student assistant takes orders, adds them to a cart, computes totals,
accepts cash, gives change, and prints/display a digital receipt.

**Explicitly NOT in scope:**
- User login / authentication
- Inventory tracking
- Real payment gateway / card / QR
- Deployment / hosting
- Multi-user concurrency
- Product CRUD (menu is hardcoded / DB-seeded, not editable in UI)

## Tech Stack
- **Frontend:** HTML, CSS (Apple-inspired, hand-written), vanilla JavaScript
- **Backend:** PHP (procedural, PDO for DB)
- **Database:** MySQL via XAMPP
- **Version Control:** Git + GitHub (`pejays/cafeteria-pos`)
- **Local Server:** XAMPP (Apache + MySQL)
- **AI Assistance:** Documented in `docs/ai-usage.md`

## File Map

```
cafeteria-pos/
├── index.php                  ← Main UI (single page)
├── api/
│   ├── products.php           ← GET — returns menu as JSON
│   └── checkout.php           ← POST — processes payment, saves txn
├── assets/
│   ├── css/style.css          ← All styling (Apple-like)
│   └── js/app.js              ← All front-end logic
├── config/
│   ├── db.php                 ← LIVE DB credentials (gitignored)
│   └── db.example.php         ← Template for setup on other machines
├── docs/
│   └── cafeteria_pos.sql      ← DB schema + seed data
├── agent.md                   ← AI restriction contract
├── knowledge.md               ← This file
└── README.md                  ← Submission summary
```

## Database Schema

**Database name:** `cafeteria_pos`

### `products`
| Column     | Type           | Notes                          |
|------------|----------------|--------------------------------|
| id         | INT PK AI      |                                |
| name       | VARCHAR(100)   |                                |
| price      | DECIMAL(10,2)  | Philippine pesos               |
| is_active  | TINYINT(1)     | 1 = visible, 0 = hidden        |
| created_at | TIMESTAMP      |                                |

### `transactions`
| Column        | Type           | Notes                          |
|---------------|----------------|--------------------------------|
| id            | INT PK AI      |                                |
| reference_no  | VARCHAR(30) UQ | e.g. `TXN-20261014-080101-1234`|
| total_amount  | DECIMAL(10,2)  |                                |
| amount_paid   | DECIMAL(10,2)  |                                |
| change_amount | DECIMAL(10,2)  |                                |
| created_at    | TIMESTAMP      |                                |

### `transaction_items`
| Column         | Type           | Notes                          |
|----------------|----------------|--------------------------------|
| id             | INT PK AI      |                                |
| transaction_id | INT FK         | → transactions.id (cascade)    |
| product_id     | INT FK         | → products.id                  |
| product_name   | VARCHAR(100)   | Snapshot (denormalized)        |
| unit_price     | DECIMAL(10,2)  | Snapshot                       |
| quantity       | INT            |                                |
| subtotal       | DECIMAL(10,2)  |                                |

## Seeded Menu (Scenario 1)

| Product                  | Price    |
|--------------------------|----------|
| Rice (Plain)             | ₱15.00   |
| Fried Chicken (1 pc)     | ₱65.00   |
| Pork Adobo               | ₱60.00   |
| Vegetable Side Dish      | ₱35.00   |
| Iced Tea (cup)           | ₱20.00   |
| Bottled Water            | ₱20.00   |

## Transaction Flow (Section IV of exam)

1. Browse Products — grid on left, loaded from `api/products.php`
2. Add to Cart — click product card
3. Review Order — cart panel on right shows items, qty, subtotals, total
4. Enter Payment — numeric input for cash amount
5. Validate & Compute — client-side JS + server-side PHP both validate
6. Confirm Payment — POST to `api/checkout.php`, DB transaction committed
7. Receipt / New Transaction — receipt panel shown, "Start New Transaction" resets

## Validation Rules

| Case                  | Message                                                     |
|-----------------------|-------------------------------------------------------------|
| Blank amount          | "Please enter a valid payment amount."                      |
| Non-numeric amount    | "Please enter a valid payment amount."                      |
| Negative amount       | "Please enter a valid payment amount."                      |
| Amount < total        | "Insufficient payment. Please enter at least X.XX."         |
| Empty cart            | "Cart is empty. Add products first."                        |
| Valid + sufficient    | Proceeds → DB insert → receipt rendered                     |

**Validation runs on BOTH sides** (defense in depth):
- JS: instant feedback
- PHP: source of truth, cannot be bypassed

## Design Language
- Apple-inspired: soft `#f5f5f7` background, white rounded panels
- Font stack: `-apple-system, BlinkMacSystemFont, "SF Pro Text", Helvetica Neue`
- Subtle shadows, generous whitespace, thin borders (`#d2d2d7`)
- Accent color: Apple blue `#0071e3`
- No frameworks, no build tools — plain HTML/CSS/JS

## Git Commit Convention
See `agent.md` for the full rule list. Short version:
- `feat:` — new feature
- `fix:` — bug fix
- `style:` — CSS only
- `docs:` — documentation
- `chore:` — setup / tooling
- `MUTATE:` — modifying a restricted file (requires justification)

## AI-Assisted Development Policy
- AI use is allowed and expected (per exam Section VIII)
- Minimum **2 documented prompts** required
- Every prompt logged in `docs/ai-usage.md` with:
  - Exact prompt text
  - Purpose
  - AI response summary
  - Evaluation (correct / partially correct / incorrect)
  - Modifications made
- Student must understand and be able to explain any AI-generated code

## How to Run Locally

1. Start XAMPP (Apache + MySQL)
2. Ensure `cafeteria_pos` DB exists (import `docs/cafeteria_pos.sql`)
3. Verify `config/db.php` has correct credentials (default XAMPP: root, no password)
4. Open browser → `http://localhost/cafeteria-pos/`
5. That's it.

## Known Limitations / Future Work
- No login (not required)
- No multi-terminal support (single cashier assumed)
- No receipt printing to physical printer (on-screen only, per exam scope)
- Products not editable via UI (menu is seeded)

## Glossary
- **POS** — Point of Sale
- **TXN** — Transaction
- **PDO** — PHP Data Objects (PHP's DB abstraction layer)
- **XAMPP** — Cross-platform Apache + MySQL + PHP + Perl bundle
- **SKU** — Stock Keeping Unit (not used here; we use product IDs)

## Change Log
- 2026-10-13: Project scaffolded, DB created, core flow built and tested
- (append new entries here as the project evolves)

## Version
v1.0 — 2026-10-14 — Initial knowledge base for IT 415 Midterm