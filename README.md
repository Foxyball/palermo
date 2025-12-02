## Installation & Setup

### 1) Place the project
Place this folder at:
```
C:\xampp\htdocs\palermo
```

### 2) Install dependencies
Open Command Prompt and run:
```bash
cd C:\xampp\htdocs\palermo
composer install
npm install
npm run build
```

### 3) Database setup
1. Start MySQL from XAMPP Control Panel
2. Create a database named `palermo_live`
3. Import the SQL from `install.sql` into that database (via phpMyAdmin or MySQL client)
4. Check `include/config.php` and adjust if needed:
   - DB_HOST (default: `localhost`)
   - DB_USER (default: `root`)
   - DB_PASS (default: empty)
   - DB_NAME (default: `palermo_live`)
   - BASE_URL (leave empty for `http://localhost/palermo/`)

### 4) Start services
Start Apache and MySQL in the XAMPP Control Panel

### 5) Access the application
- **Website**: `http://localhost/palermo/`
- **Admin Panel**: `http://localhost/palermo/admin`

## Credentials

### Customer Login
- **Email**: `hsabev@sprintax.com`
- **Password**: `123456`

### Admin Login
- **Email**: `hsabev@sprintax.com`
- **Password**: `123456`

## Troubleshooting
- **Cart issues**: Verify sessions are working and user is logged in
- **Image upload failures**: Ensure `uploads/` directory exists and is writable

## License

This project is designed as a coursework for UE Varna
