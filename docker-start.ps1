# Docker Laravel Startup Script
# Save as: docker-start.ps1

param(
    [string]$Command = "up"
)

$commands = @{
    "up" = "docker-compose up -d --build"
    "down" = "docker-compose down"
    "stop" = "docker-compose stop"
    "logs" = "docker-compose logs -f"
    "vite" = "docker-compose up -d vite"
    "vite-logs" = "docker-compose logs -f vite"
    "shell" = "docker-compose exec app bash"
    "migrate" = "docker-compose exec app php artisan migrate"
    "prepare" = "docker-compose exec app sh -lc 'mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data bootstrap/cache && chmod -R 775 storage bootstrap/cache && php artisan config:clear && php artisan cache:clear && php artisan config:cache'"
    "seed" = "docker-compose exec app php artisan db:seed"
    "tinker" = "docker-compose exec app php artisan tinker"
    "test" = "docker-compose exec app php artisan test"
    "refresh" = "docker-compose down -v && docker-compose up -d --build && docker-compose exec app php artisan migrate"
}

if ($commands.ContainsKey($Command)) {
    Write-Host "Running: $($commands[$Command])" -ForegroundColor Green
    Invoke-Expression $commands[$Command]
} else {
    Write-Host "Available commands:" -ForegroundColor Yellow
    $commands.Keys | ForEach-Object {
        Write-Host "  .\docker-start.ps1 $_"
    }
        Write-Host ""
        Write-Host "Usage: .\docker-start.ps1 [command]" -ForegroundColor Cyan
        Write-Host "  prepare - Prepare cache/runtime directories"
    }
