<?php

// DB CREDENTIALS
const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "palermo_live";

// GENERAL SETTINGS
const BASE_URL = "/palermo/";
const SITE_TITLE = "Palermo";

// CURRENCY SETTINGS
const BGN_TO_EUR_RATE = 1.95583;

// ERROR REPORTING
error_reporting(E_ALL & ~E_NOTICE); // E_ALL for development, E_ALL & ~E_NOTICE for production
ini_set('display_errors', 1);  // 1 to display errors, 0 to hide errors

// TIMEZONE
date_default_timezone_set('Europe/Sofia');

// START SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}