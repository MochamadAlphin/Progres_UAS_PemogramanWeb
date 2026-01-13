<?php
session_start();

$session_active = $_SESSION['session_active'] ?? $_COOKIE['session_active'] ?? 'no';
$user_id        = $_SESSION['user_id'] ?? $_COOKIE['user_id'] ?? null;

// Jika belum login (bukan member & bukan guest)
if ($session_active === '' || $session_active === 'no') {
    header("Location: login.php");
    exit();
}

// Jika sudah login sebagai member
if ($session_active === 'yes' && $user_id) {
    header("Location: dashboard.php");
    exit();
}

// Jika guest (session_active = no tapi tetap ingin masuk dashboard sebagai tamu)
if ($session_active === 'no') {
    header("Location: dashboard.php");
    exit();
}
