# Temer Properties - Real Estate Listing Website

## Overview

A dynamic real estate listing website built with PHP for Temer Properties. The application features a public-facing frontend for browsing properties and an admin dashboard for managing property listings. The system uses PHP 8.4 with PostgreSQL database storage and follows a clear separation between backend API and frontend presentation.

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

**Fresh GitHub Clone - Complete Replit Environment Setup**

This project was imported fresh from a GitHub repository and has been fully configured to run in the Replit environment.

**Database Configuration**
- Created PostgreSQL database via Replit (Neon-backed)
- Updated `backend/database.php` to use PostgreSQL PDO driver with Replit environment variables:
  - Changed from MySQL to PostgreSQL DSN
  - Configured to use PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD
  - Removed MySQL-specific SSL configuration
- Ran database migration successfully creating 4 tables:
  - `properties` - Property listings with all metadata
  - `sliders` - Hero slider images and content
  - `settings` - Site configuration settings
  - `users` - Admin user authentication
- Created default admin user (username: admin, password: admin123)
- Seeded 3 default slider entries and 4 default settings

**Environment Setup**
- PHP 8.4.10 module already installed (via Replit toolchain)
- Workflow configured to run PHP development server on port 5000 with 0.0.0.0 host binding
- Cache-Control headers already implemented in index.php to prevent caching in Replit iframe
- Deployment configuration set to autoscale deployment target (port 80)
- All PostgreSQL environment variables properly configured

**Testing & Verification**
- ✅ Database connection verified - All API endpoints functional
- ✅ GET /api/properties returns empty array (no properties added yet)
- ✅ GET /api/sliders returns 3 default sliders
- ✅ GET /api/settings returns default site settings
- ✅ Frontend homepage displays correctly with hero slider, search filters, and branding
- ✅ Workflow running without errors
- ✅ All application components ready (frontend, admin panel, API)

**Project Status**
- ✅ Fresh GitHub import successfully configured for Replit environment
- ✅ PostgreSQL database fully operational
- ✅ All API endpoints functional
- ✅ Frontend verified working with screenshot
- ✅ Admin panel ready (accessible at /admin with credentials above)
- ✅ Deployment configuration complete and ready for publishing
- ✅ Ready to add property listings via admin dashboard

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
