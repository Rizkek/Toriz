# Inventory Management System - Environment Configuration

## Installation Steps

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

3. **Update database credentials in .env:**
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   
   # Or for MySQL:
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=inventory_db
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

4. **Create database file (if using SQLite):**
   ```bash
   touch database/database.sqlite
   ```

5. **Run migrations and seed:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Create storage symlink:**
   ```bash
   php artisan storage:link
   ```

7. **Build frontend assets:**
   ```bash
   npm run build
   # Or for development with hot reload:
   npm run dev
   ```

8. **Start the server:**
   ```bash
   php artisan serve
   # Or use the dev script:
   composer run dev
   ```

9. **Access the application:**
   Open `http://localhost:8000/inventory` in your browser

## Key Environment Variables

```env
APP_NAME=IntoRiz
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# File Storage
FILESYSTEM_DISK=public

# Image Upload Settings
# Max file size for images (in KB, default 2048 = 2MB)
# Max file size for imports (in KB, default 5120 = 5MB)

# Database
DB_CONNECTION=sqlite
```

## Deployment Checklist

For production deployment:

1. Set `APP_DEBUG=false`
2. Set `APP_ENV=production`
3. Run `npm run build` (minified production build)
4. Configure proper database (MySQL recommended)
5. Enable HTTPS
6. Set proper file permissions on storage/
7. Configure backups
8. Set up logging and monitoring
9. Use a production web server (Nginx/Apache)
10. Configure CORS if needed

## Troubleshooting

### "SQLSTATE[HY000]: General error: 11 database disk image is malformed"
- This happens with concurrent requests on SQLite
- **Solution:** Use MySQL for production
- For development: Clear database and re-migrate

### "The FILESYSTEM_DISK environment variable has not been set"
- **Solution:** Add to .env: `FILESYSTEM_DISK=public`

### Camera not working
- Requires HTTPS (or localhost on development)
- Check browser permissions
- Ensure camera hardware is available

### Images not showing after upload
- Run: `php artisan storage:link`
- Check `config/filesystems.php` disk configuration
- Verify file permissions on `storage/app/public/`

### Port 8000 already in use
- Use different port: `php artisan serve --port=8001`
- Or kill process: `lsof -i :8000` then `kill -9 <PID>`

## Testing

Run the test suite:
```bash
composer test
```

Run specific test:
```bash
php artisan test tests/Feature/InventoryControllerTest.php
```
