# Haarlem Festival

A full-stack festival ticketing and content management platform built with PHP, MariaDB, Nginx, and Stripe. Visitors can browse events, reserve restaurants, purchase tickets, and manage their festival programme. Staff manage all content, orders, and scan tickets via a built-in CMS.

---

## Team

| Name | Student Number |
|---|---|
| Daria Gakhova | 727546 |
| Kian Khatibi | 726925 |
| Leandro Nuñez | 731427 |
| Luwana da Conceição | 728287 |
| Matheus Fracasso | 725283 |

---

## Table of Contents

1. [Quick Start — Docker](#1-quick-start--docker)
2. [Stripe Setup](#2-stripe-setup)
3. [Accessing the Application](#3-accessing-the-application)
4. [Demo Login Credentials](#4-demo-login-credentials)
5. [Feature Overview](#5-feature-overview)
6. [Architecture](#6-architecture)
7. [Environment Variables](#7-environment-variables)

---

## 1. Quick Start — Docker

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

### Steps

**1. Extract the zip**

Unzip the provided archive and open a terminal inside the `haarlem-festival` folder.

**2. Copy the environment file**

```bash
cp .env.example .env
```

Edit `.env` with your own values (see [Environment Variables](#7-environment-variables)). The defaults in `.env.example` will work for a local setup — you only need to add your own Stripe keys and mail credentials.

**3. Start all containers**

```bash
docker compose up -d
```

This starts four services:

| Service | Description | Port |
|---|---|---|
| `nginx` | Web server / reverse proxy | `80` |
| `php` | PHP-FPM application (auto-runs `composer install` on first start) | — |
| `mysql` | MariaDB 12 database | `3306` |
| `phpmyadmin` | Database GUI | `8080` |

**4. Import the database**

On first run the database is empty. Import the provided SQL dump:

```bash
docker exec -i haarlem-festival-mysql-1 mariadb -u developer -pSecret123@ haarlem_festival_db < Database/haarlem_festival_db.sql
```

**5. Open the application**

```
http://localhost
```

### Useful Docker commands

```bash
# View running containers
docker compose ps

# View PHP application logs
docker compose logs php -f

# Restart a specific service
docker compose restart php

# Stop all containers
docker compose down

# Stop and remove all data (including the database volume)
docker compose down -v
```

---

## 2. Stripe Setup

Payments use [Stripe Checkout](https://stripe.com/docs/payments/checkout). The repository ships with test keys that work out-of-the-box in development.

### Environment keys (already in `.env`)

```dotenv
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### Forwarding webhooks locally

Stripe needs to reach your local machine when a payment completes. Use the [Stripe CLI](https://stripe.com/docs/stripe-cli):

**1. Install the Stripe CLI**

```bash
# macOS
brew install stripe/stripe-cli/stripe

# Windows (Scoop)
scoop bucket add stripe https://github.com/stripe/scoop-stripe-cli.git
scoop install stripe
```

**2. Log in**

```bash
stripe login
```

**3. Forward events to your local server**

```bash
stripe listen --forward-to http://localhost/api/stripe/webhook
```

The CLI prints a new webhook signing secret — copy it into your `.env`:

```dotenv
STRIPE_WEBHOOK_SECRET=whsec_<the-value-printed-by-the-cli>
```

**4. Test card numbers**

| Scenario | Card Number | Expiry | CVC |
|---|---|---|---|
| Successful payment | `4242 4242 4242 4242` | Any future date | Any 3 digits |
| Card declined | `4000 0000 0000 0002` | Any future date | Any 3 digits |
| Requires authentication | `4000 0025 0000 3155` | Any future date | Any 3 digits |

### Checkout flow

1. User adds tickets/passes to **My Programme** (`/my-program`)
2. Proceeds to **Checkout** (`/checkout`) and fills in recipient details
3. Redirected to Stripe-hosted payment page
4. On success, Stripe fires a `checkout.session.completed` webhook
5. The application marks the order as **Paid**, generates PDF tickets + invoice, and sends them by email
6. User lands on the **Success** page (`/checkout/success`)

A **24-hour pay-later** option is available — pending orders appear in **My Orders** (`/my-orders`) with a "Complete Payment" button.

---

## 3. Accessing the Application

| URL | Description |
|---|---|
| [http://localhost](http://localhost) | Main festival website |
| [http://localhost/cms](http://localhost/cms) | CMS / back-office (admin & employee login) |
| [http://localhost:8080](http://localhost:8080) | phpMyAdmin database GUI |

### phpMyAdmin credentials

| Field | Value |
|---|---|
| Server | `mysql` (auto-filled) |
| Username | `developer` |
| Password | `Secret123@` |
| Database | `haarlem_festival_db` |

Root access (full privileges):

| Field | Value |
|---|---|
| Username | `root` |
| Password | `Secret123@` |

---

## 4. Demo Login Credentials

The seeded database includes three demo accounts, one per role:

| Role | Username | Password | Access |
|---|---|---|---|
| **Administrator** | `administrator` | `Admin123!` | Full CMS + front-end |
| **Employee** | `employee` | `Password123!` | Ticket scanner + front-end |
| **Customer** | `customer` | `User123!` | Front-end only |

Login pages:

- Front-end: [http://localhost/login](http://localhost/login)
- CMS: [http://localhost/cms/login](http://localhost/cms/login)

---

## 5. Feature Overview

### Public Website

#### Home
- Dynamic hero section, event showcase, and location map loaded from the CMS database
- Schedule preview widget for upcoming events

#### Jazz
- Artist listing page (`/jazz`) with filterable performance schedule
- Artist detail pages (`/jazz/{slug}`) with biography, photo gallery, albums, tracks, and highlight quotes
- Social sharing buttons (Facebook, X, WhatsApp)

#### History
- History overview page (`/history`) with guided city walk details and filterable schedule
- Historical location detail pages (`/history/{name}`) with descriptions, highlights, and gallery images
- Multi-language tour support (NL / EN) with per-language guide assignments

#### Storytelling
- Storytelling overview page (`/storytelling`) with event cards and schedule
- Storytelling detail pages (`/storytelling/{slug}`) with rich content, gallery, and ticket options
- Social sharing buttons

#### Restaurant
- Restaurant overview page (`/restaurant`) listing all participating restaurants
- Restaurant detail pages (`/restaurant/{slug}`) with cuisine info, map embed, opening times, and star rating
- Table reservation form — collects date, time slot, party size, and special requests

#### Schedule
- Live schedule API (`/api/schedule/days`, `/api/schedule/sessions`) consumed by all event pages
- Day-of-week visibility configurable per event type from the CMS

---

### User Accounts

#### Registration & Login
- Register with username, email, first/last name, and password
- Google reCAPTCHA v2 on the registration form to prevent bots
- Login accepts either username or email
- Argon2id password hashing with unique salts

#### Password Reset
- Request a reset link via email (anti-enumeration: always shows success message)
- Cryptographically secure single-use tokens with 1-hour expiry
- SHA-256 hashing of tokens before DB storage

#### Email Confirmation
- Confirmation email sent on registration
- Token-based verification flow

#### Account Management
- Edit first name, last name, and email
- Upload a profile picture
- Change password (current password verification required)

---

### My Programme (Shopping Cart)

- Works for both anonymous and logged-in visitors (session-based for guests)
- Add event session tickets with quantity and price tier (Adult / Child / Family)
- Add festival passes (Day Pass, All-Access) with a valid date
- Add restaurant reservations
- Adjust quantity with live total recalculation
- Optional pay-what-you-like donation per item
- Remove individual items or clear the entire cart
- 90% single-ticket capacity cap enforced (10% reserved for pass holders)

---

### Checkout & Payment

- Stripe Checkout hosted payment page (iDEAL, card, and other Stripe-supported methods)
- Recipient details collected (first name, last name, email)
- Atomic seat reservation prevents overselling under concurrent requests
- Webhook-based payment verification with idempotent deduplication
- Success page fallback in case the webhook is delayed

#### After successful payment
- Order status updated to **Paid**
- Individual PDF tickets generated with unique QR codes (cryptographically secure, Base32-encoded)
- PDF invoice generated with all required fields (invoice number, VAT breakdown, line items)
- Tickets and invoice emailed to the recipient via SMTP

#### Pay later
- Pending orders held for 24 hours (`PayBeforeUtc`)
- Retry payment page creates a fresh Stripe session for existing pending orders
- Expired orders automatically released (seats restored)

---

### Order History

- `/my-orders` — list of all orders with status badges and totals
- Order detail view with individual ticket PDFs available for download
- Pending orders show a "Complete Payment" button

---

### Ticket Scanner (Employee / Admin)

- Accessible at `/cms/scanner` for Employee and Administrator roles
- Camera-based QR code scanning via `html5-qrcode`
- Manual ticket code input as fallback (useful for physical scanners with barcode readers)
- Three result states:
  - **Valid** (green) — ticket accepted and marked as scanned
  - **Already scanned** (amber) — shows original scan timestamp
  - **Not found** (red) — invalid or unknown code
- Atomic database guard prevents the same ticket being scanned twice under concurrency

---

### CMS / Back-Office

All CMS features require login at `/cms/login`.

#### Dashboard
- Summary statistics (total orders, revenue, upcoming events, recent activity)

#### Content Management (Page Editor)
- Edit all text, HTML, and image content on every public page (home, jazz, history, storytelling, restaurant) without touching code
- TinyMCE 6 WYSIWYG editor for HTML fields with image upload, lists, and link support
- Section-by-section structure (`CmsPage → CmsSection → CmsItem`)

#### Media Library
- Upload images and files via drag-and-drop
- Browse, preview, and delete media assets
- Images used in TinyMCE, event/artist/restaurant featured images, and CMS items

#### Artist Management
- Full CRUD for Jazz artists
- Rich profile editing: biography (WYSIWYG), albums, tracks, gallery images, lineup members, highlights
- Toggle visibility on the Jazz overview page
- Drag-and-drop sort order

#### Event Management
- Full CRUD for events across all event types
- Per-event sessions with start/end datetime, capacity, price tiers, labels, language code, age restrictions
- Session pricing matrix (Adult, Child, Family, Reservation Fee, Pay What You Like)
- Session discount rules
- Session labels (e.g., "Sold Out", "In Dutch", "Outdoor")

#### Venue Management
- Create and manage physical venue locations used by events and sessions

#### User Management (Admin only)
- Full CRUD for user accounts with search, filter by role, and sort
- Assign roles: Customer, Employee, Administrator
- Toggle account active/inactive

#### Order Management
- List all orders with status filter (Pending / Paid / Cancelled / Expired / Refunded)
- Order detail view: line items, payments, and ticket scan status

#### Schedule Configuration
- Toggle day-of-week visibility per event type (e.g., hide Mondays for Jazz)

---

### Security

| Protection | Implementation |
|---|---|
| SQL injection | PDO prepared statements on every query; `ATTR_EMULATE_PREPARES = false` |
| XSS | `htmlspecialchars()` / `htmlentities()` on all output; `CmsOutputHelper` wrapper |
| CSRF | Per-scope 64-character tokens validated with `hash_equals()` on all CMS forms |
| Password storage | Argon2id hashing |
| Password reset tokens | CSPRNG-generated, SHA-256 hashed before storage, single-use, 1-hour expiry |
| QR code security | 10 cryptographically random bytes, Base32-encoded, confusable characters excluded |
| Bot prevention | Google reCAPTCHA v2 on registration |
| Enumeration prevention | Generic error messages on login and password reset |
| Concurrent overselling | Atomic `WHERE SeatsAvailable > 0` guard in `decrementCapacity()` |
| Stripe webhook integrity | Signature verification via `STRIPE_WEBHOOK_SECRET` |

---

## 6. Architecture

```
haarlem-festival/
├── app/
│   ├── public/             # Web root (index.php, assets)
│   ├── bootstrap/          # DI container, route definitions
│   └── src/
│       ├── Controllers/    # 27 controllers (public + CMS + scanner)
│       ├── Services/       # Business logic (38 services)
│       ├── Repositories/   # Data access layer — PDO (30+ repositories)
│       ├── Models/         # Readonly DTO classes (36 models)
│       ├── DTOs/           # Request/response data objects
│       ├── Mappers/        # DTO ↔ ViewModel conversions
│       ├── ViewModels/     # Strongly-typed view data
│       ├── Views/          # PHP + Twig templates
│       ├── Enums/          # Type-safe enumerations
│       ├── Exceptions/     # Domain-specific exceptions
│       └── Helpers/        # Utilities (asset versioning, output escaping)
├── Database/
│   └── Migrations/         # SQL migration files
├── docker-compose.yml
├── PHP.Dockerfile
├── nginx.conf
└── .env
```

**Stack:** PHP 8 (FPM) · MariaDB 12 · Nginx · Docker Compose  
**Key libraries:** Stripe PHP SDK v15 · PHPMailer · chillerlan/php-qrcode · html5-qrcode · TinyMCE 6 · Tailwind CSS · FastRoute

---

## 7. Environment Variables

Copy `.env.example` to `.env` and fill in each value:

```dotenv
# ── Database ────────────────────────────────────────────────────────────────
MYSQL_ROOT_PASSWORD=Secret123@
MYSQL_USER=developer
MYSQL_PASSWORD=Secret123@
MYSQL_DATABASE=haarlem_festival_db

# ── Application ─────────────────────────────────────────────────────────────
APP_URL=http://localhost        # Base URL — used in Stripe redirect URLs
APP_ENV=local                   # local | production

# ── Stripe ──────────────────────────────────────────────────────────────────
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...  # Update after running: stripe listen --forward-to ...

# ── Google reCAPTCHA v2 ──────────────────────────────────────────────────────
RECAPTCHA_SITE_KEY=...
RECAPTCHA_SECRET_KEY=...

# ── SMTP Mail ────────────────────────────────────────────────────────────────
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=you@example.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@haarlemfestival.nl
MAIL_FROM_NAME="Haarlem Festival"
MAIL_ENCRYPTION=tls
MAIL_FORCE_SEND=false           # Set to true to send real emails in development
MAIL_DEBUG=false
```

> **Gmail tip:** Use an [App Password](https://myaccount.google.com/apppasswords) (not your regular Gmail password). Go to Google Account → Security → 2-Step Verification → App passwords.
