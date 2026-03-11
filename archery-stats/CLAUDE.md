# Archery Stats

Web application for tracking archer performance, scoring, and equipment.
Coaches use a dashboard to review data. Mobile-accessible via PWA.

## Stack
- **Backend**: Laravel (PHP)
- **Frontend**: Alpine.js + Tailwind CSS + Blade templates
- **Database**: MySQL
- **Charts**: Chart.js
- **Mobile**: PWA (manifest.json + service worker)

## Conventions
- Controllers: one per resource, RESTful methods only
- Models: use Eloquent relationships, no raw SQL
- Views: Blade templates, Tailwind utility classes only (no custom CSS unless unavoidable)
- JavaScript: Alpine.js for interactivity, vanilla JS for Chart.js integration
- Routes: `web.php` for browser routes, `api.php` for JSON endpoints
- Roles enforced via `RoleMiddleware` on route groups

## User Roles (hierarchy)
`super_admin` > `club_admin` > `coach` > `archer` > `guest`

## Database
MySQL. Run migrations: `php artisan migrate`
Seed demo data: `php artisan db:seed`

## Deployment (cPanel VPS)
- Document root must point to `archery-stats/public/`
- `.env` must be configured with MySQL credentials before migrating
- Run `composer install --no-dev` on the server after upload
- Run `npm install && npm run build` on server for compiled assets
- Run `php artisan key:generate` if APP_KEY is empty
- Run `php artisan storage:link` for file uploads

## Key Directories
- `app/Models/` — Eloquent models
- `app/Http/Controllers/` — Controllers
- `app/Http/Middleware/RoleMiddleware.php` — Role-based access
- `resources/views/` — Blade templates
- `database/migrations/` — DB schema
- `database/seeders/` — Demo/reference data
- `public/manifest.json` — PWA manifest
- `public/sw.js` — PWA service worker
