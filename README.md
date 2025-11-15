# Palermo Restaurant Management System

A comprehensive full-stack restaurant e-commerce platform built with PHP, MySQL, and Bootstrap. Palermo provides complete restaurant operations management with customer ordering, admin panel, email notifications, and real-time order tracking.

## 🍕 About Palermo

Palermo is a production-ready restaurant management and e-commerce platform designed for modern food establishments. The system handles everything from menu management to order processing, cart functionality, checkout, email confirmations, and admin notifications - providing a complete end-to-end solution for restaurant operations.

## ✨ Key Features

### **Customer-Facing Features**
- **Product Catalog**: Browse menu items with categories, images, and detailed descriptions
- **Shopping Cart**: Session-based cart with real-time price calculations in BGN and EUR
- **Product Customization**: Add-on system for product customizations (extra cheese, toppings, etc.)
- **Dynamic Pricing**: Real-time price updates based on selected addons
- **Responsive Design**: Mobile-first design that works on all devices
- **User Accounts**: Registration, login, password reset functionality
- **Order History**: View past orders with detailed breakdowns
- **Checkout Process**: Simple, secure checkout with delivery address and notes
- **Order Confirmation**: Immediate order confirmation with email receipt
- **Multi-Currency Display**: All prices shown in both BGN and EUR

### **Admin Panel Features**
- **Dashboard**: Overview of restaurant operations and statistics
- **User Management**: Create, edit, and manage customer accounts with role-based access
- **Product Management**: Complete CRUD operations for menu items with image upload and slug generation
- **Category Management**: Organize products into logical categories with image support
- **Addon Management**: Flexible addon system for product customizations
- **Order Management**: View and manage all customer orders with detailed item breakdowns
  - Order status tracking (Pending, Processing, Completed, Cancelled)
  - Export orders to PDF and Excel
  - Real-time notifications for new orders
- **Blog Management**: Content management system for restaurant news and updates
- **Email System**: Automated email notifications with custom HTML templates (UTF-8 encoded)
- **Bulk Operations**: Bulk user creation with automated email notifications
- **Image Management**: Upload, preview, and manage product images
- **Gallery System**: Manage photo galleries for restaurant ambiance
- **Status Management**: Toggle active/inactive status for all entities
- **Real-Time Notifications**: Admin receives instant notifications for new pending orders

### **Technical Features**

- **Repository Pattern**: Clean separation of data access logic from business logic
- **Session-Based Cart**: Secure cart management with unique product-addon combinations
- **AJAX Operations**: Seamless cart updates, status toggles, and delete operations
- **Transaction Safety**: All order processing wrapped in database transactions
- **Image Preview**: Real-time image preview during upload with validation
- **Form Validation**: Comprehensive client and server-side validation
- **Security**: Admin authentication, session management, and SQL injection prevention
- **Database Integrity**: Foreign key constraints and referential integrity
- **Error Handling**: Comprehensive error logging with user-friendly feedback
- **UTF-8 Support**: Full UTF-8 encoding for international characters and currency symbols
- **Email Templating**: Responsive HTML email templates with inline CSS
- **Real-Time Polling**: Admin receives new order notifications every 30 seconds
- **Multi-Currency**: Automatic BGN to EUR conversion throughout the application

### **E-Commerce Cart System**

The shopping cart implements a sophisticated key-based system:

- **Unique Cart Keys**: Format `productId_addonId1-addonId2` (sorted) allows the same product with different addons as separate cart items
- **Session Storage**: Cart data persists in `$_SESSION['cart']` for reliable management
- **Price Structure**: 
  - `price`: Base product price (stored in orders as `unit_price`)
  - `item_price`: Base price + all addon prices (used for cart total calculations)
  - This prevents double-counting addons in admin order displays
- **Real-Time Updates**: AJAX-powered cart updates without page refreshes
- **Quantity Controls**: Min/max quantity validation with increment/decrement buttons

📖 **[View Complete Cart Session Flow Documentation](CART_SESSION_FLOW.md)** - Detailed explanation of the entire cart lifecycle from product page to order completion.

### **Order System Architecture**

The system implements a normalized three-table order structure for flexibility and data integrity:
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

#### **Customer E-Commerce System**

- Shopping cart with session management and unique product-addon combinations
- Product detail pages with add-on selection and dynamic pricing
- Real-time cart updates via AJAX (add, update quantity, remove items)
- Cart page with order summary in BGN and EUR
- Checkout process with delivery address and order notes
- Order confirmation page with success message
- User order history with detailed order information
- Order detail view showing all items, addons, and pricing breakdown
- Responsive design optimized for mobile and desktop

