<?php 
session_start();

//logout
if(isset($_GET['logout'])) {
    if(isset($_SESSION['admin_logged_in'])) {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_email']);
        header('location: login');
        exit;
    }
}

?>