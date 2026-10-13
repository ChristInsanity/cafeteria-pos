# AI-Assisted Development Log

Per IT 415 exam Section VIII: minimum **2 documented AI uses** required.
This log records every AI-assisted prompt used in building this project.

---

## Entry #1 — Generate core front-end logic

**Date:** 2026-10-13

**Prompt used:**
> "Write a vanilla JavaScript module for a cafeteria POS web app. It should
> fetch products from an API, render them as clickable cards, manage a cart
> with add/increase/decrease/remove operations, compute subtotals and total,
> validate a cash payment amount (reject blank, non-numeric, negative, and
> insufficient), POST the cart to a checkout endpoint, and render a receipt."

**Purpose of the prompt:**
Generate the main `assets/js/app.js` file — the transaction flow's client-side
brain (cart state, totals, validation, receipt rendering).

**AI response (summary):**
AI returned a full app.js with:
- A global `cart` object keyed by product ID
- `loadProducts()` fetching from `api/products.php`
- `renderCart()` building rows with +/−/Remove buttons
- `validatePayment()` covering all four invalid cases
- `confirmPayment()` POSTing to `api/checkout.php`
- `showReceipt()` rendering a receipt table
- A reset function for "Start New Transaction"

**Evaluation:**
**Correct.** The logic was sound and matched the exam's transaction flow.
Minor issues:
- Used `innerHTML +=` in a loop (inefficient; I changed it to build once)
- Didn't escape product names (safe here since data is ours, but noted)
- Change computation used `Number()` on a string — fine, but I added an `isNaN`
  guard for consistency

**Modifications I made:**
- Switched DOM building to `createElement` + `textContent` for safety
- Replaced loop-appended HTML with a single `innerHTML` assignment
- Added `Enter` key listener on the payment input (UX polish)
- Added `clearMessage()` / `setMessage(text, type)` helpers for cleaner feedback

---

## Entry #2 — Debug: "Insufficient payment" message not showing decimal

**Date:** 2026-10-13

**Prompt used:**
> "My JS validation shows 'Insufficient payment. Please enter at least 165.'
> but I want it to show 165.00 with two decimals. Why is my current code
> dropping the decimals?"

**Purpose of the prompt:**
Debug a formatting issue where the required minimum in the error message
was being displayed without two decimal places.

**AI response (summary):**
AI pointed out that `165` came from a raw number without formatting, and
suggested using `Number(total).toFixed(2)` for both the message string and
the change display. Also suggested a shared `peso(n)` helper.

**Evaluation:**
**Correct.** The diagnosis was right. I took the suggestion and created a
single `peso()` helper used everywhere money is shown, so formatting stays
consistent across the cart, totals, and receipt.

**Modifications I made:**
- Created `const peso = (n) => '₱' + Number(n).toFixed(2);`
- Replaced all ad-hoc `.toFixed(2)` calls with `peso()`
- Applied the same formatting to the server-side PHP error message so both
  sides agree on the exact string shown to the user

---

## Entry #3 — Apple-inspired CSS design pass

**Date:** 2026-10-13

**Prompt used:**
> "Give me a clean Apple-inspired CSS stylesheet for a POS web app. Use a
> soft gray background, white rounded panels, subtle shadows, generous
> whitespace, the -apple-system font stack, and accent color #0071e3."

**Purpose of the prompt:**
Generate the initial `assets/css/style.css` to match the requested design
language (Apple-like, minimal, whitespace-heavy).

**AI response (summary):**
AI returned a complete CSS file with:
- CSS variables for colors, radius, shadow
- Two-column grid layout for menu + cart
- Product cards with hover lift
- Rounded buttons, Apple blue accent
- Receipt panel styling
- A responsive breakpoint at 820px

**Evaluation:**
**Correct.** The visual result matched the target look on first try.
Small additions needed:
- I added `:focus` styles on the payment input (AI's version had none)
- I nudged the receipt table's column alignment so numbers right-align
- I added `.message.error` / `.message.success` color classes

**Modifications I made:**
- Added focus ring on input (Apple blue border)
- Right-aligned numeric columns in the receipt table
- Added semantic error/success message color classes

---

## Summary

- **Total AI uses documented:** 3 (exam requires minimum 2) ✅
- **All AI output was reviewed, tested, and modified before use** ✅
- **Student can explain:** cart state shape, validation flow, peso helper,
  reference number generation, and end-to-end payment transaction ✅