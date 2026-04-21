<?php
/**
 * auth.php — session guard
 * Include this at the top of any page that requires login.
 * Usage: require_once 'auth.php';
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
