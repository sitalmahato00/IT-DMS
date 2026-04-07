This repository helper runs a `git pull` for the current branch from a specified remote.

Run in PowerShell from the repo root:

```powershell
# default: pulls from 'origin' current branch
.\git-pull-c-panel.ps1

# specify remote and branch
.\git-pull-c-panel.ps1 -Remote origin -Branch main
```

Notes:
- The script will stash uncommitted changes automatically and pop them after the pull.
- It runs `git fetch` then `git pull --rebase`.
