# Docker Setup for Client Portal

This project uses Docker to run both the Filament admin panel and the Blade frontend in a single, unified development environment.

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose installed
- No other services running on ports 8001, 5173, or 6379

### Start Everything
```bash
# Using the helper script (recommended)
./docker.sh up

# Or directly with docker-compose
docker-compose up --build -d
```

### Access Your Application
- **🔧 Admin Panel (Filament)**: http://localhost:8001/admin
- **🌐 Client Portal (Blade)**: http://localhost:8001
- **⚡ Vite Dev Server**: http://localhost:5173

## 🛠️ Docker Services

### App Container (`client-portal-app`)
- **Port**: 8001
- **Purpose**: Runs the Laravel application with both Filament admin and Blade frontend
- **Features**:
  - Auto-migration and seeding on startup
  - SQLite database (persistent via volume)
  - Redis integration
  - Health checks

### Vite Container (`client-portal-vite`)
- **Port**: 5173
- **Purpose**: Handles frontend asset compilation and hot reloading
- **Features**:
  - Hot module replacement (HMR)
  - Auto-restart on file changes
  - Optimized for Docker development

### Redis Container (`client-portal-redis`)
- **Port**: 6379 (internal)
- **Purpose**: Caching and session storage
- **Features**: Health monitoring

## 📋 Management Commands

### Using the Helper Script

```bash
# Start services
./docker.sh up

# Stop services
./docker.sh down

# Restart everything
./docker.sh restart

# View logs (all services)
./docker.sh logs

# View logs for specific service
./docker.sh logs app
./docker.sh logs vite

# Open shell in app container
./docker.sh shell
./docker.sh shell app

# Run Laravel artisan commands
./docker.sh artisan migrate
./docker.sh artisan db:seed
./docker.sh artisan tinker

# Run npm commands
./docker.sh npm install
./docker.sh npm run build

# Clean restart (removes all data)
./docker.sh fresh

# Check status and URLs
./docker.sh status
```

### Direct Docker Commands

```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# View logs
docker compose logs -f

# Execute commands in containers
docker compose exec app php artisan migrate
docker compose exec vite npm install

# Rebuild containers
docker compose up --build
```

## 🗂️ Persistent Data

The following data persists between container restarts:

- **Database**: SQLite file in project root
- **Vendor Dependencies**: Cached in `app_vendor` volume
- **Storage Files**: Cached in `app_storage` volume
- **Node Modules**: Cached in `vite_node_modules` volume

## 🔧 Configuration

### Environment Variables
The app container automatically creates a `.env` file if none exists, with these defaults:
- Database: SQLite
- Cache: Database-based
- Session: Database-based
- Queue: Database-based
- Redis: Enabled for caching

### Port Configuration
If you need different ports, modify `docker-compose.yml`:
```yaml
services:
  app:
    ports:
      - "8002:8000"  # Change host port from 8001 to 8002
  vite:
    ports:
      - "5174:5173"  # Change host port
```

### Build Performance
The Docker setup is optimized for fast builds:
- **Optimized .dockerignore**: Excludes `vendor/`, `node_modules/`, and other large directories
- **Multi-layer caching**: Composer dependencies are cached separately from app code
- **Minimal build context**: Only necessary files are sent to Docker daemon

## 🐛 Troubleshooting

### Container Won't Start
```bash
# Check container logs
./docker.sh logs app

# Clean restart
./docker.sh fresh
```

### Vite Assets Not Loading
```bash
# Check vite service status
./docker.sh logs vite

# Restart vite container
docker-compose restart vite
```

### Database Issues
```bash
# Reset database
./docker.sh artisan migrate:fresh --seed

# Or completely fresh start
./docker.sh fresh
```

### Permission Issues
```bash
# Fix Laravel permissions
./docker.sh shell
chown -R www-data:www-data storage bootstrap/cache
```

## 🎯 Development Workflow

1. **Start Development Environment**:
   ```bash
   ./docker.sh up
   ```

2. **Make Changes**: Edit your Laravel/PHP files, Blade templates, or frontend assets

3. **View Changes**: 
   - PHP/Blade changes: Refresh http://localhost:8001
   - Frontend assets: Auto-reload via Vite at http://localhost:5173

4. **Run Database Migrations**:
   ```bash
   ./docker.sh artisan migrate
   ```

5. **Stop When Done**:
   ```bash
   ./docker.sh down
   ```

## 📚 Additional Notes

- **Hot Reloading**: Vite provides hot module replacement for frontend assets
- **Database Seeding**: First startup automatically seeds the database
- **Health Checks**: All services have health monitoring for reliability
- **Volume Optimization**: Dependencies are cached in Docker volumes for faster rebuilds
- **Production**: This setup is optimized for development; see deployment docs for production configuration

## 🆘 Need Help?

If you encounter issues:
1. Check the logs: `./docker.sh logs`
2. Try a clean restart: `./docker.sh fresh`
3. Verify Docker and Docker Compose are properly installed
4. Ensure ports 8000, 5173, and 6379 are available