#### **User Management**

- User CRUD operations (Create, Read, Update, Delete)
- User registration with email verification
- Login/logout functionality with session management
- Password reset flow with secure tokens
- Bulk user creation with CSV import
- Automated email notifications for new accounts
- Admin authentication and role-based access control

#### **Product Management**

- Complete product CRUD with image upload and preview
- Product categorization with category images
- Addon system with many-to-many relationships
- Product-specific addon assignments
- Image preview during upload with file validation
- Status toggle (Active/Inactive) via AJAX
- SEO-friendly slug generation
- Short and long description fields
- Gallery system for product images

#### **Order Management**

- Complete order processing with repository pattern
- Three-table order structure (orders, order_items, order_item_addons)
- Order status management (Pending, Processing, Completed, Cancelled)
- Order list view for customers filtered by user ID
- Detailed order view with item breakdown
- Admin order management with export to PDF and Excel
- Real-time admin notifications for pending orders (30-second polling)
- Notification badge and dropdown in admin navbar
- Accurate addon price calculation (prevents double-counting)

#### **Content Management**

- Blog post creation and management
- Category-based blog organization
- Rich text content editing
- Featured image support
- Blog list and detail views with Fancybox galleries

#### **Email System**

- HTML email templates with responsive design and Palermo branding
- External CSS styling for easy customization
- SMTP integration via PHPMailer with UTF-8 encoding
- Welcome emails for new user registrations
- Test account creation notifications
- **Order confirmation emails** with:
  - Complete order details and order number
  - Itemized list with products, quantities, and prices
  - Addon breakdown for each product
  - Dual currency display (BGN and EUR)
  - Delivery address and order notes
  - Link to view full order details
- Email sending with proper character encoding for international support

#### **Admin Interface**

- Responsive Bootstrap 5-based dashboard
- AJAX-powered status toggles without page refresh
- Real-time form validation with error feedback
- Image upload with preview and file validation
- DataTables integration for search, sort, and pagination
- Bulk operations support
- Toast notifications for user actions
- Modal confirmations for delete operations

### 🚧 **In Development**

- Payment gateway integration (Stripe, PayPal)
- Invoice generation system
- Advanced reporting and analytics dashboard
- Customer reviews and ratings
- Multi-language support

### 📱 **Planned Features**

- Mobile app integration (iOS and Android)
- Push notifications for order updates
- Multi-location and franchise support
- Loyalty program and reward points
- Table reservation system
- Social media integration and sharing
- Advanced inventory management
- Supplier management system

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

- **Repository Pattern**: All database operations abstracted into dedicated repository classes
  - `OrderProcessingRepository`: Handles order creation with transactions
  - `UserOrderRepository`: Manages user order queries
  - `ProductRepository`: Product and addon data access
- **MVC-like Structure**: Separation of concerns with dedicated includes and partials
- **AJAX Integration**: Real-time UI updates without page refreshes
- **Transaction Safety**: Database operations wrapped in PDO transactions with rollback
- **Image Management**: Centralized upload handling with type and size validation
- **Email Templates**: Reusable `EmailTemplateGenerator` class for all email types
- **Form Validation**: Both client-side (jQuery) and server-side (PHP) validation
- **Session Management**: Secure session handling for cart and authentication
- **Error Logging**: Comprehensive error logging with `error_log()` for debugging

## 📡 API Endpoints & AJAX Operations

### **Cart Operations**

- `include/cart_add.php` - Add product to cart with addons
- `include/cart_update.php` - Update item quantity in cart
- `include/cart_remove.php` - Remove item from cart
- `include/cart_data.php` - Get cart data (count and total)

### **Order Processing**

- `include/process_order.php` - Process checkout and create order
  - Validates user login
  - Creates order with repository pattern
  - Sends confirmation email
  - Clears cart on success

### **Admin Notifications**

- `admin/get_notifications.php` - Get pending order count and list
  - Polls every 30 seconds via `admin/js/notifications.js`
  - Returns JSON with count and last 5 pending orders
  - Updates badge and dropdown in real-time

### **Status Toggle Operations**

- `admin/toggle_status.php` - Generic status toggle for any entity
  - Used for products, categories, users, etc.
  - Returns JSON success/error response

## 🗃️ Database Schema Details

### **Cart Storage Structure**

