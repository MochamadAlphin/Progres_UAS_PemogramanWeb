<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['session_active']) && isset($_COOKIE['session_active'])) {
    $_SESSION['session_active'] = $_COOKIE['session_active'];
    $_SESSION['user_id']        = $_COOKIE['user_id'] ?? '0';
    $_SESSION['user_name']      = $_COOKIE['user_name'] ?? 'Tamu';
}

$s_active = strtolower(trim($_SESSION['session_active'] ?? 'no'));

require_once(__DIR__ . '/../config/connection.php');

$results = [];
$asal    = htmlspecialchars($_GET['asal'] ?? '');
$tujuan  = htmlspecialchars($_GET['tujuan'] ?? '');
$tanggal = htmlspecialchars($_GET['tanggal'] ?? '');

try {
    $query  = "SELECT * FROM jadwal WHERE 1=1";
    $params = [];
    $types  = "";

    if (!empty($asal)) {
        $query .= " AND stasiun_asal = ?";
        $params[] = $asal;
        $types .= "s";
    }
    if (!empty($tujuan)) {
        $query .= " AND stasiun_tujuan = ?";
        $params[] = $tujuan;
        $types .= "s";
    }
    if (!empty($tanggal)) {
        $query .= " AND tanggal = ?";
        $params[] = $tanggal;
        $types .= "s";
    }

    $query .= " ORDER BY jam_berangkat ASC"; 
    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Error pada cari_data.php: " . $e->getMessage());
}