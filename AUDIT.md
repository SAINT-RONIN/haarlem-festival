# Haarlem Festival — Full Verification Audit

**Date:** 2026-03-30
**Branch:** Story-page

---

## 1. USERS

### Website — Visitor

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 1 | **Login/logout** (username OR email + password) | ✅ IMPLEMENTED | Both username and email accepted in a single `login` field. Argon2id password hashing. Session regenerated on login, destroyed on logout. Generic error messages prevent enumeration. |
| 2 | **Register** (captcha, hashed password, duplicate checks) | ✅ IMPLEMENTED | reCAPTCHA v2 on registration. Argon2id hashing. Checks `existsByUsername()` and `existsByEmail()` before creation. Field-level validation (username 3-60 chars, email format, password min 8 chars). |

### Website — Customer

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 3 | **Password reset** (email link, set new password) | ✅ IMPLEMENTED | CSPRNG token (64 hex chars), SHA-256 hashed before DB storage, 1-hour expiry, single-use tokens. Always shows success message on request (prevents enumeration). Atomic DB transaction wraps password update + token invalidation. |
| 4 | **Manage account** (edit email/name/password/picture + confirmation email) | ❌ MISSING | No customer-facing account management page exists. No routes like `/account` or `/profile`. No AccountController or ProfileController. `profilePictureAssetId` and `isEmailConfirmed` fields exist in UserAccount model but are unused. Admin can edit users via CMS only. |

### CMS — Administrator

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 5 | **Login/logout** (username OR email + password) | ✅ IMPLEMENTED | Separate CMS login at `/cms/login`. Requires Administrator role. Generic error messages prevent enumeration. Already-authenticated admins redirected to `/cms`. |
| 6 | **Manage users (CRUD)** with search/filter/sort + registration date | ✅ IMPLEMENTED | Full CRUD with soft-delete. Search by username/email/name. Filter by role. Sort by multiple columns. Registration date displayed. Self-delete protection. Per-user CSRF token scoping. Shared `_user-form.php` partial for create/edit. |

---

## 2. GENERAL INFORMATION

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 7 | **View homepage** | ✅ IMPLEMENTED | Homepage at `/` with dynamic sections (hero, intro, event showcase, locations map, schedule preview). All data from DB via `HomeService::getHomePageData()`. |
| 8 | **Edit homepage** via CMS | ✅ IMPLEMENTED | Admin can edit all homepage sections via CMS page editor (`/cms/pages`) with section/item architecture. TinyMCE for HTML fields, image picker for featured images. |

---

## 3. EVENTS

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 9 | **Visual/functional design match** | ⚠️ PARTIAL | Jazz, History, Restaurant, Storytelling pages exist with detailed designs. **Dance event type has no frontend page** (enum + config + navigation links exist, but no DanceController/view/route — will 404). |
| 10 | **View general event information** (overview pages) | ⚠️ PARTIAL | Overview pages exist for Jazz (`/jazz`), History (`/history`), Restaurant (`/restaurant`), Storytelling (`/storytelling`). **Dance overview page missing.** |
| 11 | **View specific event detail** | ⚠️ PARTIAL | Detail pages for Jazz artists (`/jazz/{slug}`), History locations (`/history/{name}`), Restaurants (`/restaurant/{id}`), Storytelling events (`/storytelling/{slug}`). **No Dance detail page.** |
| 12 | **Purchase tickets/reservations** | ✅ IMPLEMENTED | Users can add events to cart and proceed through Stripe checkout. Full checkout flow with validation, atomic seat reservation, and Stripe session creation. |
| 13 | **Personal program** (after purchase, viewable) | ✅ IMPLEMENTED | Cart/"My Program" at `/my-program` for pre-purchase. Post-purchase order history at `/my-orders` showing all orders with status badges, totals, and ticket PDF download links. |
| 14 | **All-access pass** | ✅ IMPLEMENTED | `PassType`, `PassPurchase`, `PassScope` models in DB. "Add to Program" button on Jazz pricing cards. Pass items flow through cart and checkout, creating `PassPurchase` records linked to `OrderItem`. |
| 15 | **Day pass** | ✅ IMPLEMENTED | Same as above — "Add to Program" on day pass pricing card. Full purchase flow integrated. |
| 16 | **Edit general event info** (CMS) | ✅ IMPLEMENTED | CMS page editor supports editing event overview pages with section-level content editing. |
| 17 | **Manage event entities (CRUD)** | ✅ IMPLEMENTED | Full CRUD for Artists, Restaurants, Events, Sessions, Venues, Pricing, Labels, and Schedule Day visibility via CMS. |
| 18 | **Manage tickets and availabilities** | ✅ IMPLEMENTED | Admin can set capacity/limits per session. Enforced at checkout via `validateItemAvailability()` with atomic seat reservation in `CheckoutService`. Capacity restored on cancel/expiry via `OrderCapacityRestorer`. |

---