Session-based cart with unique keys:

```php
$_SESSION['cart'] = [
    '5_2-3' => [  // productId_addonId1-addonId2 (sorted)
        'product_id' => 5,
        'name' => 'Margherita Pizza',
        'slug' => 'margherita-pizza',
        'image' => 'pizza.jpg',
        'price' => 12.50,  // Base price
        'addons' => [
            ['id' => 2, 'name' => 'Extra Cheese', 'price' => 2.00],
            ['id' => 3, 'name' => 'Mushrooms', 'price' => 1.50]
        ],
        'addons_total' => 3.50,
        'item_price' => 16.00,  // Base + addons
        'quantity' => 2
    ]
];
```

### **Order Tables Relationship**

```sql
orders (id, user_id, amount, status_id, order_address, message, created_at)
  └─ order_items (id, order_id, product_id, unit_price, qty, subtotal)
      └─ order_item_addons (id, order_item_id, addon_id, price)
```

**Key Points:**

- `unit_price` in `order_items` stores BASE product price only
- Addons are stored separately in `order_item_addons`
- This prevents double-counting when displaying orders
- Historical pricing is preserved even if menu prices change

## 🎨 Frontend Assets & Build Process

### **CSS Structure**

- `css/style.css` - Main site styles
- `css/cart.css` - Shopping cart specific styles
- `css/checkout.css` - Checkout page styles
- `css/product-detail.css` - Product detail page with addon styling
- `css/orders.css` - Order history and detail pages
- `css/email-template.css` - Email styling (external CSS)
- `css/auth.css` - Login, register, password reset pages

### **JavaScript Modules**

- `js/cart.js` - Cart dropdown functionality and AJAX operations
- `js/cart-page.js` - Cart page quantity updates and removal
- `js/checkout.js` - Checkout form validation and submission
- `js/product-detail.js` - Dynamic price calculation with addons
- `js/functions.js` - Global utility functions

### **Build Tools**

- **Webpack** - Module bundling and asset compilation
- **npm** - Frontend package management
- **Composer** - PHP dependency management

### **Third-Party Libraries**

- Bootstrap 5 - UI framework
- jQuery 3.7.1 - DOM manipulation and AJAX
- Fancybox 5.0 - Image galleries (installed locally)
- PHPMailer - Email sending via SMTP
- Dompdf - PDF generation for orders

## 🔒 Security Implementation

- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: HTML escaping for all user input
- **CSRF Protection**: Session-based request validation
- **File Upload Security**: Type and size validation for images
- **Admin Authentication**: Session-based access control

## 🔒 Security Implementation

### **Authentication & Authorization**

- **Admin Access Control**: `checkAdminLogin()` function protects all admin pages
- **User Authentication**: Session-based login with secure password hashing  
- **Role-Based Access**: Separate admin and customer user roles
- **Session Security**: HTTPOnly cookies and session regeneration on login

### **Data Protection**

- **SQL Injection Prevention**: Prepared statements with PDO throughout entire application
- **XSS Protection**: `htmlspecialchars()` for all user input display
- **CSRF Protection**: Session-based request validation for state-changing operations
- **Password Security**: `password_hash()` and `password_verify()` for secure password storage
- **File Upload Security**: Type validation, size limits, random filename generation

### **Error Handling**

- **Production Mode**: Generic error messages to users
- **Development Mode**: Detailed error logging with `error_log()`
- **Database Errors**: Caught and logged without exposing schema details
- **Email Errors**: Non-blocking - order succeeds even if email fails

## 🐛 Troubleshooting Common Issues

### **Database Connection**
- Verify MySQL is running in XAMPP Control Panel
- Check `include/config.php` credentials
- Ensure `palermo_live` database exists
- Confirm `pdo_mysql` extension enabled

### **Cart Issues**
- Check if user is logged in (cart requires authentication)
- Verify sessions working: check `session.save_path`
- Clear browser cookies and retry

### **Email Problems**
- Verify SMTP credentials in `include/smtp_class.php`
- Check Apache error log for PHPMailer errors
- Ensure `allow_url_fopen` enabled for external CSS

### **Image Upload Failures**
- Verify `uploads/` directory exists and is writable
- Check `upload_max_filesize` and `post_max_size` in `php.ini`

### **Port Conflicts**
- Apache (80): Change to 8080 in `httpd.conf`
- MySQL (3306): Reconfigure if another instance running
- Use `netstat -ano | findstr :80` to identify port usage

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

