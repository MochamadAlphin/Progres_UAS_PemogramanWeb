<?php
session_start();
session_unset();
session_destroy();

$expired = time() - 3600; 

$cookies_to_clear = ['user_id', 'user_name', 'user_session', 'session_active'];

foreach ($cookies_to_clear as $cookie_name) {
    if (isset($_COOKIE[$cookie_name])) {
        setcookie($cookie_name, '', $expired, '/');
        unset($_COOKIE[$cookie_name]);
    }
}

if (!headers_sent()) {
    header("Location: ../login.php");
    exit();
} else {
    echo '<script>window.location.href="../login.php";</script>';
    exit();
}
?>