# Archery Stats

Web application for tracking archer performance, scoring, and equipment.
Coaches use a dashboard to review data. Mobile-accessible via PWA.

## Stack
- **Backend**: Laravel 11.48.0 (PHP 8.3.30)
- **Frontend**: Alpine.js 3.x + Tailwind CSS (CDN) + Blade templates
- **Database**: MySQL — `DB_HOST=127.0.0.1` (not localhost)
- **Charts**: Chart.js (future)
- **Mobile**: PWA (future)

## Conventions
- Controllers: one per resource, RESTful methods only
- Models: use Eloquent relationships, no raw SQL
- Views: Blade templates, Tailwind utility classes only (no custom CSS unless unavoidable)
- JavaScript: Alpine.js for interactivity, vanilla JS for Chart.js integration
- Routes: `web.php` for browser routes, `api.php` for JSON endpoints
- Roles enforced via `RoleMiddleware` on route groups
- **Always declare static routes (e.g. `/archers/create`) before parameterised routes (`/archers/{archer}`)**

## Design System
- Layout: fixed sidebar (indigo gradient) + main content area
- Cards: `rounded-2xl shadow-sm border border-gray-100`
- Section headers: coloured gradient strip with icon
- Buttons: gradient `style="background: linear-gradient(135deg, #4338ca, #6366f1)"`
- Inputs: `rounded-xl border border-gray-300 bg-gray-50` with focus ring
- No build step — Tailwind CDN + Alpine.js CDN

## User Roles (hierarchy)
`super_admin` > `club_admin` > `coach` > `archer` > `guest`

- `super_admin` — full access including delete
- `club_admin` — create, edit, view
- `coach` — view only
- `archer` / `guest` — no access to admin screens

## Database
MySQL. Run migrations: `php artisan migrate`
Seed demo data: `php artisan db:seed`

## Deployment (cPanel — sportdns.com)
- Installed via Softaculous — Laravel already set up on server
- Remote path: `/home/mfazil/public_html/laravel`
- Upload files: `scp -i "C:/Users/craze/.ssh/claudecode" <file> mfazil@sportdns.com:<remote_path>`
- SSH port: 22 (key is passphrase protected)
- After upload: `php artisan view:clear && php artisan cache:clear`
- `DB_HOST` must be `127.0.0.1` in `.env`
- Photos stored in `storage/app/public/archers/` — served via `storage:link`

## Key Directories
- `app/Models/` — Eloquent models
- `app/Http/Controllers/` — Controllers
- `app/Http/Controllers/Auth/LoginController.php` — Manual login/logout
- `app/Http/Middleware/RoleMiddleware.php` — Role-based access
- `resources/views/layouts/app.blade.php` — Main sidebar layout
- `resources/views/auth/` — Login page
- `resources/views/archers/` — Archer views
- `database/migrations/` — DB schema
- `database/seeders/` — Demo/reference data

## Modules

### Module 1: Personal Information ✅
Archer CRUD — fully built and live.

**Fields:** Ref No (auto: ARCH-00001), Full Name, Date of Birth, Age (auto), Gender, Team, State, Country (default: Malaysia), Email, Address / Postcode / Address State, Division (multi-select: Recurve, Compound, Barebow, Traditional), Photo (bmp/jpg/jpeg/webp)

**Key files:**
- `app/Models/Archer.php` — ref_no boot hook, MALAYSIAN_STATES, DIVISIONS constants, divisions cast as array
- `app/Http/Controllers/ArcherController.php` — CRUD + inline club creation via `resolveClub()`
- `resources/views/archers/` — index, create, edit, show, _form partial
- `database/migrations/2024_01_01_000009_update_archers_table_personal_info.php`

**Login:** admin@archery.my / password
