# Inventory Management System - Setup & Documentation

## Overview

Production-ready inventory management interface with:
- ✅ Responsive dashboard with real-time search & filtering
- ✅ Manual form entry with validation
- ✅ Excel/CSV bulk import with error handling
- ✅ Camera & photo upload (mobile & desktop)
- ✅ Toast notifications & skeleton loaders
- ✅ Pagination & statistics
- ✅ Dark mode support
- ✅ Fully accessible & mobile-optimized

## Quick Start

### 1. Install & Setup

```bash
# Navigate to project root
cd intoriz

# Run setup script
composer run setup

# Run migrations
php artisan migrate

# Start development server
composer run dev
```

### 2. Access Dashboard

Visit: `http://localhost:8000/inventory`

## Features

### 1. **Manual Item Entry**
- SKU (auto-uniqueness validation)
- Product name & description
- Quantity & unit price
- Category & location
- Real-time form validation
- Clear error messages

### 2. **Excel/CSV Import**
- Drag & drop interface
- Support for `.csv`, `.xlsx`, `.xls`
- Batch operations (up to 5MB per file)
- Error reporting for problematic rows
- Upsert logic (update if exists, create if new)
- Automatic data type conversion

**CSV Template:**
```csv
sku,name,description,quantity,unit_price,category,location
SKU-001,Product A,Description A,100,29.99,Electronics,Warehouse A
SKU-002,Product B,Description B,50,49.99,Tools,Warehouse B
```

### 3. **Camera & Photo Upload**
- Desktop: File upload with drag & drop preview
- Mobile: Native camera access
- Supported formats: JPEG, PNG, WebP
- Max size: 2MB (auto-compressed)
- Auto-orientation handling
- Graceful fallback for unsupported browsers

### 4. **Search & Filtering**
- Real-time search across: SKU, Name, Description, Category
- Filter by category (dynamic dropdown)
- Low stock indicator (<10 units)
- Pagination with 15 items per page

### 5. **Dashboard Analytics**
- Total inventory count
- Low stock items alert
- Total inventory value ($)
- Quick stats on load

## API Endpoints

### Get Items
```
GET /inventory/api/items?page=1&search=keyword&category=Electronics
Response: { data: [...], total: 100, current_page: 1, ... }
```

### Add Item
```
POST /inventory/api/items
Body: { sku, name, description, quantity, unit_price, category, location }
```

### Update Item
```
PUT /inventory/api/items/{id}
Body: { Same as add... }
```

### Delete Item
```
DELETE /inventory/api/items/{id}
```

### Import File
```
POST /inventory/api/import
Body: FormData with file (max 5MB, .csv/.xlsx/.xls)
Response: { success: true, imported: 150, errors: [...] }
```

### Upload Image
```
POST /inventory/api/upload-image
Body: FormData with image & optional item_id
Response: { success: true, path: 'inventory-images/...', url: '...' }
```

### Get Categories
```
GET /inventory/api/categories
Response: ["Electronics", "Tools", "Supplies", ...]
```

## File Structure

```
resources/
├── views/
│   └── inventory/
│       └── index.blade.php         # Main dashboard layout
├── js/
│   └── services/
│       ├── inventory-service.js    # API communication
│       ├── ui-manager.js           # Modal & table rendering
│       └── toast-manager.js        # Notifications
├── css/
│   └── app.css                     # TailwindCSS styles

app/
├── Models/
│   └── InventoryItem.php           # Database model
└── Http/
    └── Controllers/
        └── InventoryController.php # API logic

database/
└── migrations/
    └── *_create_inventory_items_table.php

routes/
└── web.php                         # Route definitions
```

## Component Breakdown

### InventoryService (inventory-service.js)
- Wraps fetch API with proper error handling
- Auto-includes CSRF token
- Handles multipart FormData for file uploads
- Converts responses to JSON with error detection

### UIManager (ui-manager.js)
- Modals: Add, Edit, Import, Upload
- Table rendering with pagination
- Stats updates
- Drag & drop handlers
- Camera stream management
- HTML escaping for XSS prevention

### ToastManager (toast-manager.js)
- Success, error, warning, info notifications
- Auto-dismiss after duration
- Manual close option
- Icon indicators
- Stacked display

### InventoryController (PHP)
- Item CRUD operations
- Pagination (15 per page)
- Multi-field search
- Category filtering
- File import parsing (CSV/Excel)
- Image upload with storage
- Validation on all inputs

## Validation Rules

### Item Form
- **SKU**: Required, unique, string
- **Name**: Required, max 255 chars
- **Description**: Optional, max 1000 chars
- **Quantity**: Required, integer ≥ 0
- **Unit Price**: Required, numeric ≥ 0
- **Category**: Optional, max 100 chars
- **Location**: Optional, max 255 chars

### File Import
- Max file size: 5MB
- Allowed types: CSV, XLSX, XLS
- Required columns: sku, name, quantity
- Auto-skips incomplete rows with warning

### Image Upload
- Max size: 2MB
- Formats: JPEG, PNG, WebP
- Stored in: `storage/app/public/inventory-images/`

## Mobile Responsiveness

- Desktop-first design
- Tablet-optimized table view
- Mobile menu collapse
- Touch-friendly buttons (min 44x44px)
- Camera input for photo capture
- Simplified modals on small screens
- Sticky header with filters

## Dark Mode

- Automatic detection via `prefers-color-scheme`
- All components styled for both themes
- Smooth color transitions
- Accessible contrast ratios

## Error Handling

- Try-catch blocks around all async operations
- Descriptive error messages to user
- Form validation before submission
- API error responses properly parsed
- Network errors gracefully handled
- File validation before upload

## Loading States

- Skeleton loaders while fetching
- Disabled buttons during operations
- Loading spinner on form submit
- Smooth transitions between states
- Empty state message when no items

## Security

- CSRF token on all POST/PUT/DELETE
- HTML escaping on all user inputs
- File type validation (frontend & backend)
- SQLinjection prevention (Eloquent ORM)
- File upload to non-web directory
- Input sanitization on imports

## Performance

- Lazy loading of categories
- Debounced search (can be added)
- Pagination for large datasets
- Optimized queries with indexes
- Minimal re-renders
- CSS-in-HTML (no extra requests)
- Image storage with public disk

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Mobile)
- Camera access (https required or localhost)

## Future Enhancements

- [ ] Barcode scanning
- [ ] Advanced analytics/reports
- [ ] Batch operations (bulk delete/update)
- [ ] Export to Excel
- [ ] User roles & permissions
- [ ] Audit log
- [ ] Real-time sync with WebSockets
- [ ] Offline support (PWA)
- [ ] Multi-warehouse support
- [ ] REST API documentation (Swagger)

## Troubleshooting

### Camera not working
- Ensure HTTPS or localhost
- Check browser permissions
- Verify `getUserMedia` support

### File import errors
- Validate CSV column headers match template
- Check file encoding (UTF-8)
- Ensure file size < 5MB
- Review error messages for each row

### Images not displaying
- Check `APP_URL` in .env
- Verify storage symlink: `php artisan storage:link`
- Confirm `FILESYSTEM_DISK=public` in .env

### Database errors
- Run migrations: `php artisan migrate`
- Clear cache: `php artisan cache:clear`
- Check database connection in .env

## Support & Maintenance

For issues or feature requests, please follow Laravel best practices and update this documentation as the system evolves.
