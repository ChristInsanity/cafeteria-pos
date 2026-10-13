# agent.md — AI Agent Contract for Cafeteria POS

## Purpose
This file defines the rules any AI assistant (or human developer) must follow
when modifying the **Cafeteria POS** codebase. It is a *restriction skill* —
the system is intentionally locked so that critical files cannot be altered
without an explicit, documented decision.

## Restricted Files (do NOT modify without permission)

The following files are **protected**. Any change requires:
1. An explicit user instruction ("I allow you to modify X")
2. A written reason in `docs/CHANGELOG.md`
3. A Git commit with the message prefix `MUTATE:` (see commit rules below)

| File                    | Why it's restricted                               |
|-------------------------|---------------------------------------------------|
| `config/db.php`         | Holds live DB credentials — never touched by AI.  |
| `docs/cafeteria_pos.sql`| DB schema + seed data — changes break existing data. |
| `api/checkout.php`      | Payment logic — financial integrity.              |
| `api/products.php`      | Source of truth for menu — pricing integrity.     |
| `assets/js/app.js`      | Core transaction flow — validation logic lives here. |

## Modification Protocol

Before modifying ANY restricted file, the AI must:
1. **Ask** the user for explicit permission.
2. **State** which file and what change.
3. **Wait** for confirmation.
4. **Log** the change (see below).

If any of these steps are skipped, the modification must be reverted.

## Commit Message Rules

All commits must be prefixed with one of:

- `feat:`     — new feature (non-restricted files)
- `fix:`      — bug fix
- `style:`    — CSS / UI only
- `docs:`     — documentation
- `chore:`    — setup, config, tooling
- `MUTATE:`   — **only** for changing restricted files, with justification in body

Example `MUTATE:` commit:
