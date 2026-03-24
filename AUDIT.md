# Haarlem Festival — Full Verification Audit

**Date:** 2026-03-24
**Branch:** dev-sprint1

---

## 1. USERS

### Website — Visitor

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 1 | **Login/logout** (username OR email + password) | ✅ IMPLEMENTED | Both username and email accepted in a single `login` field. Argon2id password hashing. Session destroyed on logout. |
| 2 | **Register** (captcha, hashed password, duplicate checks) | ✅ IMPLEMENTED | reCAPTCHA v2 on registration. Argon2id hashing. Checks `existsByUsername()` and `existsByEmail()` before creation. |

### Website — Customer

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 3 | **Password reset** (email link, set new password) | ✅ IMPLEMENTED | CSPRNG token (64 hex chars), SHA-256 hashed before DB storage, 1-hour expiry, single-use tokens. |
| 4 | **Manage account** (edit email/name/password/picture + confirmation email) | ❌ MISSING | No customer-facing account management page exists. No routes like `/account` or `/profile`. No confirmation email on changes. Admin can edit users via CMS only. |

### CMS — Administrator

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 5 | **Login/logout** (username OR email + password) | ✅ IMPLEMENTED | Separate CMS login at `/cms/login`. Requires Administrator role. Generic error messages prevent enumeration. |
| 6 | **Manage users (CRUD)** with search/filter/sort + registration date | ✅ IMPLEMENTED | Full CRUD with soft-delete. Search by username/email/name. Filter by role. Sort by multiple columns. Registration date displayed. Self-delete protection. |

---

## 2. GENERAL INFORMATION

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 7 | **View homepage** | ✅ IMPLEMENTED | Homepage at `/` with dynamic sections (hero, intro, event showcase). |
| 8 | **Edit homepage** via CMS | ✅ IMPLEMENTED | Admin can edit all homepage sections via CMS page editor with section/item architecture. |

---

## 3. EVENTS

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 9 | **Visual/functional design match** | ⚠️ PARTIAL | Jazz, History, Restaurant, Storytelling pages exist with detailed designs. **Dance event type has no frontend page** (enum + config exist, but no controller/view/route — will 404). |
| 10 | **View general event information** (overview pages) | ⚠️ PARTIAL | Overview pages exist for Jazz, History, Restaurant, Storytelling. **Dance overview page missing.** |
| 11 | **View specific event detail** | ⚠️ PARTIAL | Detail pages for Jazz artists, History locations, Restaurants, Storytelling events. **No Dance detail page.** |
| 12 | **Purchase tickets/reservations** | ✅ IMPLEMENTED | Users can add events to cart and proceed through Stripe checkout. |
| 13 | **Personal program** (after purchase, viewable) | ⚠️ PARTIAL | Cart/"My Program" exists as pre-purchase view. **No post-purchase order history page for customers.** |
| 14 | **All-access pass** | ⚠️ PARTIAL | `PassType`, `PassPurchase`, `PassScope` models exist in DB. **No frontend UI to add passes to cart.** |
| 15 | **Day pass** | ⚠️ PARTIAL | Same as above — DB models ready, **no frontend UI.** |
| 16 | **Edit general event info** (CMS) | ✅ IMPLEMENTED | CMS page editor supports editing event overview pages. |
| 17 | **Manage event entities (CRUD)** | ✅ IMPLEMENTED | Full CRUD for Artists, Restaurants, Events, Sessions, Venues via CMS. |
| 18 | **Manage tickets and availabilities** | ✅ IMPLEMENTED | Admin can set capacity/limits per session. Enforced at checkout via `validateItemAvailability()` with atomic seat reservation in `CheckoutService`. |

---

## 4. SHOPPING CART

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 19 | **Manage shopping cart** (change quantity, delete items) | ✅ IMPLEMENTED | Full cart with +/- quantity controls, item deletion, cart clearing. Works for both anonymous and logged-in users. |

---

