<?php 
session_start();

//logout
if(isset($_GET['logout'])) {
    if(isset($_SESSION['admin_logged_in'])) {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_email']);
        
        // Clear localStorage before redirect
        echo '<script>
            if (typeof(Storage) !== "undefined") {
                localStorage.removeItem("adminSessionStartTime");
            }
            window.location.href = "login.php";
        </script>';
        exit;
    }
}

