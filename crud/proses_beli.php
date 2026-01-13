<?php
require_once __DIR__ . '/../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_user = $_SESSION['user_id'] ?? 0;
    
    if ($id_user <= 0) {
        die("Error: Sesi berakhir. Silakan login kembali.");
    }

    $id_jadwal    = (int)($_POST['id_jadwal'] ?? 0);
    $harga_asli   = (int)($_POST['harga_asli'] ?? 0);
    $total_bayar  = (int)($_POST['total_bayar'] ?? 0);
    $tgl_beli     = date('Y-m-d H:i:s');

    $id_voucher_raw = $_POST['id_voucher'] ?? '';
    $id_voucher = (!empty($id_voucher_raw)) ? (int)$id_voucher_raw : "NULL";

    if ($id_jadwal <= 0) {
        echo "<h3>Debug Info:</h3>";
        echo "ID Jadwal yang diterima: " . htmlspecialchars($id_jadwal) . "<br>";
        echo "Isi POST: <pre>"; print_r($_POST); echo "</pre>";
        die("Error: Jadwal tidak valid. Form tidak mengirimkan ID Jadwal.");
    }

    $query = "INSERT INTO transaksi (
                id_user, 
                id_jadwal, 
                id_voucher, 
                harga_asli, 
                total_bayar, 
                status, 
                tgl_beli
              ) VALUES (
                $id_user, 
                $id_jadwal, 
                $id_voucher, 
                $harga_asli, 
                $total_bayar, 
                'Lunas', 
                '$tgl_beli'
              )";

    if (mysqli_query($conn, $query)) {
        $id_baru = mysqli_insert_id($conn);
        
        if ($id_voucher !== "NULL") {
            mysqli_query($conn, "UPDATE vouchers SET kuota = kuota - 1 WHERE id_voucher = $id_voucher");
        }

        header("Location: ../bukti.php?id_transaksi=" . $id_baru);
        exit();
    } else {
        die("Gagal simpan transaksi ke database: " . mysqli_error($conn));
    }

} else {
    header("Location: ../dashboard.php");
    exit();
}