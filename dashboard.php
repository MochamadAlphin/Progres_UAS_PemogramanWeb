<?php 
include('crud/dashboard_data.php'); 

date_default_timezone_set('Asia/Jakarta'); // Set zona waktu ke WIB
$hour = date('H');
if ($hour >= 5 && $hour < 11) {
    $salam = "Selamat Pagi";
} elseif ($hour >= 11 && $hour < 15) {
    $salam = "Selamat Siang";
} elseif ($hour >= 15 && $hour < 18) {
    $salam = "Selamat Sore";
} else {
    $salam = "Selamat Malam";
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BERANGKATIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --accent-orange: #ff6b00;
            --bg-light: #f4f7fe;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1a202c;
            min-height: 100vh;
            position: relative;
            background-color: var(--bg-light);
        }

        .bg-fixed {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('assets/images/background1.jpg') no-repeat center center;
            background-size: cover;
            z-index: -2; 
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(244, 247, 254, 0.85); 
            z-index: -1;
        }

        .main-content { 
            margin-left: 280px; 
            padding: 40px; 
            min-height: 100vh;
        }

        .stat-link {
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .greeting-box { margin-bottom: 45px; }

        .stat-card { 
            border: none; 
            border-radius: 24px; 
            padding: 30px; 
            color: white; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 180px; 
            height: 100%; 
        }

        .stat-card:hover { 
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.15);
        }

        .card-purple { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }
        .card-teal { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); }
        .card-orange { background: linear-gradient(135deg, #c2410c 0%, #f97316 100%); }

        .progress-thin {
            height: 6px; 
            background: rgba(255,255,255,0.2); 
            border-radius: 10px;
            margin-top: 15px;
        }

        .task-card { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(5px);
            border-radius: 20px; 
            padding: 25px; 
            border: 1px solid rgba(255,255,255,0.3); 
            margin-bottom: 18px;
            transition: all 0.3s ease;
            box-shadow: var(--card-shadow);
        }

        .schedule-link { text-decoration: none; color: inherit; display: block; }
        .schedule-link:hover .task-card {
            transform: translateX(10px);
            border-color: var(--primary-blue);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            border-radius: 24px;
            padding: 35px;
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: var(--card-shadow);
            height: calc(100% - 18px);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .btn-add { 
            background: var(--primary-blue); 
            color: white; 
            border-radius: 16px; 
            padding: 14px 28px; 
            font-weight: 700;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.2);
        }

        .card-disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .lock-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.2rem;
            opacity: 0.5;
        }

        @media (max-width: 992px) { 
            .main-content { margin-left: 0; padding: 20px; } 
            .glass-panel { height: auto; margin-top: 20px; }
        }
    </style>
</head>
<body>

    <div class="bg-fixed"></div>
    <div class="bg-overlay"></div>

    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="greeting-box d-flex justify-content-between align-items-end animate__animated animate__fadeIn">
            <div>
                <h2 class="fw-bold mb-1" style="font-weight: 800; color: var(--primary-blue);">
                    <?= $salam ?>, <?= htmlspecialchars($display_name) ?>! 👋
                </h2>
                <p class="text-muted m-0">Mau kemana kita hari ini? Cek jadwal perjalananmu.</p>
            </div>
            <a href="cari_jadwal.php" class="btn btn-add shadow-sm">
                <i class="fas fa-plus me-2"></i> Cari Tiket Baru
            </a>
        </div>

        <div class="row g-4 mb-5 d-flex align-items-stretch">
            <div class="col-xl-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <a href="cari_jadwal.php" class="stat-link h-100 <?= $is_guest ? 'card-disabled' : '' ?>">
                    <div class="stat-card card-purple">
                        <?php if ($is_guest) echo '<i class="fas fa-lock lock-badge"></i>'; ?>
                        <div>
                            <p class="opacity-75 mb-1 fw-semibold small text-uppercase">perjalanan tersedia</p>
                            <h3 class="fw-bold mb-0"><?= $tiket_aktif ?> Perjalanan</h3>
                        </div>
                        <div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-white" style="width: <?= $prog_tiket ?>%"></div>
                            </div>
                            <small class="mt-2 d-block opacity-75">Klik untuk lihat tiket <i class="fas fa-arrow-right ms-1"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="stat-card card-teal h-100 <?= $is_guest ? 'card-disabled' : '' ?>">
                    <?php if ($is_guest) echo '<i class="fas fa-lock lock-badge"></i>'; ?>
                    <div>
                        <p class="opacity-75 mb-1 fw-semibold small text-uppercase">Point Reward</p>
                        <h3 class="fw-bold mb-0"><?= number_format($point_reward) ?> Poin</h3>
                    </div>
                    <div>
                        <div class="progress progress-thin">
                            <div class="progress-bar bg-white" style="width: <?= $prog_point ?>%"></div>
                        </div>
                        <small class="mt-2 d-block opacity-0">Placeholder</small> 
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <a href="voucher.php" class="stat-link h-100 <?= $is_guest ? 'card-disabled' : '' ?>">
                    <div class="stat-card card-orange">
                        <?php if ($is_guest) echo '<i class="fas fa-lock lock-badge"></i>'; ?>
                        <div>
                            <p class="opacity-75 mb-1 fw-semibold small text-uppercase">Voucher Saya</p>
                            <h3 class="fw-bold mb-0"><?= $voucher_count ?> Tersedia</h3>
                        </div>
                        <div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-white" style="width: <?= $prog_voucher ?>%"></div>
                            </div>
                            <small class="mt-2 d-block opacity-75">Gunakan promo sekarang <i class="fas fa-arrow-right ms-1"></i></small>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-4 d-flex align-items-stretch">
            <div class="col-lg-8 animate__animated animate__fadeInLeft" style="animation-delay: 0.4s;">
                <h5 class="fw-bold mb-4">Jadwal Terdekat Tersedia</h5>
                
                <?php if ($result_jadwal && mysqli_num_rows($result_jadwal) > 0): 
                    while ($row = mysqli_fetch_assoc($result_jadwal)): ?>
                    <a href="cari_jadwal.php" class="schedule-link">
                        <div class="task-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="date-badge text-center me-4" style="min-width: 75px; padding-right: 20px; border-right: 2px dashed #e2e8f0;">
                                    <h4 class="fw-bold mb-0 text-primary"><?= date('d', strtotime($row['tanggal'])) ?></h4>
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;"><?= date('M', strtotime($row['tanggal'])) ?></small>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1" style="font-size: 1.1rem;">
                                        <?= htmlspecialchars($row['stasiun_asal']) ?> 
                                        <i class="fas fa-arrow-right mx-2 small text-muted"></i> 
                                        <?= htmlspecialchars($row['stasiun_tujuan']) ?>
                                    </h6>
                                    <div class="small text-muted">
                                        <span class="me-3"><i class="fas fa-train me-1"></i> <?= htmlspecialchars($row['nama_kereta']) ?></span>
                                        <span><i class="fas fa-clock me-1"></i> <?= date('H:i', strtotime($row['jam_berangkat'])) ?> WIB</span>
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold">Tersedia</span>
                        </div>
                    </a>
                <?php endwhile; else: ?>
                    <div class="task-card text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted opacity-25 mb-3"></i>
                        <p class="text-muted mb-0">Tidak ada jadwal ditemukan untuk saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4 animate__animated animate__fadeInRight" style="animation-delay: 0.5s;">
                <h5 class="fw-bold mb-4">Statistik Perjalanan</h5>
                <div class="glass-panel text-center">
                    <p class="text-muted small text-uppercase fw-bold mb-1">Total Waktu di Kereta</p>
                    <h1 class="fw-bold mb-0" style="color: var(--primary-blue); font-size: 3.5rem;"><?= $total_jam ?></h1>
                    <p class="fw-bold text-primary mb-4">Jam Terbang</p>
                    
                    <div class="p-3 rounded-4 bg-light mb-4 shadow-sm border">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted fw-semibold">Estimasi Transaksi</span>
                            <span class="small fw-bold text-dark">Rp <?= number_format($total_transaksi, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    
                    <hr class="my-3 opacity-10">
                    <a href="tiket_saya.php" class="text-decoration-none small fw-bold text-primary">
                        <i class="fas fa-ticket-alt me-1"></i> Tiket Saya Aktif
                    </a>
                </div>
            </div>
        </div>
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>