<#
Pulls the current branch from a remote with automatic stashing for local changes.

Usage:
  .\git-pull-c-panel.ps1
  .\git-pull-c-panel.ps1 -Remote origin -Branch sital
#>
Param(
    [string]$Remote = 'origin',
    [string]$Branch = ''
)

$ErrorActionPreference = 'Stop'

if (-not (git rev-parse --is-inside-work-tree 2>$null)) {
    Write-Error "Not a git repository. Run this script from a repo root."
    exit 1
}

if ([string]::IsNullOrWhiteSpace($Branch)) {
    $Branch = (git rev-parse --abbrev-ref HEAD).Trim()
}

Write-Host "Pulling from remote '$Remote' branch '$Branch'..."

$status = git status --porcelain
$stashed = $false
if ($status) {
    Write-Host "Uncommitted changes detected; stashing before pull."
    git stash push -u -m "autostash from git-pull-c-panel" | Out-Null
    $stashed = $true
}

git fetch $Remote

try {
    git pull --rebase $Remote $Branch
} catch {
    Write-Error "git pull failed: $_"
    if ($stashed) {
        Write-Host "Attempting to restore stashed changes after failure..."
        git stash pop | Out-Null
    }
    exit 1
}

if ($stashed) {
    Write-Host "Restoring stashed changes..."
    git stash pop | Out-Null
}

Write-Host "Pull complete."
