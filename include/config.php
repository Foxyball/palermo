<?php

// DB CREDENTIALS
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "palermo_live");

// GENERAL SETTINGS
define("BASE_URL", "");
define("SITE_TITLE", "Palermo");

// ERROR REPORTING
error_reporting(E_ALL & ~E_NOTICE); // E_ALL for development, E_ALL & ~E_NOTICE for production
ini_set('display_errors', 1);  // 1 to display errors, 0 to hide errors

// TIMEZONE
date_default_timezone_set('Europe/Sofia');