# AI-Assisted Development Log

Per IT 415 exam Section VIII: minimum **2 documented AI uses** required.
This log records every AI-assisted prompt used in building this project.

---

## Entry #1 — Generate the complete POS application structure

**Date:** 2026-10-13

**Prompt used:**
> "This is our exam. We are instructed to make Scenario 1 which is the
> Cafeteria POS. I will use HTML, CSS, JS, PHP, and XAMPP for database.
> Include a skill so that the system is restricted to modify anything.
> You will give me an agent.md and knowledge.md. We also need to push it
> onto GitHub."

**Purpose of the prompt:**
Set up the whole project: decide the tech stack, plan the folder structure,
scaffold the front-end + back-end for the POS transaction flow, and set up
GitHub version control with documentation.

**AI response (summary):**
The AI proposed a step-by-step plan: create a folder structure under XAMPP,
initialize Git, build the DB schema, write API endpoints (`products.php`,
`checkout.php`), build the front-end (`index.php`, `style.css`, `app.js`),
and write `agent.md` (restriction contract) + `knowledge.md` (project
knowledge base) plus a GitHub workflow with commit conventions.

**Evaluation:**
**Correct.** The plan matched the IT 415 exam requirements (Section V and
Section IX). The suggested structure was clean, minimal, and avoided
unnecessary frameworks. The "restriction skill" idea was implemented as
`agent.md`, which lists protected files and requires a `MUTATE:` commit
prefix for changes.

**Modifications I made:**
- Chose an Apple-inspired UI (soft gray, white panels, SF font stack)
  instead of a plain default style
- Added a real MySQL persistence layer with three tables (products,
  transactions, transaction_items) rather than in-memory cart only
- Restricted a specific file list in `agent.md` (config, SQL, APIs, app.js)
  rather than protecting everything

---

## Entry #2 — Write the front-end cart and payment logic

**Date:** 2026-10-13

**Prompt used:**
> "Build the front-end logic (`assets/js/app.js`). It should fetch products
> from the API, render them as clickable cards, manage a cart with
> add/increase/decrease/remove, compute subtotals and total, validate a
> cash payment (reject blank, non-numeric, negative, and insufficient),
> POST to a checkout endpoint, and render a receipt. Also include a
> 'Start New Transaction' reset."

**Purpose of the prompt:**
Generate the entire client-side JavaScript that drives the transaction
flow described in exam Section IV.

**AI response (summary):**
The AI returned a complete `app.js` with:
- A `cart` object keyed by product ID
- `loadProducts()` fetching from `api/products.php`
- `renderProducts()` and `renderCart()` for the two panels
- Quantity controls with `data-action` buttons (`inc`, `dec`, `rm`)
- `validatePayment()` covering all four invalid cases
- `confirmPayment()` POSTing JSON to `api/checkout.php`
- `showReceipt()` that renders a receipt table with reference number
- `startNewTransaction()` for a clean reset

**Evaluation:**
**Correct.** The logic matched the exam's transaction flow. Minor issues:
- Used `innerHTML +=` inside a loop (inefficient, and XSS-prone)
- Payment input lacked an Enter-key shortcut
- Money formatting was scattered with raw `.toFixed(2)` calls

**Modifications I made:**
- Replaced loop-appended HTML with a single `innerHTML` assignment plus
  delegated button listeners via `data-action` attributes
- Added a shared `peso()` helper so all currency uses the same format
- Added an Enter-key listener on the payment input for keyboard UX
- Added `setMessage()` / `clearMessage()` helpers for consistent on-screen
  feedback

---

## Entry #3 — Build the server-side checkout endpoint

**Date:** 2026-10-13

**Prompt used:**
> "Build `api/checkout.php` — a PHP endpoint that accepts JSON with
> `amount_paid` and `items` (product_id, quantity), validates input,
> re-fetches product prices from the database, computes the total,
> rejects insufficient payments, inserts a `transactions` row and
> `transaction_items` rows, generates a unique reference number, and
> returns a receipt JSON."

**Purpose of the prompt:**
Create the back-end payment processor that acts as the source of truth,
so the client cannot tamper with prices by editing the DOM.

**AI response (summary):**
The AI returned a PHP script that:
- Reads the raw JSON body via `php://input`
- Validates `amount_paid` (null, non-numeric, negative)
- Re-fetches product prices from the `products` table using an
  `IN (?,?,...)` prepared statement
- Computes the total server-side
- Wraps inserts in `beginTransaction()` / `commit()` / `rollBack()`
- Generates a reference number like `TXN-20261013-080101-1234`
- Returns a JSON receipt with all line items and totals

**Evaluation:**
**Correct.** Handles the security-sensitive path properly — never trusts
the client's prices, uses prepared statements, and rolls back on failure.
I liked that it re-fetched prices from the DB instead of trusting the
client payload.

**Modifications I made:**
- Added HTTP status codes (400 / 500) so the front-end could distinguish
  validation errors from server errors
- Formatted the "insufficient payment" message with `number_format(...,2)`
  so it matches the client-side wording exactly
- Confirmed that the reference number is `UNIQUE` in the schema to avoid
  collisions

---

## Summary

- **Total AI uses documented:** 3 (exam requires minimum 2) ✅
- **All AI output was reviewed, tested, and modified before use** ✅
- **Student can explain:**
  - Why the cart uses an object keyed by product ID
  - Why validation runs on both JS and PHP
  - How the reference number is generated
  - Why product prices are re-fetched server-side
  - How the "Start New Transaction" reset works