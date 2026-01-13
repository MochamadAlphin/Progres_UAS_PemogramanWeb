<?php
session_start();
session_unset();
session_destroy();

require_once '../config/connection.php'; 

if (isset($_POST['register'])) {
    $nama       = trim($_POST['nama'] ?? '');
    $nik        = trim($_POST['nik'] ?? '');
    $telp       = trim($_POST['telp'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $gender     = $_POST['gender'] ?? ''; 
    $pass       = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if (empty($nama) || empty($email) || empty($pass) || empty($nik)) {
        echo "<script>alert('Semua kolom wajib diisi!'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!'); window.history.back();</script>";
        exit;
    }

    if ($pass !== $konfirmasi) {
        echo "<script>alert('Konfirmasi password tidak cocok!'); window.history.back();</script>";
        exit;
    }

    $cek = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    $result = $cek->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar! Gunakan email lain.'); window.history.back();</script>";
        $cek->close();
        exit;
    }
    $cek->close();

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $query = $conn->prepare("INSERT INTO users (nama, nik, telp, email, gender, password) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssss", $nama, $nik, $telp, $email, $gender, $hashed_pass);

    if ($query->execute()) {
        echo "<script>
                alert('Registrasi Berhasil! Silakan Login dengan akun baru Anda.'); 
                window.location.href='../login.php';
              </script>";
    } else {
        echo "Terjadi kesalahan database: " . $query->error;
    }

    $query->close();
    $conn->close();

} else {
    header("Location: ../register.php");
    exit();
}
?>