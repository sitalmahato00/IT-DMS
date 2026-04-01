#!/bin/bash

# Docker Laravel Startup Script
# Save as: docker-start.sh
# Usage: ./docker-start.sh [command]

COMMAND=${1:-up}

case $COMMAND in
    up)
        echo "Starting Docker containers..."
        docker-compose up -d --build
        ;;
    down)
        echo "Stopping Docker containers..."
        docker-compose down
        ;;
    stop)
        echo "Stopping Docker containers..."
        docker-compose stop
        ;;
    logs)
        echo "Showing Docker logs..."
        docker-compose logs -f
        ;;
    vite)
        echo "Starting or restarting Vite..."
        docker-compose up -d vite
        ;;
    vite-logs)
        echo "Showing Vite logs..."
        docker-compose logs -f vite
        ;;
    shell)
        echo "Opening app shell..."
        docker-compose exec app bash
        ;;
    migrate)
        echo "Running migrations..."
        docker-compose exec app php artisan migrate
        ;;
    seed)
        echo "Running seeders..."
        docker-compose exec app php artisan db:seed
        ;;
    tinker)
        echo "Opening Tinker console..."
        docker-compose exec app php artisan tinker
        ;;
    test)
        echo "Running tests..."
        docker-compose exec app php artisan test
        ;;
    refresh)
        echo "Refreshing Docker setup..."
        docker-compose down -v
        docker-compose up -d --build
        docker-compose exec app php artisan migrate
        ;;
    *)
        echo "Available commands:"
        echo "  ./docker-start.sh up       - Start containers"
        echo "  ./docker-start.sh down     - Stop and remove containers"
        echo "  ./docker-start.sh stop     - Stop containers"
        echo "  ./docker-start.sh logs     - Show logs"
        echo "  ./docker-start.sh vite     - Start or restart Vite"
        echo "  ./docker-start.sh vite-logs - Show Vite logs"
        echo "  ./docker-start.sh shell    - Access app shell"
        echo "  ./docker-start.sh migrate  - Run migrations"
        echo "  ./docker-start.sh seed     - Run seeders"
        echo "  ./docker-start.sh tinker   - Open Tinker"
        echo "  ./docker-start.sh test     - Run tests"
        echo "  ./docker-start.sh refresh  - Full refresh"
        ;;
esac
