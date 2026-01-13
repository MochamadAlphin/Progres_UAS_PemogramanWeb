<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../config/connection.php');


$s_active = $_SESSION['session_active'] ?? $_COOKIE['session_active'] ?? '';
$s_name   = $_SESSION['user_name'] ?? $_COOKIE['user_name'] ?? 'Tamu';
$s_id     = $_SESSION['user_id'] ?? $_COOKIE['user_id'] ?? 0;

if ($s_active !== 'yes') {
    $is_guest = true;
    $display_name = "Tamu";
} else {
    $is_guest = false;
    $display_name = $s_name;
}


$tiket_aktif   = 0; 
$point_reward  = 0; 
$voucher_count = 0;
$total_jam     = 0; 
$total_uang    = 0;
$prog_tiket    = 0; 
$prog_point    = 0; 
$prog_voucher  = 0;
$result_jadwal = null; 

try {
    $query_jadwal = "SELECT * FROM jadwal ORDER BY tanggal ASC, jam_berangkat ASC";
    $result_jadwal = $conn->query($query_jadwal);


    if (!$is_guest) {
        $total_data = $result_jadwal->num_rows;
        
        $tiket_aktif   = $total_data; 
        $point_reward  = $tiket_aktif * 50; 
        $voucher_count = 2; 
 
        $prog_tiket   = min(($tiket_aktif / 10) * 100, 100); 
        $prog_point   = min(($point_reward / 1000) * 100, 100);
        $prog_voucher = 40;

        $total_jam    = $tiket_aktif * 2; 

        $query_total = "SELECT SUM(harga) as total FROM jadwal";
        $res_total = $conn->query($query_total);
        if ($res_total) {
            $row_total = $res_total->fetch_assoc();
            $total_uang = $row_total['total'] ?? 0;
        }
    } 

    $total_transaksi = number_format($total_uang, 0, ',', '.');

} catch (Exception $e) {
    error_log("Error Dashboard: " . $e->getMessage());
}

if ($result_jadwal) {
    $result_jadwal->data_seek(0);
}
?>