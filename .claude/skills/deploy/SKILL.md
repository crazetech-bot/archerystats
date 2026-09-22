---
name: deploy
description: Deploy latest changes to sportdns.com server
user-invocable: true
---

# Deploy to Production

Deploy the ArcheryStats project to sportdns.com. The server checkout at
`/home/mfazil/public_html/laravel` is a git clone of `origin/main` (GitHub,
token embedded in the remote URL), so a deploy is a **fetch + reset on the
server**, not a file upload.

## Steps

All remote commands run through MCP SSH (`mcp__mcp-ssh__run-command`).

1. **Local preflight** — `git status` must be clean and `main` must be pushed:
   ```
   git status --short && git log --oneline -1 && git fetch --dry-run
   ```
   Warn about uncommitted changes; nothing uncommitted ever reaches the server.

2. **Compare heads** — on the server:
   ```
   cd /home/mfazil/public_html/laravel && git fetch origin main && git log --oneline -1 HEAD && git log --oneline -1 origin/main
   ```
   If they match, there is nothing to deploy.

3. **Dry run** — list exactly what the reset will overwrite, including files
   that are untracked on the server but tracked in git:
   ```
   cd /home/mfazil/public_html/laravel && git -c core.safecrlf=false add -A . ; git diff --ignore-cr-at-eol --diff-filter=M --stat origin/main ; git reset -q
   ```
   Expected modified files: only `public/.htaccess` (see below). Anything else
   listed as modified is a server-side edit that was never committed — stop and
   pull it into the repo first (compare with `git diff --ignore-cr-at-eol` on
   both sides) rather than overwrite it.

4. **Confirm with the user**, then reset and restore the cPanel block:
   ```
   cd /home/mfazil/public_html/laravel && cp public/.htaccess /home/mfazil/htaccess.cpanel.bak && git reset --hard origin/main && cp /home/mfazil/htaccess.cpanel.bak public/.htaccess && grep -c ea-php83 public/.htaccess
   ```
   The grep must print `2`.

5. **Migrations** (safe to run every time):
   ```
   cd /home/mfazil/public_html/laravel && php artisan migrate --force
   ```

6. **Composer** — only if `composer.json` or `composer.lock` changed:
   ```
   cd /home/mfazil/public_html/laravel && php composer.phar install --no-dev --no-interaction --optimize-autoloader
   ```

7. **Caches** — clear app, config and route caches, then precompile views.
   `view:cache` must run last and `view:clear` must never run (see below):
   ```
   cd /home/mfazil/public_html/laravel && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:cache
   ```

8. **Verify**:
   ```
   cd /home/mfazil/public_html/laravel && php artisan --version && git log --oneline -1 && for p in /login /manual /register; do printf '%s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' -m 20 https://sportdns.com$p)"; done && grep -c "^\[$(date +%Y-%m-%d)" storage/logs/laravel.log
   ```
   Expect 200 on all three URLs and no new Laravel log entries for today.

## Important

- **Always confirm with the user before step 4.** `git reset --hard` is the
  deploy; there is no separate upload step.
- **`public/.htaccess` is special.** The server copy ends with a cPanel-generated
  `ea-php83` AddHandler block that is not in git. It is marked
  `git update-index --skip-worktree` and backed up at
  `/home/mfazil/htaccess.cpanel.bak`, but a reset can still overwrite it, so
  always restore it as in step 4. Never commit the cPanel block.
- **Never run `git clean` on the server.** Untracked files that must survive:
  `composer.phar`, `docs/`, `cache/`, `error_log`, `.env`, `storage/`, `vendor/`.
- **Views: cache, never clear.** On this live server `php artisan view:clear`
  causes intermittent HTTP 500s (`filemtime(): stat failed for
  .../storage/framework/views/<hash>.php`) — a request hits a compiled view in the
  gap between deletion and recompilation. End the cache step with `view:cache`
  (precompiles all views) and run it last so none is ever missing.
- Composer on server: `php composer.phar` (not in PATH).
- User has no local MySQL or `vendor/` — nothing can be tested locally, so
  deploy is the preview. Check the URLs in step 8 after every deploy.
- Hotfixes must go through git (commit, push, deploy). Editing files directly
  on the server recreates the drift this workflow replaced; if it happens
  anyway, pull the change into the repo before the next deploy (step 3 will
  flag it).
