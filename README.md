# Palermo Restaurant Management System

A comprehensive web-based restaurant management system built with PHP, MySQL, and Bootstrap. Palermo provides both customer-facing features and a powerful admin panel for complete restaurant operations management.

## 🍕 About Palermo

Palermo is a full-featured restaurant management platform designed for modern food establishments. The system handles everything from product catalog management to order processing, with support for complex product customizations through an addon system.

## ✨ Key Features

### **Admin Panel Features**
- **User Management**: Create, edit, and manage user accounts with role-based access
- **Product Management**: Complete CRUD operations for menu items with image upload
- **Category Management**: Organize products into logical categories
- **Addon System**: Flexible addon management for product customizations (extra toppings, sizes, etc.)
- **Blog Management**: Content management system for restaurant news and updates
- **Email System**: Automated email notifications with custom HTML templates
- **Bulk Operations**: Bulk user creation with automated email notifications
- **Image Management**: Upload, preview, and manage product images
- **Status Management**: Toggle active/inactive status for all entities

### **Technical Features**
- **Responsive Design**: Mobile-first Bootstrap interface
- **AJAX Operations**: Seamless status toggles and delete operations
- **Image Preview**: Real-time image preview during upload
- **Form Validation**: Client and server-side validation
- **Security**: Admin authentication and session management
- **Database Integrity**: Foreign key constraints and transaction safety
- **Error Handling**: Comprehensive error logging and user feedback

### **Order System Architecture**
The system implements a sophisticated three-table order structure:
- **`orders`**: Main order records with customer and payment information
- **`order_items`**: Individual products within each order
- **`order_item_addons`**: Customizations and addons for each product

This architecture supports complex scenarios like:
- Multiple products per order
- Different addons per product instance
- Accurate pricing with historical preservation
- Detailed order analytics and reporting

## 🛠 Technology Stack

- **Backend**: PHP 8.x with PDO for database operations
- **Database**: MySQL with normalized relational design
- **Frontend**: Bootstrap 5 with jQuery for interactions
- **Email**: PHPMailer for SMTP email delivery
- **Build Tools**: Webpack for asset compilation
- **Dependencies**: Composer for PHP packages, npm for frontend assets

## 📱 User Interface

### **Admin Dashboard**
- Modern, responsive admin interface
- Sidebar navigation with organized menu sections
- Data tables with search, pagination, and sorting
- Modal dialogs for confirmations and forms
- Toast notifications for user feedback
- Image upload with real-time preview

### **Frontend** (In Development)
- Customer-facing restaurant website
- Product catalog with category filtering
- Shopping cart functionality
- Order placement system
- User account management

## Orders and Addons

When a customer places an order, it can contain:
multiple pizzas (or products), and
each pizza can have addons (extra cheese, mushrooms, etc.)
That's why we need three tables:
orders, order_items, and order_item_addons.

## �️ Database Schema

### **Core Tables**

- **`users`**: Customer and admin accounts with role-based access
- **`categories`**: Product categorization (Pizzas, Salads, Drinks, etc.)
- **`products`**: Menu items with pricing, descriptions, and images
- **`addons`**: Customization options (toppings, sizes, extras)
- **`product_addons`**: Many-to-many relationship between products and available addons

### **Order Management Tables**

- **`orders`**: Order headers with customer info, totals, and status
- **`order_items`**: Individual products within orders with quantities and pricing
- **`order_item_addons`**: Specific addon selections for each order item

### **Content Management Tables**

- **`blogs`**: News, announcements, and restaurant content
- **`blog_categories`**: Organization of blog content
- **`password_reset_tokens`**: Secure password reset functionality

## 📋 Current Implementation Status

### ✅ **Completed Features**

#### **User Management**
- User CRUD operations (Create, Read, Update, Delete)
- Bulk user creation with CSV import
- Automated email notifications for new accounts
- Admin authentication and session management

#### **Product Management**
- Complete product CRUD with image upload
- Product categorization and filtering
- Addon system with many-to-many relationships
- Image preview during upload
- Status toggle (Active/Inactive)
- Slug generation for SEO-friendly URLs

#### **Content Management**
- Blog post creation and management
- Category-based blog organization
- Rich text content editing
- Featured image support