## 4. SHOPPING CART

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 19 | **Manage shopping cart** (change quantity, delete items) | ✅ IMPLEMENTED | Full cart with +/- quantity controls, item deletion, cart clearing, donation per item. Works for both anonymous and logged-in users. Dual DOM sync (desktop grid + mobile card). Debounced donation inputs. Supports both event tickets and festival passes. |

---

## 5. ORDERS

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 20 | **Order payment** (iDEAL/Stripe sandbox) | ✅ IMPLEMENTED | Stripe PHP SDK v15.0 with test keys. Webhook-based payment verification with idempotent processing via `StripeWebhookEvent` table. Success page fallback marks order Paid when webhook unavailable. |
| 21 | **Receive tickets via email** (PDF with QR) | ✅ IMPLEMENTED | `TicketFulfillmentService` orchestrates: unique ticket codes, custom QR codes, custom PDF generation, email delivery via PHPMailer. Triggered by webhook and success page fallback. |
| 22 | **Receive invoice via email** (PDF) | ✅ IMPLEMENTED | `InvoiceFulfillmentService` generates invoice number (`INV-YYYYMMDD-XXXXXX`), creates `Invoice` + `InvoiceLine` records, generates PDF via `InvoicePdfGenerator` (extends `BasePdfWriter`), stores as `MediaAsset`, emails via PHPMailer. Triggered by `StripeWebhookHandler` independently from ticket fulfillment. |
| 23 | **Pay later** (24h window) | ✅ IMPLEMENTED | `PayBeforeUtc` set to +24 hours. Retry payment page at `/checkout/retry/{orderId}` creates fresh Stripe session for existing pending orders. Cancel page "Try again" links to retry. Pending orders shown with "Complete Payment" button on `/my-orders`. |
| 24 | **View orders** (CMS admin) | ✅ IMPLEMENTED | Admin order list at `/cms/orders` with status filtering and "View" action links. Detail page at `/cms/orders/{id}` showing order header, line items, payments, and tickets with scan status. |
| 25 | **Export orders** (CSV/Excel with column selection) | ❌ MISSING | No export functionality. No PhpSpreadsheet dependency. |
| 26 | **Ticket scanner** (mobile-friendly, camera QR scan, already-scanned warning) | ✅ IMPLEMENTED | `ScannerController` at `/cms/scanner` accessible to Employee + Administrator roles. `html5-qrcode` camera scanning with manual input fallback. `ScannerService` orchestrates find → validate → mark scanned. Three result states: valid (green), already scanned (amber with timestamp), not found (red). Atomic `WHERE IsScanned = 0` prevents double-scanning. |

---

## 6. IMPORTANT REQUIREMENTS & ADDITIONAL FEATURES

| # | Requirement | Status | Notes |
|---|------------|--------|-------|
| 27 | **WYSIWYG editor on HTML fields** | ✅ IMPLEMENTED | TinyMCE 6 (CDN) integrated for HTML content items in CMS page editor and entity forms (artists, restaurants). Plugins: lists, link, image. |
| 28 | **Image upload in WYSIWYG editor** | ✅ IMPLEMENTED | TinyMCE `images_upload_handler` uploads to CMS endpoint with CSRF token. Supports toolbar button and drag-and-drop. Server-side validation (type, size, dimensions). |
| 29 | **Featured image management** | ✅ IMPLEMENTED | Media library at `/cms/media` with drag-and-drop upload, image grid, delete functionality. Events/Artists/Restaurants have `FeaturedImageAssetId`. Separate image picker in CMS forms. |
| 30 | **All pages are dynamic** (no static content) | ✅ IMPLEMENTED | All page content loaded from DB via `CmsContentRepository`. Views are structural templates; text comes from database. Each page type has dedicated service for data loading. |
| 31 | **Social media sharing** | ✅ IMPLEMENTED | Share buttons (Facebook, X, WhatsApp) on jazz artist detail and storytelling detail pages via reusable `_social-share.php` partial. Secure links with `noopener noreferrer`. |
| 32 | **Ticket availability enforcement** | ✅ IMPLEMENTED | `CheckoutService::validateItemAvailability()` checks quantity vs available seats. Atomic `decrementCapacity()` in DB transaction prevents overselling. Capacity restored on cancel/expiry via `OrderCapacityRestorer`. |
| 33 | **Sold-out prevention** | ✅ IMPLEMENTED | Checkout blocked when `getAvailableSeats() <= 0`. Atomic WHERE guard in `decrementCapacity()` prevents going below zero under concurrency. |
| 34 | **90% single-ticket cap** | ✅ IMPLEMENTED | `CheckoutConstraints::SINGLE_TICKET_CAPACITY_RATIO = 0.9` enforced in `validateItemAvailability()`. Remaining 10% reserved for pass holders. |
| 35 | **Payment status verification** | ✅ IMPLEMENTED | Stripe webhook verifies payment status. Idempotent processing via `StripeWebhookEvent` deduplication table. Success page fallback also transitions status. |
| 36 | **Invoice required fields** | ✅ IMPLEMENTED | `Invoice` model with all required fields (invoice number, date, client info, subtotals, VAT, total). `InvoiceLine` model for line items. PDF generated with all fields. |
| 37 | **Ticket required fields** | ✅ IMPLEMENTED | `Ticket` model has `ticketCode` (unique, `HF-<BASE32>`), scanning fields, `pdfAssetId` linking generated PDF. Tickets created with secure random codes via `TicketCodeGenerator`. |
| 38 | **QR code is secure** (not guessable) | ✅ IMPLEMENTED | Custom `QrCodeGenerator` encodes ticket codes derived from 10 cryptographically random bytes. Base32 encoding with confusable-character exclusion. QR embedded in PDF tickets as 184x184 bitmap. |
| 39 | **SQL injection prevention** | ✅ IMPLEMENTED | All queries use PDO prepared statements with named parameters via `BaseRepository::execute()`. `ATTR_EMULATE_PREPARES => false`. No raw query construction found. |
| 40 | **Code injection prevention** | ✅ IMPLEMENTED | Input sanitization with `trim()`, `strip_tags()`. Type casting for numerics. `declare(strict_types=1)` on all PHP files. Validation helpers centralized. |
| 41 | **XSS prevention** | ✅ IMPLEMENTED | `htmlspecialchars()` / `htmlentities()` on all output across 92+ view files. `CmsOutputHelper::text()` for escaped output, `::html()` for controlled HTML with style stripping. TinyMCE limited to safe plugins. |
| 42 | **CSRF prevention** | ✅ IMPLEMENTED | Per-scope CSRF tokens (64-char hex via `random_bytes(32)`) using `hash_equals()` timing-safe comparison. Required on all CMS forms (13+ forms). Validated in `CmsBaseController::validateCsrf()`. |
| 43 | **Bot prevention (captcha)** | ✅ IMPLEMENTED | Google reCAPTCHA v2 on registration form. `CaptchaService` validates server-side via Google siteverify API. Fails closed if Google unreachable. Graceful bypass in development when keys not configured. |
| 44 | **CMS proper UI** | ✅ IMPLEMENTED | Tailwind CSS (CDN) with Lucide icons. Montserrat font via Google Fonts. Clean sidebar navigation, flash messages, form validation displays, modal dialogs, accordion sections, responsive grid layouts. |