## 5. ORDERS

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 20 | **Order payment** (iDEAL/Stripe sandbox) | ✅ IMPLEMENTED | Stripe integration with test keys. iDEAL and credit card supported. Webhook-based payment verification. |
| 21 | **Receive tickets via email** (PDF with QR) | ❌ MISSING | No PDF generation, no QR code generation, no ticket email sending. DB schema exists (`Ticket` table) but no implementation. |
| 22 | **Receive invoice via email** (PDF) | ❌ MISSING | No invoice PDF generation, no invoice email. DB schema exists (`Invoice`, `InvoiceLine` tables) but no implementation. |
| 23 | **Pay later** (24h window) | ⚠️ PARTIAL | `PayBeforeUtc` set to +24 hours on order creation. **No retry payment flow, no reminder emails, no "resume payment" page.** |
| 24 | **View orders** (CMS admin) | ✅ IMPLEMENTED | Admin order list at `/cms/orders` with status filtering, order details, payment status. |
| 25 | **Export orders** (CSV/Excel with column selection) | ❌ MISSING | No export functionality at all. |
| 26 | **Ticket scanner** (mobile-friendly, camera QR scan, already-scanned warning) | ❌ MISSING | No scanner controller, no camera integration, no scan endpoints. DB fields exist (`isScanned`, `scannedAtUtc`, `scannedByUserId`) but no implementation. |

---

## 6. IMPORTANT REQUIREMENTS & ADDITIONAL FEATURES

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 27 | **WYSIWYG editor on HTML fields** | ✅ IMPLEMENTED | TinyMCE 6 integrated for HTML content items in CMS page editor. |
| 28 | **Image upload in WYSIWYG editor** | ✅ IMPLEMENTED | TinyMCE `image` plugin added with `images_upload_handler` that uploads to existing CMS endpoint. Supports toolbar button and drag-and-drop. |
| 29 | **Featured image management** | ✅ IMPLEMENTED | Media library at `/cms/media` with upload/delete. Events have `FeaturedImageAssetId`. Separate image picker in CMS forms. |
| 30 | **All pages are dynamic** (no static content) | ✅ IMPLEMENTED | All page content loaded from DB via `CmsContentRepository`. Views are structural templates; text comes from database. |
| 31 | **Social media sharing** | ✅ IMPLEMENTED | Share buttons (Facebook, X, WhatsApp) on jazz artist detail and storytelling detail pages via reusable `_social-share.php` partial. |
| 32 | **Ticket availability enforcement** | ✅ IMPLEMENTED | `CheckoutService::validateItemAvailability()` checks quantity vs available seats. Atomic `decrementCapacity()` in DB transaction prevents overselling. Capacity restored on cancel/expiry. |
| 33 | **Sold-out prevention** | ✅ IMPLEMENTED | Checkout blocked when `getAvailableSeats() <= 0`. Atomic WHERE guard in `decrementCapacity()` prevents going below zero under concurrency. |
| 34 | **90% single-ticket cap** | ✅ IMPLEMENTED | `CheckoutConstraints::SINGLE_TICKET_CAPACITY_RATIO = 0.9` enforced in `validateItemAvailability()`. Remaining 10% reserved for pass holders. |
| 35 | **Payment status verification** | ✅ IMPLEMENTED | Stripe webhook verifies payment status. Idempotent processing. Status transitions properly tracked. |
| 36 | **Invoice required fields** | ⚠️ PARTIAL | DB schema has all required fields (invoice number, date, client info, subtotals, VAT rates). No generation logic. |
| 37 | **Ticket required fields** | ⚠️ PARTIAL | DB schema has `ticketCode`, scanning fields. No ticket creation, no QR code generation. |
| 38 | **QR code is secure** (not guessable) | ❌ MISSING | No QR code generation at all. No QR library installed. |
| 39 | **SQL injection prevention** | ✅ IMPLEMENTED | All queries use PDO prepared statements with named parameters. `ATTR_EMULATE_PREPARES => false`. |
| 40 | **Code injection prevention** | ✅ IMPLEMENTED | Input sanitization with `trim()`, `strip_tags()`. Type casting for numerics. Validation helpers centralized. |
| 41 | **XSS prevention** | ✅ IMPLEMENTED | `htmlspecialchars()` / `htmlentities()` on all output. `CmsOutputHelper::text()` for escaped output, `::html()` for controlled HTML. |
| 42 | **CSRF prevention** | ✅ IMPLEMENTED | Per-scope CSRF tokens using `hash_equals()` timing-safe comparison. Required on all CMS forms. |
| 43 | **Bot prevention (captcha)** | ✅ IMPLEMENTED | Google reCAPTCHA v2 on registration form. `CaptchaService` validates server-side. |
| 44 | **CMS proper UI** | ✅ IMPLEMENTED | Uses Tailwind CSS with Lucide icons. Clean sidebar navigation, form components, data tables. |

---

## Summary

### Per-Section Counts

