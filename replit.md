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
- `GET /api/properties` - Retrieve all properties (with optional filters)
- `GET /api/properties/:id` - Get single property
- `POST /api/properties` - Create new property
- `PUT /api/properties/:id` - Update property
- `DELETE /api/properties/:id` - Delete property

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

## Recent Changes (October 1, 2025)

**Database Migration - PostgreSQL Setup**
- Migrated from MySQL/MariaDB to PostgreSQL for Replit compatibility
- Updated database.php to use PostgreSQL PDO driver with PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD environment variables
- Converted all MySQL-specific syntax to PostgreSQL:
  - Changed ENUM to VARCHAR with CHECK constraints
  - Changed TINYINT to SMALLINT
  - Changed AUTO_INCREMENT to SERIAL
  - Removed MySQL-specific clauses (ENGINE, CHARSET, COLLATE, ON UPDATE CURRENT_TIMESTAMP)
- Updated test-db.php to use PostgreSQL system catalog queries
- Successfully ran database migration creating 4 tables: properties, sliders, settings, users
- Created default admin user (username: admin, password: admin123)
- Seeded default slider and settings data
- All API endpoints tested and working correctly

**GitHub Import Setup - Completed**
- Verified PHP 8.4.10 module installation via Replit toolchain
- Confirmed workflow running PHP development server on port 5000 with 0.0.0.0 host
- Verified cache-control headers preventing iframe caching issues in Replit environment
- Updated deployment configuration for autoscale target (port 80)
- Confirmed .gitignore for PHP projects
- Verified all application components (frontend, admin, API) working correctly
- Tested API endpoints returning proper responses
- Confirmed login page and admin authentication working
- Project successfully imported and running in Replit environment

**Previous Implementation**
- Complete PHP implementation with separate backend/frontend architecture
- RESTful API with full CRUD operations
- Admin dashboard with property management
- Temer Properties branding integration
- Security hardening (XSS prevention, input validation)
- Event listener-based interactions for security

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
