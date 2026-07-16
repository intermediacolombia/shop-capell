# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

- **Backend:** Vanilla PHP 8.0+ (no framework), PDO for DB access
- **Frontend:** Bootstrap 5.3, jQuery, Owl Carousel — no build step (no npm)
- **Database:** MySQL 8.0 (33 tables)
- **Payments:** Mercado Pago SDK (`mercadopago/dx-php`)
- **Email:** PHPMailer
- **Server:** Apache + mod_rewrite, cPanel (ea-php80)

## Setup

```bash
composer install
mysql -u txcfsrrf_shop -p txcfsrrf_shop < txcfsrrf_shop.sql
# Edit inc/config.php with DB credentials and API keys
```

No build process. Deploy to Apache webroot and configure mod_rewrite.

## Routing

`.htaccess` rewrites `/page/slug` → `index.php?page=page&slug=slug`, which loads `template/page.php`.  
`/actions/`, `/admin/`, `/public/` are bypassed by rewrite rules and accessed directly.

## Key Directories

| Path | Purpose |
|------|---------|
| `inc/config.php` | DB connection, constants, loads `system_settings` from DB into `$GLOBALS['SYS_SETTINGS']` |
| `inc/cart_functions.php` | Cart calculation logic |
| `template/` | Frontend pages + partials (`inc/header.php`, `inc/footer.php`) |
| `actions/` | AJAX/form handlers — business logic endpoints |
| `admin/` | Admin dashboard, own routing via `admin/login/session.php` auth middleware |
| `mailer/` | PHPMailer email templates |
| `ws_api/` | WhatsApp notification endpoints |
| `cron/` | Scheduled jobs dispatcher (`cron/cron.php`) — schedule via server cron |

## Checkout Flow

1. `template/checkout.php` — form submission via JS POST
2. `actions/checkout_process.php` — validates stock, shipping, coupons; creates order in DB
3. `actions/mp_create_preference.php` — creates Mercado Pago payment preference
4. `actions/mp_webhook.php` — payment callback; updates order status, triggers email + WhatsApp

## Configuration

Dynamic settings live in the `system_settings` DB table (Mercado Pago keys, SMTP, SEO, free shipping threshold). Static DB credentials are hardcoded in `inc/config.php`. `URLBASE` is `https://www.capellb5.com`.

## Admin

- Login: `/admin/login/`
- Auth guard: `admin/login/session.php` (included at top of every admin page)
- Timezone: `America/Bogota`
