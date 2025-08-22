#!/bin/bash

# Docker management script for Client Portal

set -e

case "$1" in
    "up")
        echo "🚀 Starting Client Portal with Docker..."
        docker compose up --build -d
        echo "✅ Services started!"
        echo "📱 Admin Panel (Filament): http://localhost:8001/admin"
        echo "🌐 Client Portal: http://localhost:8001"
        echo "⚡ Vite Dev Server: http://localhost:5173"
        echo ""
        echo "📊 Checking service status..."
        docker compose ps
        ;;
    "down")
        echo "🛑 Stopping Client Portal..."
        docker compose down
        echo "✅ All services stopped!"
        ;;
    "restart")
        echo "🔄 Restarting Client Portal..."
        docker compose down
        docker compose up --build -d
        echo "✅ Services restarted!"
        ;;
    "logs")
        if [ -n "$2" ]; then
            echo "📋 Showing logs for $2..."
            docker compose logs -f "$2"
        else
            echo "📋 Showing all logs..."
            docker compose logs -f
        fi
        ;;
    "shell")
        if [ -n "$2" ]; then
            echo "🐚 Opening shell in $2..."
            docker compose exec "$2" sh
        else
            echo "🐚 Opening shell in app container..."
            docker compose exec app sh
        fi
        ;;
    "artisan")
        shift
        echo "🎨 Running artisan command: $*"
        docker compose exec app php artisan "$@"
        ;;
    "npm")
        shift
        echo "📦 Running npm command: $*"
        docker compose exec vite npm "$@"
        ;;
    "fresh")
        echo "🗑️  Cleaning up everything..."
        docker compose down -v
        docker system prune -f
        echo "🚀 Starting fresh..."
        docker compose up --build -d
        ;;
    "debug")
        echo "🔍 Running debug checks..."
        docker compose exec app php /var/www/html/check-socialite.php
        echo ""
        echo "📋 Composer packages:"
        docker compose exec app composer show | grep socialite
        ;;
    "status")
        echo "📊 Service Status:"
        docker compose ps
        echo ""
        echo "🔗 URLs:"
        echo "  Admin Panel: http://localhost:8001/admin"
        echo "  Client Portal: http://localhost:8001"
        echo "  Vite Dev: http://localhost:5173"
        ;;
    *)
        echo "🐳 Client Portal Docker Management"
        echo ""
        echo "Usage: $0 {command}"
        echo ""
        echo "Commands:"
        echo "  up       - Start all services"
        echo "  down     - Stop all services"
        echo "  restart  - Restart all services"
        echo "  logs     - Show logs (optional: specify service name)"
        echo "  shell    - Open shell (optional: specify service name)"
        echo "  artisan  - Run artisan commands"
        echo "  npm      - Run npm commands"
        echo "  fresh    - Clean restart (removes volumes)"
        echo "  debug    - Run debug checks for dependencies"
        echo "  status   - Show service status and URLs"
        echo ""
        echo "Examples:"
        echo "  $0 up"
        echo "  $0 logs app"
        echo "  $0 shell vite"
        echo "  $0 artisan migrate"
        echo "  $0 npm install"
        ;;
esac