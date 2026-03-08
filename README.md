# Recip

**Less reading, more cooking.**

Recip is an open-source, community-driven recipe website built for people who just want to cook. No 2,000-word preambles about grandmother's kitchen. No cookie-tracking. No dark patterns. Just clean, well-formatted recipes with easy metric/imperial conversion.

## Why?

Every recipe website has the same problems:

- A 12-paragraph essay before the actual recipe
- Imperial units with no conversion
- Cookie consent banners, pop-ups, and newsletter modals
- Slow, ad-heavy pages that take forever to load

Recip fixes all of that.

## Features

- **Instant unit conversion** — Toggle between metric and imperial with one click (stored in localStorage, no account needed)
- **Community recipes** — Anyone can sign up and publish recipes immediately
- **Email verification** — New accounts must verify their email before posting
- **Cloudflare Turnstile** — Privacy-preserving bot protection on registration (no cookies)
- **Upvote system** — Good recipes rise to the top (upvote-only, no downvotes)
- **Clean print view** — Print any recipe without nav bars, ads, or clutter
- **Structured data** — Every recipe includes JSON-LD for rich Google search results
- **No tracking** — No cookies, no analytics, no third-party scripts
- **Flag/report system** — Community moderation with admin dashboard
- **Full-text search** — Find recipes fast
- **SEO-friendly** — Clean URLs, meta tags, Open Graph support
- **Responsive** — Works on desktop, tablet, and mobile
- **Docker-ready** — One command to deploy with Cloudflare Tunnel

## Tech Stack

- **Backend:** PHP 8.4 + Laravel 12
- **Frontend:** Tailwind CSS + Alpine.js
- **Database:** MariaDB 11
- **Auth:** Laravel Breeze (Blade stack) + email verification
- **Build:** Vite
- **Deploy:** Docker + Nginx + Cloudflare Tunnel

---

## Quick Start (Local Development)

### Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MariaDB 10.6+ or MySQL 8.0+

### Setup

```bash
git clone https://github.com/miromert/recip.git
cd recip

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configure database in .env, then:
php artisan migrate --seed
php artisan storage:link
npm run build

php artisan serve
```

Visit `http://localhost:8000` and start cooking.

### Development (hot reload)

```bash
npm run dev       # Terminal 1
php artisan serve # Terminal 2
```

---

## Self-Hosting with Docker

Recip ships with a production-ready Docker setup: **Nginx + PHP-FPM + MariaDB + Cloudflare Tunnel** — all behind a single `docker compose` command.

### Prerequisites

- Docker & Docker Compose
- A Cloudflare account (free tier works)
- A domain pointed to Cloudflare DNS

### 1. Configure

```bash
cp .env.production.example .env
```

Edit `.env` and fill in all `CHANGE_ME` values:

| Variable | What to set |
|---|---|
| `APP_KEY` | Run `docker compose run --rm app php artisan key:generate --show` |
| `APP_URL` | `https://yourdomain.com` |
| `DB_PASSWORD` | A strong random password |
| `DB_ROOT_PASSWORD` | A different strong random password |
| `MAIL_HOST` | Your SMTP host (see Mail section below) |
| `MAIL_PASSWORD` | Your SMTP password / API key |
| `TURNSTILE_SITE_KEY` | From Cloudflare Dashboard → Turnstile |
| `TURNSTILE_SECRET_KEY` | From Cloudflare Dashboard → Turnstile |
| `TUNNEL_TOKEN` | From Cloudflare Dashboard → Zero Trust → Tunnels |

### 2. Deploy

```bash
# Build and start all containers
docker compose --profile production up -d

# Seed the database (first time only)
docker compose exec app php artisan db:seed
```

The app runs on port **3080** internally. With the `production` profile, Cloudflare Tunnel handles public access — no ports need to be exposed to the internet.

### 3. Cloudflare Tunnel Setup

1. Go to [Cloudflare Zero Trust](https://one.dash.cloudflare.com/) → **Networks** → **Tunnels**
2. Create a tunnel, copy the token into `TUNNEL_TOKEN` in `.env`
3. Add a **public hostname** pointing your domain to `http://nginx:80`
4. DNS is managed automatically by Cloudflare

### 4. Updating

```bash
git pull
docker compose build
docker compose --profile production up -d
# Migrations run automatically on container start
```

---

## Mail Configuration

Mail is configured via plain SMTP env vars — **zero code changes** to switch providers:

| Provider | `MAIL_HOST` | `MAIL_PORT` | `MAIL_SCHEME` | `MAIL_USERNAME` | `MAIL_PASSWORD` |
|---|---|---|---|---|---|
| **Resend** | `smtp.resend.com` | `465` | `tls` | `resend` | `re_xxxxx` |
| **Mailgun** | `smtp.mailgun.org` | `587` | `tls` | your username | your password |
| **Self-hosted** | `mail.yourdomain.com` | `587` | `null` | your username | your password |

To switch providers, change those 5 env vars and restart:

```bash
docker compose restart app
```

---

## Default Admin Account

After seeding, an admin account is created:

- **Email:** admin@recip.cooking
- **Password:** password

Change this immediately in production.

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── RecipeController.php      # Recipe CRUD
│   │   ├── VoteController.php        # Upvote toggle
│   │   ├── ReportController.php      # Flag recipes
│   │   ├── SearchController.php      # Full-text search
│   │   ├── UserProfileController.php # Public profiles
│   │   └── AdminController.php       # Admin dashboard
│   └── Middleware/
│       ├── AdminMiddleware.php
│       └── SecurityHeadersMiddleware.php
├── Models/
│   ├── Recipe.php
│   ├── Ingredient.php
│   ├── KnownIngredient.php
│   ├── Step.php
│   ├── Category.php
│   ├── Tag.php
│   ├── Vote.php
│   └── Report.php
docker/
├── nginx.conf                    # Nginx virtual host
├── php.ini                       # Production PHP settings
└── entrypoint.sh                 # Container startup script
resources/
├── views/
│   ├── recipes/          # Index, show, create, edit
│   ├── search/           # Search results
│   ├── users/            # Public profiles
│   ├── admin/            # Admin dashboard
│   ├── pages/            # About, privacy
│   ├── components/       # Recipe card
│   └── layouts/          # App layout, navigation
├── js/app.js             # Alpine.js components (unit conversion, voting, forms)
└── css/app.css           # Print stylesheet
```

## Unit Conversion

The unit conversion system runs entirely client-side using Alpine.js and localStorage. Supported conversions:

| Metric     | Imperial              |
|------------|-----------------------|
| ml         | cups, fl oz, tsp, tbsp |
| g          | oz, lb                |
| kg         | lb                    |
| L          | cups                  |

Default unit system is metric. Users can toggle anytime — no account required.

## Security

- **Email verification** required before posting
- **Cloudflare Turnstile** CAPTCHA on registration (privacy-preserving, no tracking cookies)
- **Honeypot field** catches basic bots
- **Rate limiting** on login (10/min), registration (5/min), password reset (5/min)
- **Security headers** — X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy
- **HTTPS enforced** in production
- **Encrypted sessions** in production
- **No tracking** — zero analytics, zero third-party scripts

## Contributing

1. Fork the repo
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please keep the spirit of the project:

- **No bloat** — Every feature should make recipes easier to find, read, or cook
- **No tracking** — Respect user privacy
- **Keep it fast** — Pages should load quickly

## License

This project is open source under the [MIT License](LICENSE).
