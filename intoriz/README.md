# Production-Ready Inventory Management System

A professional, feature-rich inventory dashboard built with Laravel 12, TailwindCSS, and vanilla JavaScript. Enterprise-grade UI with no third-party component libraries.

## 🎯 Key Features

- ✅ Dashboard with real-time statistics
- ✅ Manual item entry with validation
- ✅ CSV/Excel bulk import (up to 5MB)
- ✅ Camera & photo upload (mobile & desktop)
- ✅ Real-time search & filtering
- ✅ Responsive design (desktop-first)
- ✅ Dark mode support
- ✅ Toast notifications
- ✅ Skeleton loaders
- ✅ Pagination (15 items/page)
- ✅ Fully accessible (WCAG 2.1 AA)

## 🚀 Quick Start

```bash
cd intoriz
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
php artisan serve
```

Visit: `http://localhost:8000/inventory`

## 📁 What's Included

### Backend
- Laravel 12 with RESTful API
- Eloquent models with validation
- CSV/Excel file parsing
- Image upload & storage
- Pagination with filters

### Frontend
- Vanilla JavaScript (no frameworks)
- TailwindCSS v4
- Responsive modals
- Drag & drop upload
- Real-time search

### Database
- SQLite for development
- MySQL-ready for production
- Indexed queries
- Sample seeder data

### Testing
- Feature tests
- Factory patterns
- API endpoint tests

## 📋 API Endpoints

```
GET    /inventory/api/items              # List items
POST   /inventory/api/items              # Add item
PUT    /inventory/api/items/{id}         # Update item
DELETE /inventory/api/items/{id}         # Delete item
GET    /inventory/api/categories         # Get categories
POST   /inventory/api/import             # Import file
POST   /inventory/api/upload-image       # Upload image
```

## 📚 Documentation

- [Full Features Guide](INVENTORY_README.md)
- [Setup & Deployment](SETUP.md)

## 🔒 Security

- CSRF token on all forms
- Input validation (client & server)
- XSS prevention (HTML escaping)
- File type validation
- Secure file storage
- SQL injection protection (Eloquent)

## ⚡ Performance

- Pagination for large datasets
- Database indexes on common queries
- Lazy-loaded categories
- Minified CSS/JS in production
- Image compression (JPEG @ 90%)
- Efficient vanilla JS

## 📱 Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🧪 Testing

```bash
php artisan test
```

## 📝 License

MIT License