---

## Summary

### Per-Section Counts

| Section | ✅ Implemented | ⚠️ Partial | ❌ Missing |
|---------|:-:|:-:|:-:|
| 1. Users | 4 | 0 | 1 |
| 2. General Information | 2 | 0 | 0 |
| 3. Events | 6 | 3 | 0 |
| 4. Shopping Cart | 1 | 0 | 0 |
| 5. Orders | 6 | 0 | 1 |
| 6. Additional Requirements | 18 | 0 | 0 |
| **TOTAL** | **37** | **3** | **2** |

---

## Changes Since Last Audit (2026-03-30 earlier)

| Item | Previous Status | Current Status | What Changed |
|------|:-:|:-:|--------------|
| Invoice PDF + email (#22) | ❌ MISSING | ✅ IMPLEMENTED | `InvoiceFulfillmentService` with `InvoicePdfGenerator` (extends `BasePdfWriter`), triggered by `StripeWebhookHandler` independently. |
| Invoice required fields (#36) | ⚠️ PARTIAL | ✅ IMPLEMENTED | `Invoice` + `InvoiceLine` models, repository, PDF generator, email delivery. |
| Ticket scanner (#26) | ⚠️ PARTIAL | ✅ IMPLEMENTED | `ScannerController` + `ScannerService` + html5-qrcode camera UI at `/cms/scanner`. |
| Pay later (#23) | ⚠️ PARTIAL | ✅ IMPLEMENTED | Retry payment at `/checkout/retry/{orderId}` with fresh Stripe session. |
| All-access pass (#14) | ⚠️ PARTIAL | ✅ IMPLEMENTED | "Add to Program" buttons on Jazz pricing cards, full checkout integration. |
| Day pass (#15) | ⚠️ PARTIAL | ✅ IMPLEMENTED | Same as all-access — full purchase flow. |
| Personal program (#13) | ⚠️ PARTIAL | ✅ IMPLEMENTED | Order history at `/my-orders` with ticket PDF downloads. |
| CMS order detail (#24) | ✅ (list only) | ✅ IMPLEMENTED | Detail page at `/cms/orders/{id}` with items, payments, tickets. |

**Net change:** 8 items improved. Total: 37 ✅ / 3 ⚠️ / 2 ❌ (was 30/7/4).

---

## Remaining Missing / Partial Items

| Priority | Item | Status | What's Missing |
|----------|------|--------|----------------|
| Must | Customer account management (#4) | ❌ | No `/account` route, no AccountController, no views for editing email/name/password/picture, no confirmation emails |
| Must | Order export CSV/Excel (#25) | ❌ | No export functionality, no PhpSpreadsheet dependency, no column selection UI |
| Must | Dance event pages (#9, #10, #11) | ⚠️ | Enum + config + nav links exist. No DanceController/view/route (404 if accessed) |
