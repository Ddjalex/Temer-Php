# Temer Properties - Real Estate Listing Website

## Overview

A dynamic real estate listing website built with PHP for Temer Properties. The application features a public-facing frontend for browsing properties and an admin dashboard for managing property listings. The system uses PHP 8.4 with JSON file-based storage and follows a clear separation between backend API and frontend presentation.

## User Preferences

- **Language**: PHP (specifically requested)
- **Communication Style**: Simple, everyday language
- **Architecture**: Separate backend and frontend

## System Architecture

### Technology Stack

**Backend**
- PHP 8.4.10 (via nix-shell)
- PostgreSQL database (Neon-backed via Replit)
- RESTful API design
- Built-in PHP development server
- PDO for database abstraction

**Frontend**
- HTML5/CSS3
- Vanilla JavaScript
- Responsive design with Temer green branding (#8BC34A)
- Custom CSS styling

### Project Structure

```
/
├── index.php                 # Main router
├── backend/
│   ├── config.php           # Configuration and helper functions
│   └── api.php              # RESTful API endpoints
├── frontend/
│   ├── index.php            # Home page template
│   ├── property.php         # Property detail page
│   ├── style.css            # Styles with Temer branding
│   ├── app.js               # Frontend JavaScript
│   └── assets/
│       └── images/
│           └── temer-logo.jpg
├── admin/
│   ├── index.php            # Admin dashboard template
│   └── admin.js             # Admin panel functionality
└── data/
    └── properties.json      # Property data storage
```

### Backend Architecture

**RESTful API Endpoints**
- Properties:
  - `GET /api/properties` - Retrieve all properties (with optional filters)
  - `GET /api/properties/:id` - Get single property
  - `POST /api/properties` - Create new property
  - `PUT /api/properties/:id` - Update property
  - `DELETE /api/properties/:id` - Delete property
- Sliders:
  - `GET /api/sliders` - Retrieve all sliders
  - `GET /api/sliders/:id` - Get single slider
  - `POST /api/sliders` - Create new slider
  - `PUT /api/sliders/:id` - Update slider
  - `DELETE /api/sliders/:id` - Delete slider
- Upload:
  - `POST /api/upload` - Upload image file (returns URL)

**Security Features**
- Input validation with `strip_tags()` for text fields
- HTML escaping on frontend with `escapeHtml()` function
- URL encoding in all fetch calls
- JSON error handling
- Event listeners instead of inline handlers (XSS prevention)
- Numeric bounds checking for prices and measurements

**Data Storage**
- PostgreSQL database with 4 tables: properties, sliders, settings, users
- Database credentials via environment variables (PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD)
- Automatic connection pooling via PDO
- Database migrations via migrate.php

### Frontend Architecture

**Public Pages**
- **Home Page** (`/`) - Property grid with search/filter capabilities
- **Property Detail** (`/property?id=xxx`) - Individual property view with full details

**Admin Dashboard** (`/admin`)
- Hero Slider management with image upload
  - Add/Edit/Delete sliders
  - Upload images or use URL
  - Image preview
  - Set display order and active status
  - Live preview in admin dashboard
- Property management table
- Create/Edit/Delete operations
- Inline editing with form validation
- Real-time updates

**Design System**
- Temer green color scheme (#8BC34A, #558B2F, #DCEDC8)
- Responsive grid layout
- Card-based property display
- Professional form styling

### Data Model

**Property Schema**
```php
[
    'id' => string,           // Unique identifier (uniqid('prop_'))
    'title' => string,        // Property title
    'description' => string,  // Full description
    'price' => float,         // Price in dollars
    'location' => string,     // Location/address
    'type' => enum,           // 'sale' or 'rent'
    'bedrooms' => int,        // Number of bedrooms
    'bathrooms' => int,       // Number of bathrooms
    'area' => int,            // Square footage
    'image' => string,        // Image URL
    'featured' => bool,       // Featured property flag
    'status' => string,       // 'available' (default)
    'createdAt' => datetime   // Creation timestamp
]
```

## Deployment

**Development Server**
- Runs on port 5000 using PHP 8.4 module
- Command: `php -S 0.0.0.0:5000 -t . index.php`
- Configured workflow named "Server"
- Cache-Control headers enabled to prevent caching in Replit iframe

**For Production**
- Recommend adding authentication to `/admin` route
- Consider restricting CORS from `Access-Control-Allow-Origin: *` to same-origin
- Implement session-based admin login for security
- Consider migrating to database storage for better performance

## Security Considerations

**Implemented**
- XSS prevention with HTML escaping
- Input sanitization with `strip_tags()`
- URL encoding in API calls
- Event listener-based handlers (no inline onclick)
- JSON validation and error handling

**Recommended for Production**
- Admin authentication and authorization
- CORS restrictions (remove `Access-Control-Allow-Origin: *`)
- CSRF protection
- Rate limiting
- Database migration for better concurrency

## Recent Changes (October 2, 2025)

**Hero Slider Image Management Feature Added**
- Added complete slider management in admin dashboard
  - Create/Edit/Delete sliders with image upload functionality
  - Support for both file upload and URL input
  - Image preview before saving
  - Display order and active/inactive status control
- Implemented image upload endpoint (`/api/upload`)
  - File type validation (JPG, PNG, GIF, WEBP)
  - File size limit (5MB maximum)
  - Automatic filename generation with unique IDs
  - Secure upload handling with authentication
- Added full CRUD API endpoints for sliders
- Frontend now dynamically loads sliders from database
- Admin dashboard hero slider updates in real-time
- Images stored in `/frontend/assets/images/` directory

## Previous Changes (October 2, 2025)

**GitHub Import Setup - PostgreSQL Configuration Completed**
- Configured database.php to use PostgreSQL PDO driver with Replit environment variables (PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD)
- Converted migrate.php from MySQL to PostgreSQL-compatible syntax:
  - Changed ENUM to VARCHAR with CHECK constraints
  - Changed TINYINT to SMALLINT
  - Changed AUTO_INCREMENT to SERIAL
  - Removed MySQL-specific clauses (ENGINE, CHARSET, COLLATE, ON UPDATE CURRENT_TIMESTAMP)
- Created PostgreSQL database via Replit
- Successfully ran database migration creating 4 tables: properties, sliders, settings, users
- Created default admin user (username: admin, password: admin123)
- Seeded default slider and settings data (3 slider entries, 4 settings entries)

**Environment Setup**
- PHP 8.4.10 installed and verified
- Workflow configured to run PHP development server on port 5000 with 0.0.0.0 host
- Cache-Control headers already implemented in index.php to prevent caching in Replit iframe
- Deployment configuration set to autoscale deployment target (port 80)
- All environment variables properly configured

**Testing & Verification**
- ✅ Database connection verified from CLI and web contexts
- ✅ All API endpoints functional (GET /api/properties returns empty array as expected)
- ✅ Frontend homepage displays correctly with slider carousel and search filters
- ✅ Admin login page renders correctly
- ✅ Workflow restarted successfully and running without errors
- ✅ All application components (frontend, admin panel, API) verified working

**Project Status**
- ✅ Fresh GitHub import successfully configured for Replit environment
- ✅ PostgreSQL database fully migrated and operational
- ✅ All API endpoints functional
- ✅ Frontend and admin interfaces working
- ✅ Ready for property data to be added via admin panel
- ✅ Deployment configuration complete and ready for publishing

## External Dependencies

**Current**
- PHP 8.4.10 module (installed via Replit toolchain)
- PostgreSQL database (Neon-backed via Replit)
- No external libraries or frameworks
- Self-contained application

**Potential Future Enhancements**
- Image upload and storage service (Cloudinary, AWS S3)
- Map integration (Google Maps, Mapbox)
- Email notifications
- Enhanced user role management
- Payment processing (Stripe)
