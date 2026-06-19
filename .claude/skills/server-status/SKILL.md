---
name: server-status
description: Check the status of the production server
user-invocable: true
---

# Server Status Check

Check the health of the ArcheryStats production server at sportdns.com.

## Steps

Use MCP SSH (`mcp__mcp-ssh__exec`) to run these checks:

1. **Laravel & PHP version**:
   ```
   cd /home/mfazil/public_html/laravel && php artisan --version && php -v | head -1
   ```

2. **Migration status**:
   ```
   cd /home/mfazil/public_html/laravel && php artisan migrate:status 2>&1 | tail -10
   ```

3. **Storage link**:
   ```
   ls -la /home/mfazil/public_html/laravel/public/storage
   ```

4. **Last deployed commit**:
   ```
   cd /home/mfazil/public_html/laravel && git log --oneline -5
   ```

5. **Disk usage**:
   ```
   du -sh /home/mfazil/public_html/laravel/
   ```

6. **Check for uncommitted changes on server**:
   ```
   cd /home/mfazil/public_html/laravel && git status --short
   ```

## Output
Present results in a summary table comparing local vs server state.
Note: Local has no MySQL — only compare git state and file versions.
