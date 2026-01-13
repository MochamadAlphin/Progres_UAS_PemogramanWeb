<?php
header('Content-Type: application/json');

if (isset($_COOKIE['session_active']) && isset($_COOKIE['user_session'])) {
    
    echo json_encode([
        "status" => "logged_in",
        "user" => [
            "id_user" => $_COOKIE['user_id'] ?? null,
            "email"   => base64_decode($_COOKIE['user_session']),
            "nama"    => $_COOKIE['user_name'] ?? 'Pengguna'
        ]
    ]);

} else {
    http_response_code(401);
    echo json_encode([
        "status" => "error", 
        "message" => "Sesi habis, silakan login kembali"
    ]);
}
?>