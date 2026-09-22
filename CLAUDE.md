# Archery Stats

Web application for tracking archer performance, scoring, and equipment.
Coaches use a dashboard to review data. Mobile-accessible via PWA.

## Stack
- **Backend**: Laravel 11.50.0 (PHP 8.3.30)
- **Frontend**: Alpine.js 3.x + Tailwind CSS (CDN) + Preline UI (CDN) + Blade templates
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
- Layout: fixed sidebar + main content area — colors driven by theme settings (CSS variables)
- Theme: configurable via admin panel (`/admin/settings`) — 8 presets + custom color picker
- CSS variables: `--th-primary`, `--th-primary-hover`, `--th-sidebar`, `--th-sidebar-hover`, `--th-accent`
- Default palette: amber primary (`#f59e0b`), navy sidebar (`#0f172a`)
- Cards: `rounded-2xl shadow-sm border border-gray-100`
- Section headers: coloured gradient strip with icon
- Buttons: `.btn-primary` (theme primary) or `.btn-navy` (theme sidebar)
- Inputs: `rounded-xl border border-gray-300 bg-gray-50` with focus ring
- UI library: Preline UI (CDN) for advanced components
- No build step — Tailwind CDN + Alpine.js CDN + Preline UI CDN

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
- Remote path: `/home/mfazil/public_html/laravel` — a git clone tracking `origin/main`
- **Deploy = git, not scp.** Push `main`, then on the server: `git fetch origin main && git reset --hard origin/main` (use the `/deploy` skill — it dry-runs first and restores `.htaccess`)
- Remote commands: MCP SSH `mcp__mcp-ssh__run-command` (profile `mcp-ssh`)
- Composer on server: `php composer.phar` (not in PATH)
- After deploy: `php artisan migrate --force`, then `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:cache`
- **Never `view:clear` on the live server** — it causes intermittent 500s; `view:cache` last, always
- `public/.htaccess` on the server carries a cPanel `ea-php83` handler block that is not in git (skip-worktree, backup at `/home/mfazil/htaccess.cpanel.bak`) — restore it after any reset
- Never `git clean` on the server: `composer.phar`, `docs/`, `cache/`, `error_log` are untracked and must stay
- Never edit files directly on the server; hotfixes go commit → push → deploy
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
