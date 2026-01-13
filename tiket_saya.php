<?php
require_once 'config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_user = $_SESSION['user_id'] ?? 0;
if ($id_user == 0) {
    header("Location: login.php");
    exit();
}

$query_str = "SELECT t.*, j.*, u.nama 
              FROM transaksi t 
              JOIN jadwal j ON t.id_jadwal = j.id_jadwal 
              JOIN users u ON t.id_user = u.id_user 
              WHERE t.id_user = $id_user 
              ORDER BY t.id_transaksi DESC";

$result = mysqli_query($conn, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Saya - BERANGKATIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --accent-orange: #ff6b00;
            --bg-light: #f4f7fe;
            --ticket-border: #e2e8f0;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1a202c;
            overflow-x: hidden;
        }

        /* Background System */
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

        .ticket-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            display: flex;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
            transition: transform 0.3s ease;
        }
        
        .ticket-card:hover { transform: translateY(-5px); }

        .ticket-main {
            flex: 2.5;
            padding: 30px;
            border-right: 2px dashed #cbd5e1;
            position: relative;
        }

        .ticket-stub {
            flex: 1;
            padding: 30px;
            background: rgba(248, 250, 252, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .ticket-main::before, .ticket-main::after {
            content: '';
            position: absolute;
            right: -11px;
            width: 22px;
            height: 22px;
            background: #f1f5f9; /* Match backdrop-filter feel */
            border-radius: 50%;
            z-index: 5;
        }
        .ticket-main::before { top: -11px; }
        .ticket-main::after { bottom: -11px; }

        .train-name {
            color: var(--primary-blue);
            font-weight: 800;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }

        .station-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0;
            color: #1e293b;
            line-height: 1.2;
        }

        .station-label {
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .ticket-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .info-item label {
            display: block;
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .info-item span {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .seat-display {
            background: var(--primary-blue);
            color: white;
            width: 100%;
            padding: 15px 10px;
            border-radius: 16px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
        }

        .seat-label-mini {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        .seat-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.6rem;
            font-weight: 800;
            display: block;
        }

        .booking-code-box {
            border: 1.5px solid #e2e8f0;
            background: white;
            border-radius: 12px;
            padding: 8px 15px;
            width: 100%;
            margin-bottom: 20px;
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 800;
            text-transform: uppercase;
        }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 20px; }
            .ticket-card { flex-direction: column; }
            .ticket-main { border-right: none; border-bottom: 2px dashed #e2e8f0; }
            .ticket-main::before, .ticket-main::after { display: none; }
            .ticket-stub { background: rgba(255,255,255,0.5); }
        }
    </style>
</head>
<body>

    <div class="bg-fixed"></div>
    <div class="bg-overlay"></div>

    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div class="animate__animated animate__fadeInLeft">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active">Tiket Saya</li>
                    </ol>
                </nav>
                <h2 class="fw-bold m-0" style="color: var(--primary-blue);">Tiket Perjalanan 🎫</h2>
                <p class="text-muted m-0">Simpan tiket ini untuk proses boarding di stasiun.</p>
            </div>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($data = mysqli_fetch_assoc($result)): 
                $tgl_raw = $data['tanggal'] ?? date('Y-m-d');
                $tgl_format = date('D, d M Y', strtotime($tgl_raw));
                $jam_format = substr($data['jam_berangkat'] ?? '00:00', 0, 5);
                
                $wagon = "EKS-" . (($data['id_transaksi'] % 3) + 1);
                $seat = chr(65 + ($data['id_transaksi'] % 4)) . (($data['id_transaksi'] % 12) + 1);
            ?>
            
            <div class="ticket-card animate__animated animate__fadeInUp">
                <div class="ticket-main">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <span class="train-name"><i class="fas fa-train me-2"></i><?= $data['nama_kereta'] ?></span>
                            <div class="mt-1">
                                <span class="badge bg-primary-subtle text-primary status-badge"><?= $data['kelas'] ?? 'Eksekutif' ?></span>
                                <span class="badge bg-success-subtle text-success status-badge">Lunas</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small fw-bold"><i class="far fa-calendar-alt me-1"></i> <?= $tgl_format ?></span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-start">
                            <p class="station-code"><?= strtoupper(substr($data['stasiun_asal'], 0, 3)) ?></p>
                            <p class="station-label"><?= $data['stasiun_asal'] ?></p>
                        </div>
                        
                        <div class="flex-grow-1 px-4 text-center">
                            <div style="position: relative; height: 2px; background: #cbd5e1; width: 100%;">
                                <span style="position: absolute; top: -12px; left: 45%; background: rgba(255,255,255,0.1); padding: 0 10px; font-size: 1.2rem;">🚂</span>
                            </div>
                            <small class="text-muted mt-2 d-block fw-bold">DIRECT</small>
                        </div>

                        <div class="text-end">
                            <p class="station-code"><?= strtoupper(substr($data['stasiun_tujuan'], 0, 3)) ?></p>
                            <p class="station-label"><?= $data['stasiun_tujuan'] ?></p>
                        </div>
                    </div>

                    <div class="ticket-info-grid">
                        <div class="info-item">
                            <label>Penumpang</label>
                            <span><?= strtoupper($data['nama']) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Berangkat</label>
                            <span><?= $jam_format ?> WIB</span>
                        </div>
                        <div class="info-item">
                            <label>Peron</label>
                            <span>Jalur <?= ($data['id_transaksi'] % 4) + 1 ?></span>
                        </div>
                    </div>
                </div>

                <div class="ticket-stub">
                    <div class="seat-display">
                        <span class="seat-label-mini">Kursi</span>
                        <span class="seat-val"><?= $wagon ?> / <?= $seat ?></span>
                    </div>

                    <div class="booking-code-box">
                        <span class="seat-label-mini text-muted">Booking Code</span>
                        <span class="fw-bold text-dark" style="font-family: 'JetBrains Mono'; letter-spacing: 1px;">
                            BRK<?= str_pad($data['id_transaksi'], 5, '0', STR_PAD_LEFT) ?>
                        </span>
                    </div>
                    
                    <a href="bukti.php?id_transaksi=<?= $data['id_transaksi'] ?>" class="btn btn-primary w-100 rounded-pill fw-bold btn-sm mb-2 shadow-sm">
                        <i class="fas fa-print me-1"></i> E-Ticket Detail
                    </a>
                </div>
            </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-5 animate__animated animate__zoomIn" style="background: rgba(255,255,255,0.7); border-radius: 30px; backdrop-filter: blur(10px);">
                <img src="https://illustrations.popsy.co/blue/waiting-room.svg" width="200" class="mb-4">
                <h4 class="fw-bold">Belum Ada Tiket</h4>
                <p class="text-muted">Sepertinya kamu belum merencanakan perjalanan apapun.</p>
                <a href="cari_jadwal.php" class="btn btn-primary px-4 py-2 rounded-pill fw-bold mt-2">Cari Tiket Sekarang</a>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>