#### **Email System**
- HTML email templates with Palermo branding
- SMTP integration via PHPMailer
- Welcome emails for new users
- Test account notifications

#### **Admin Interface**
- Responsive Bootstrap-based dashboard
- AJAX-powered status toggles
- Real-time form validation
- Image upload with preview
- Search and pagination
- Bulk operations support

### 🚧 **In Development**
- Customer-facing website
- Shopping cart functionality
- Order placement system
- Payment integration
- Inventory management

### 📱 **Planned Features**
- Mobile app integration
- Advanced reporting and analytics
- Multi-location support
- Loyalty program management
- Social media integration

## �🚀 Installation & Setup Guide

Follow these steps to run the project locally on Windows using XAMPP.

## Prerequisites
- Windows with XAMPP (Apache + MySQL + PHP 8.x)
- Composer (for PHP dependencies)
- Node.js + npm (optional, for frontend libraries)

## 1) Place the project
Place this folder at:
```
C:\xampp\htdocs\palermo
```

## 2) Install dependencies (optional but recommended)
Open Command Prompt and run:
```
cd C:\xampp\htdocs\palermo
composer install
npm install
npm run build
```

## 3) Database setup
1. Start MySQL from XAMPP Control Panel.
2. Create a database named `palermo_live`.
3. Import the SQL from `install.sql` into that database (via phpMyAdmin or MySQL client).
4. Check `include/config.php` and adjust if needed:
   - DB_HOST (default: `localhost`)
   - DB_USER (default: `root`)
   - DB_PASS (default: empty)
   - DB_NAME (default: `palermo_live`)
   - BASE_URL (leave empty for `http://localhost/palermo/`; set if using a different path or virtual host)

## 4) Start services
Start Apache and MySQL in the XAMPP Control Panel.

## 5) Open in your browser
- Site: `http://localhost/palermo/`
- Admin Panel: `http://localhost/palermo/admin`

Demo admin credentials:
- Email: `admin@palermo.bg`
- Password: `password`

## Troubleshooting

- If you see a database connection error, confirm the DB exists, credentials in `include/config.php` are correct, and the `pdo_mysql` PHP extension is enabled.
- If Apache or MySQL won't start, check for port conflicts on 80/443/3306 and adjust XAMPP settings if needed.

## 🔧 Development Workflow

### **Code Organization**

```text
palermo/
├── admin/              # Admin panel interface
│   ├── include/        # Admin-specific functions and AJAX handlers
│   ├── css/           # Admin stylesheets
│   └── js/            # Admin JavaScript files
├── include/           # Core application logic
│   ├── config.php     # Database and application configuration
│   ├── functions.php  # Utility functions
│   └── connect.php    # Database connection
├── css/               # Frontend stylesheets
├── js/                # Frontend JavaScript
├── uploads/           # User-uploaded files (images)
└── vendor/            # Composer dependencies
```

### **Key Development Patterns**

- **MVC-like Structure**: Separation of concerns with dedicated includes
- **AJAX Integration**: Real-time UI updates without page refreshes
- **Transaction Safety**: Database operations wrapped in transactions
- **Image Management**: Centralized upload handling with validation
- **Email Templates**: Reusable HTML email generation
- **Form Validation**: Both client-side and server-side validation

### **Security Features**

- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: HTML escaping for all user input
- **CSRF Protection**: Session-based request validation
- **File Upload Security**: Type and size validation for images
- **Admin Authentication**: Session-based access control

## 🤝 Contributing

This project follows standard PHP development practices:

1. **Code Style**: PSR-12 coding standards
2. **Database**: Use prepared statements for all queries
3. **Security**: Validate and sanitize all user input
4. **Error Handling**: Comprehensive error logging and user feedback
5. **Documentation**: Comment complex business logic

## 📊 Performance Considerations

- **Database Indexing**: Strategic indexes on foreign keys and search fields
- **Image Optimization**: Automatic resize and compression for uploads
- **Caching Strategy**: Session-based caching for frequently accessed data
- **AJAX Optimization**: Minimal payload for status updates
- **Asset Management**: Webpack bundling for optimized delivery

## 📄 License

This project is designed as a coursework for UE Varna

