<#
Pulls the current branch from a remote, then optionally clears Laravel caches,
restarts queue workers, runs composer dump, and builds frontend assets.

Usage:
  .\git-update-and-restart.ps1                      # pulls from 'origin' current branch
  .\git-update-and-restart.ps1 -Remote origin -Branch main -ClearCaches -RestartQueues -ComposerDump -NpmBuild
#>
Param(
    [string]$Remote = 'origin',
    [string]$Branch = '',
    [switch]$ClearCaches = $false,
    [switch]$RestartQueues = $false,
    [switch]$ComposerDump = $false,
    [switch]$NpmBuild = $false
)

$ErrorActionPreference = 'Stop'

# Ensure we're inside a git repo
if (-not (git rev-parse --is-inside-work-tree 2>$null)) {
    Write-Error "Not a git repository. Run this script from a repo root."
    exit 1
}

if ([string]::IsNullOrWhiteSpace($Branch)) {
    $Branch = (git rev-parse --abbrev-ref HEAD).Trim()
}

Write-Host "Pulling from remote '$Remote' branch '$Branch'..."

# Stash changes if present
$status = git status --porcelain
$stashed = $false
if ($status) {
    Write-Host "Uncommitted changes detected — stashing before pull."
    git stash push -u -m "autostash from git-update-and-restart" | Out-Null
    $stashed = $true
}

git fetch $Remote

try {
    git pull --rebase $Remote $Branch
} catch {
    Write-Error "git pull failed: $_"
    if ($stashed) {
        Write-Host "Attempting to pop stash after failure..."
        git stash pop | Out-Null
    }
    exit 1
}

if ($stashed) {
    Write-Host "Restoring stashed changes..."
    git stash pop | Out-Null
}

Write-Host "Pull complete."

# Laravel cache clearing and maintenance
if ($ClearCaches) {
    if (Get-Command php -ErrorAction SilentlyContinue) {
        Write-Host "Clearing Laravel caches..."
        php artisan view:clear
        php artisan route:clear
        php artisan config:clear
        php artisan cache:clear
        php artisan optimize:clear
        if ($RestartQueues) {
            Write-Host "Restarting queue workers..."
            php artisan queue:restart
        }
    } else {
        Write-Warning "PHP not found on PATH; skipping artisan cache commands."
    }
}

if ($ComposerDump) {
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        Write-Host "Running 'composer dump-autoload -o'..."
        composer dump-autoload -o
    } else {
        Write-Warning "Composer not found on PATH; skipping composer dump."
    }
}

if ($NpmBuild) {
    if (Test-Path package.json) {
        if (Get-Command npm -ErrorAction SilentlyContinue) {
            Write-Host "Running npm install and build..."
            npm ci
            npm run build
        } else {
            Write-Warning "npm not found on PATH; skipping npm build."
        }
    } else {
        Write-Warning "package.json not found; skipping npm build."
    }
}

Write-Host "All requested post-pull tasks complete."
