<?php 


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_active'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/connection.php'; 
$id_log = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->bind_param("i", $id_log);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$jumlah_tiket = 0;
try {
    $q_tiket = $conn->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_user = ?");
    $q_tiket->bind_param("i", $id_log);
    $q_tiket->execute();
    $res_tiket = $q_tiket->get_result()->fetch_assoc();
    $jumlah_tiket = $res_tiket['total'] ?? 0;
} catch (Exception $e) { 
    $jumlah_tiket = 0; 
}

$jumlah_voucher = 0;
try {
    $q_voucher = $conn->prepare("SELECT COUNT(*) as total FROM vouchers WHERE status = 'aktif' AND tgl_expired >= CURDATE()");
    $q_voucher->execute();
    $res_voucher = $q_voucher->get_result()->fetch_assoc();
    $jumlah_voucher = $res_voucher['total'] ?? 0;
} catch (Exception $e) {
    $jumlah_voucher = 0;
}

$max_target = 10; 
$percent = ($jumlah_tiket / $max_target) * 100;

if ($percent > 100) { $percent = 100; }

if ($jumlah_tiket >= 10) {
    // TIER GOLD (10+ Tiket)
    $tier_name = "Gold Member";
    $icon_tier = "fa-crown";
    $tier_color = "#ffd700"; // Gold
    $pesan_upgrade = "Selamat! Kamu adalah member VIP kami.";
} elseif ($jumlah_tiket >= 4) {
    // TIER SILVER (4 - 9 Tiket)
    $tier_name = "Silver Explorer";
    $icon_tier = "fa-award";
    $tier_color = "#c0c0c0"; // Silver
    $sisa = 10 - $jumlah_tiket;
    $pesan_upgrade = "Butuh $sisa tiket lagi untuk jadi Gold Member!";
} else {
    // TIER BRONZE (0 - 3 Tiket)
    $tier_name = "Bronze Explorer";
    $icon_tier = "fa-medal";
    $tier_color = "#cd7f32"; // Bronze
    $sisa = 4 - $jumlah_tiket;
    $pesan_upgrade = "Beli $sisa tiket lagi untuk naik ke Silver!";
}

$nama = $user['nama'] ?? 'User';
$words = explode(" ", trim($nama));
$inisial = strtoupper(substr($words[0], 0, 1));
if (count($words) > 1) {
    $inisial .= strtoupper(substr(end($words), 0, 1));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Eksklusif - BERANGKATIN</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --accent-blue: #2563eb;
            --bg-light: #f4f7fe;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0;
            min-height: 100vh;
            color: #1a202c;
            overflow-x: hidden;
        }

        .bg-fixed { 
            position: fixed; 
            top: 0; left: 0; 
            width: 100%; height: 100%; 
            background: url('assets/images/background1.jpg') no-repeat center center; 
            background-size: cover; 
            z-index: -2; 
        }

        .bg-overlay { 
            position: fixed; 
            top: 0; left: 0; 
            width: 100%; height: 100%; 
            z-index: -1; 
            background: linear-gradient(180deg, rgba(244, 247, 254, 0.7) 0%, rgba(244, 247, 254, 0.9) 100%); 
            backdrop-filter: blur(0px); 
        }

        .main-content { 
            margin-left: 280px; 
            padding: 40px; 
            min-height: 100vh; 
            position: relative; 
        }

        .header-box {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
            border-radius: 24px;
            padding: 40px;
            color: white;
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.2);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .avatar-circle {
            width: 100px; height: 100px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; font-weight: 800;
            border: 3px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .bento-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
            transition: all 0.4s ease;
        }

        .stat-badge {
            width: 55px; height: 55px;
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .stat-val { font-size: 2.8rem; font-weight: 800; line-height: 1; color: var(--primary-blue); }
        .stat-label { font-size: 0.85rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        .loyalty-card {
            background: #0f172a; 
            color: white; 
            border-radius: 20px;
            padding: 30px;
            height: 100%;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .detail-item:last-child { border: none; }
        .detail-label { color: #94a3b8; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        .detail-value { color: var(--primary-blue); font-weight: 700; }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 20px; }
            .header-box { flex-direction: column; text-align: center; gap: 20px; }
        }
    </style>
</head>
<body>

    <div class="bg-fixed"></div>
    <div class="bg-overlay"></div>

    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="header-box" data-aos="fade-down">
            <div class="d-flex align-items-center flex-wrap justify-content-center gap-4">
                <div class="avatar-circle"><?= $inisial ?></div>
                <div class="text-md-start text-center">
                    <span class="badge mb-2 px-3 py-2 rounded-pill bg-white fw-bold" style="color: <?= $tier_color ?>;">
                        <i class="fas <?= $icon_tier ?> me-1"></i> <?= $tier_name ?>
                    </span>
                    <h1 class="fw-800 m-0"><?= htmlspecialchars($user['nama']) ?></h1>
                    <p class="opacity-75 m-0 mt-1 fw-500"><i class="far fa-envelope me-2"></i><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="bento-card text-center">
                    <div class="stat-badge mx-auto" style="background: #eff6ff; color: var(--accent-blue);">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-val"><?= $jumlah_tiket ?></div>
                    <div class="stat-label mt-2">Riwayat Perjalanan</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="bento-card text-center">
                    <div class="stat-badge mx-auto" style="background: #f0fdf4; color: #22c55e;">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="stat-val text-success"><?= $jumlah_voucher ?></div>
                    <div class="stat-label mt-2">Voucher Aktif</div>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="loyalty-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-800 m-0 text-uppercase">Member Progress</h6>
                        <i class="fas fa-shield-alt text-info fa-lg"></i>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small opacity-75">Level: <?= $tier_name ?></span>
                        <span class="fw-800"><?= round($percent) ?>%</span>
                    </div>
                    <div class="progress mb-3" style="height: 10px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" 
                             style="width: <?= $percent ?>%"></div>
                    </div>
                    <p class="small opacity-60 mb-0"><?= $pesan_upgrade ?></p>
                </div>
            </div>

            <div class="col-lg-8" data-aos="fade-up" data-aos-delay="400">
                <div class="bento-card">
                    <h5 class="fw-800 mb-4" style="color: var(--accent-blue);">
                        <i class="fas fa-fingerprint me-2"></i>Informasi Identitas
                    </h5>
                    <div class="detail-item">
                        <span class="detail-label">Nama Lengkap</span>
                        <span class="detail-value"><?= htmlspecialchars($user['nama']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nomor WhatsApp</span>
                        <span class="detail-value"><?= htmlspecialchars($user['telp'] ?? '-') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status Akun</span>
                        <span class="detail-value text-success">
                            <i class="fas fa-check-circle me-1"></i> Terverifikasi
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="500">
                <div class="bento-card text-center d-flex flex-column justify-content-center align-items-center">
                    <div class="stat-badge bg-light mb-3">
                        <i class="fas fa-user-shield text-primary"></i>
                    </div>
                    <h5 class="fw-800">Keamanan Aktif</h5>
                    <p class="small text-muted px-2">Akun diproteksi enkripsi SSL dan identitas resmi.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
    </script>
</body>
</html>