| Section | ✅ Implemented | ⚠️ Partial | ❌ Missing |
|---------|:-:|:-:|:-:|
| 1. Users | 4 | 0 | 1 |
| 2. General Information | 2 | 0 | 0 |
| 3. Events | 4 | 4 | 0 |
| 4. Shopping Cart | 1 | 0 | 0 |
| 5. Orders | 2 | 1 | 3 |
| 6. Additional Requirements | 15 | 2 | 1 |
| **TOTAL** | **28** | **7** | **5** |

---

## Full List of Missing / Partial Items

| Priority | Item | Status | What's Missing |
|----------|------|--------|----------------|
| Must | Ticket PDF generation + email | ❌ | No PDF lib, no QR lib, no ticket creation logic, no email sending |
| Must | Invoice PDF generation + email | ❌ | No PDF generation, no invoice creation after payment |
| Must | QR code (secure, not guessable) | ❌ | No QR library installed, no generation code |
| ~~Must~~ | ~~Ticket availability enforcement~~ | ✅ | ~~Implemented in `CheckoutService` with atomic `decrementCapacity()`~~ |
| ~~Must~~ | ~~Sold-out prevention~~ | ✅ | ~~Checkout blocked when available seats <= 0~~ |
| ~~Must~~ | ~~90% single-ticket cap~~ | ✅ | ~~Enforced via `CheckoutConstraints::SINGLE_TICKET_CAPACITY_RATIO`~~ |
| Must | Ticket scanner (employee) | ❌ | No controller, no UI, no camera integration |
| Must | Customer account management | ❌ | No routes/views for customers to edit their profile |
| Must | Pay later flow | ⚠️ | 24h deadline stored but no retry/resume payment mechanism |
| Must | Admin order export (CSV/Excel) | ❌ | No export functionality |
| Must | Passes (all-access + day) UI | ⚠️ | DB models exist, no frontend to purchase |
| Must | Dance event pages | ⚠️ | Enum + config exist, no controller/view/route |
| Must | Post-purchase personal program | ⚠️ | Cart exists, no order history page for customers |
| ~~Should~~ | ~~Image upload in WYSIWYG~~ | ✅ | ~~TinyMCE `image` plugin with upload handler added~~ |
| Should | Invoice schema completion | ⚠️ | DB fields ready, no generation logic |
| Should | Ticket schema completion | ⚠️ | DB fields ready, no creation logic |
| ~~Could~~ | ~~Social media sharing~~ | ✅ | ~~Share buttons on jazz and storytelling detail pages~~ |

---

## Prioritized Action Plan

### Priority 1 — Must-Have / Critical Path

1. **Ticket + Invoice generation pipeline** — Install a PDF library (e.g. TCPDF or Dompdf), QR code library (e.g. `endroid/qr-code`). After successful Stripe webhook, generate Ticket rows with secure random codes, create QR codes, build PDF tickets and invoices, email them via `EmailService`.

2. ~~**Availability enforcement**~~ ✅ DONE — Capacity checks in `CheckoutService::validateItemAvailability()`, atomic `decrementCapacity()` in transaction, capacity restored on cancel/expiry. 90% single-ticket cap enforced via `CheckoutConstraints`.

3. **Ticket scanner** — Create `ScannerController` with mobile-friendly page. Integrate camera-based QR scanning (e.g. `html5-qrcode` JS library). Add `POST /api/scan` endpoint to mark tickets as scanned with duplicate-scan warnings.

4. **Customer account management** — Add `/account` route, `AccountController`, and views for editing email, name, password, profile picture. Send confirmation emails on changes.

5. **Pay later flow** — Add a `/checkout/retry/{orderId}` route that creates a new Stripe session for unpaid orders within 24h. Optionally add email reminders for pending orders.

6. **Order export** — Add CSV/Excel export to `CmsOrdersController` with column selection UI. Use a library like PhpSpreadsheet for Excel.

7. **Dance event pages** — Create `DanceController`, views, and routes mirroring the Jazz implementation pattern.

### Priority 2 — Should-Have

8. ~~**WYSIWYG image upload**~~ ✅ DONE — TinyMCE `image` plugin added with `images_upload_handler` pointing to existing CMS upload endpoint. Supports toolbar button and drag-and-drop.

9. **Pass purchase UI** — Build frontend for adding all-access and day passes to cart, leveraging existing `PassType`/`PassPurchase` models.

10. **Post-purchase program page** — Add customer order history view showing purchased tickets and their status.

### Priority 3 — Could-Have

11. ~~**Social media sharing**~~ ✅ DONE — Share buttons (Facebook, X, WhatsApp) added to jazz artist detail and storytelling detail pages via reusable `_social-share.php` partial.
