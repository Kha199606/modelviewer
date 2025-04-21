<?php
session_start(); // Access the existing session

// Unset all session variables
$_SESSION = array();

// If using session cookies, delete the cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: index.php"); // Redirect to your form page
exit();
?>