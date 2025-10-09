# Run Guide

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
- If Apache or MySQL won’t start, check for port conflicts on 80/443/3306 and adjust XAMPP settings if needed.

