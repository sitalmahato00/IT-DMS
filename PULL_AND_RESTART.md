Helper: Pull current branch and restart project caches/services

Usage (PowerShell):

```powershell
# Pull current branch from 'origin'
.\git-update-and-restart.ps1

# Pull specific branch and clear Laravel caches + restart queues
.\git-update-and-restart.ps1 -Remote origin -Branch cleanup/push-ready -ClearCaches -RestartQueues

# Also run composer dump and frontend build
.\git-update-and-restart.ps1 -ClearCaches -ComposerDump -NpmBuild
```

Notes:
- The script will stash uncommitted changes and restore them after pull.
- Requires `php` on PATH to run `artisan` commands, `composer` for dump, and `npm` for frontend build.
- Run from the repository root.
