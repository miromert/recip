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
- **Upvote system** — Good recipes rise to the top (upvote-only, no downvotes)
- **Clean print view** — Print any recipe without nav bars, ads, or clutter
- **Structured data** — Every recipe includes JSON-LD for rich Google search results
- **No tracking** — No cookies, no analytics, no third-party scripts
- **Flag/report system** — Community moderation with admin dashboard
- **Full-text search** — Find recipes fast
- **SEO-friendly** — Clean URLs, meta tags, Open Graph support
- **Responsive** — Works on desktop, tablet, and mobile

## Tech Stack

- **Backend:** PHP 8.4 + Laravel 12
- **Frontend:** Tailwind CSS + Alpine.js
- **Database:** MariaDB / MySQL
- **Auth:** Laravel Breeze (Blade stack)
- **Build:** Vite

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MariaDB 10.6+ or MySQL 8.0+

## Installation

```bash
# Clone the repo
git clone https://github.com/YOUR_USERNAME/recip.git
cd recip

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure your database in .env
# DB_CONNECTION=mariadb
# DB_DATABASE=recip
# DB_USERNAME=your_user
# DB_PASSWORD=your_password

# Run migrations and seed
php artisan migrate --seed

# Create storage symlink (for recipe images)
php artisan storage:link

# Build frontend assets
npm run build

# Start the dev server
php artisan serve
```

Visit `http://localhost:8000` and start cooking.

## Default Admin Account

After seeding, an admin account is created:

- **Email:** admin@recip.cooking
- **Password:** password

Change this immediately in production.

## Development

```bash
# Run the dev server with hot reload
npm run dev       # In one terminal
php artisan serve # In another terminal
```

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
│       └── AdminMiddleware.php
├── Models/
│   ├── Recipe.php
│   ├── Ingredient.php
│   ├── Step.php
│   ├── Category.php
│   ├── Tag.php
│   ├── Vote.php
│   └── Report.php
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
