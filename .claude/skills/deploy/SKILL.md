---
name: deploy
description: Deploy latest changes to sportdns.com server
user-invocable: true
---

# Deploy to Production

Deploy the ArcheryStats project to sportdns.com.

## Steps

1. Check `git status` locally — warn if there are uncommitted changes
2. Compare local HEAD with server's last deployed commit:
   - SSH into server: `cd /home/mfazil/public_html/laravel && git log --oneline -1`
   - Compare with local `git log --oneline -1`
3. If already in sync, check for uncommitted file changes and offer to upload those
4. Identify changed files between local and server commits
5. Upload changed files using `scp -i "D:/claude project/.ssh/claudecode" -P 22 <file> mfazil@sportdns.com:/home/mfazil/public_html/laravel/<path>`
6. If any migrations were added, run on server: `cd /home/mfazil/public_html/laravel && php artisan migrate --force`
7. If composer.json changed, run: `cd /home/mfazil/public_html/laravel && php composer.phar update --no-interaction`
8. Clear caches on server (cache views, do not clear them — see note below):
   ```
   cd /home/mfazil/public_html/laravel && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:cache
   ```
9. Verify deployment by checking `php artisan --version` and confirming the site loads

## Important
- Always confirm with the user before uploading files
- Use MCP SSH (`mcp__mcp-ssh__exec`) for remote commands
- Composer on server: `php composer.phar` (not in PATH)
- SSH key: `D:/claude project/.ssh/claudecode` (no passphrase, ed25519)
- Never upload `.env`, `vendor/`, `node_modules/`, or `storage/` directories
- User has no local MySQL — cannot test locally, deploy to preview
- **Views: cache, never clear.** On this live server `php artisan view:clear`
  causes intermittent HTTP 500s (`filemtime(): stat failed for
  .../storage/framework/views/<hash>.php`) — a request hits a compiled view in the
  gap between deletion and recompilation. End the cache step with `view:cache`
  (precompiles all views) and run it last so none is ever missing.
