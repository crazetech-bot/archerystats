---
name: clear-cache
description: Clear all Laravel caches on the production server
user-invocable: true
---

# Clear Server Caches

Clear all Laravel caches on sportdns.com using MCP SSH.

## Steps

Use MCP SSH (`mcp__mcp-ssh__exec`) to run:

```
cd /home/mfazil/public_html/laravel && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:cache
```

Confirm each cache was cleared successfully.

## Important — cache views, do not clear them

This is a **live** server. `php artisan view:clear` causes intermittent HTTP 500s
(`filemtime(): stat failed for .../storage/framework/views/<hash>.php`) because a
request can hit a compiled view in the instant between deletion and recompilation.
Always finish with `php artisan view:cache` (precompiles every view, so none is ever
missing) and run it **last** — never leave the views in a cleared state.
