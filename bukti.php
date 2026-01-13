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

$id_transaksi = isset($_GET['id_transaksi']) ? (int)$_GET['id_transaksi'] : 0;

if ($id_transaksi <= 0) {
    echo "<script>alert('Akses tidak sah!'); window.location.href='index.php';</script>";
    exit();
}

// 3. Query Data
$query_str = "SELECT t.*, j.*, u.nama, u.email
              FROM transaksi t 
              JOIN jadwal j ON t.id_jadwal = j.id_jadwal 
              JOIN users u ON t.id_user = u.id_user
              WHERE t.id_transaksi = $id_transaksi AND t.id_user = $id_user";

$query = mysqli_query($conn, $query_str);

if (!$query || mysqli_num_rows($query) == 0) {
    die("Tiket tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);

// --- LOGIKA DATA ---
$tgl_raw = $data['tgl_berangkat'] ?? $data['tanggal'] ?? date('Y-m-d');
$tgl_tampil = date('l, d M Y', strtotime($tgl_raw));
$jam_tampil = substr($data['jam_berangkat'] ?? $data['jam'] ?? '00:00', 0, 5);
$kereta_tampil = $data['nama_kereta'] ?? $data['kereta'] ?? 'BERANGKATIN EXPRESS';

// Simulasi Kursi
$wagon = "EKS-" . (($id_transaksi % 3) + 1);
$seat = chr(65 + ($id_transaksi % 4)) . (($id_transaksi % 12) + 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket #<?= $id_transaksi ?> - BERANGKATIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --accent-orange: #ff6b00;
            --bg-light: #f4f7fe;
        }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1a202c;
            padding: 40px 15px;
            position: relative;
        }

        .bg-fixed {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: url('assets/images/background1.jpg') no-repeat center center;
            background-size: cover; z-index: -2;
        }

        .bg-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(30, 58, 138, 0.1);
            backdrop-filter: blur(8px); z-index: -1;
        }

        .ticket-container { max-width: 850px; margin: auto; }

        .e-ticket {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px; overflow: hidden;
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
            position: relative; backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .e-ticket-header {
            background: linear-gradient(135deg, var(--primary-blue), #1e40af);
            color: white; padding: 25px 40px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .brand-logo { font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px; }
        .ticket-type { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; opacity: 0.9; }

        .ticket-body { padding: 40px; }

        .route-section {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 40px; background: rgba(248, 250, 252, 0.6);
            padding: 30px; border-radius: 20px; border: 1px solid #edf2f7;
        }

        .st-box h2 { 
            font-family: 'JetBrains Mono', monospace; font-size: 2.5rem; 
            font-weight: 800; margin: 0; color: #1e293b;
        }
        .st-box p { font-size: 0.9rem; color: #64748b; margin: 0; font-weight: 600; text-transform: uppercase; }

        .route-line { flex-grow: 1; margin: 0 30px; position: relative; height: 2px; background: #cbd5e1; }
        .route-line::after {
            content: '🚂'; position: absolute; top: -15px; left: 50%;
            transform: translateX(-50%); background: #f8fafc; padding: 0 10px; font-size: 1.5rem;
        }

        .detail-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 40px; }

        .info-group label {
            display: block; font-size: 0.7rem; text-transform: uppercase;
            color: #94a3b8; font-weight: 800; margin-bottom: 5px; letter-spacing: 0.5px;
        }

        .info-group span { font-size: 1.1rem; font-weight: 700; color: #1e293b; }

        .ticket-footer {
            border-top: 2px dashed #cbd5e1; padding-top: 35px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .seat-highlight {
            background: var(--primary-blue); color: white;
            padding: 20px 35px; border-radius: 18px; text-align: center;
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);
        }

        .seat-highlight .label { font-size: 0.7rem; text-transform: uppercase; opacity: 0.8; font-weight: 700; display: block; }
        .seat-highlight .value { font-family: 'JetBrains Mono', monospace; font-size: 1.8rem; font-weight: 800; }

        .qr-mockup { width: 80px; height: 80px; background: #fff; padding: 5px; border: 1px solid #eee; border-radius: 8px; }

        .booking-info { text-align: right; }
        .booking-code {
            font-family: 'JetBrains Mono', monospace; font-size: 1.5rem;
            font-weight: 800; color: var(--primary-blue); display: block;
        }

        .status-pill {
            display: inline-block; background: #dcfce7; color: #166534;
            padding: 6px 16px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; margin-bottom: 10px;
        }

        .btn-print { background: #1a202c; color: white; border: none; transition: 0.3s; }
        .btn-print:hover { background: #000; transform: translateY(-2px); color: white; }

        /* --- PERBAIKAN PRINT --- */
        @media print {
            @page { 
                margin: 0; /* Menghilangkan Header/Footer browser */
            }
            body { 
                padding: 0; 
                margin: 0;
                background: white !important; 
            }
            .no-print, .bg-fixed, .bg-overlay { display: none !important; }
            .ticket-container { max-width: 100%; margin: 0; padding: 20mm; }
            .e-ticket { 
                box-shadow: none !important; 
                border: 1px solid #eee !important; 
                background: white !important; 
                backdrop-filter: none !important;
                width: 100%;
            }
            .e-ticket-header {
                background: #1e3a8a !important; /* Force color for print */
                -webkit-print-color-adjust: exact;
            }
            .seat-highlight {
                background: #1e3a8a !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="bg-fixed"></div>
<div class="bg-overlay"></div>

<div class="ticket-container">
    <div class="mb-4 no-print">
        <a href="tiket_saya.php" class="btn btn-light shadow-sm rounded-pill px-4 fw-bold text-decoration-none">
            ← Kembali ke Tiket Saya
        </a>
    </div>

    <div class="e-ticket">
        <div class="e-ticket-header">
            <div class="brand-logo">BERANGKATIN</div>
            <div class="ticket-type">E-Ticket / Boarding Pass</div>
        </div>

        <div class="ticket-body">
            <div class="route-section">
                <div class="st-box">
                    <h2><?= strtoupper(substr($data['stasiun_asal'], 0, 3)) ?></h2>
                    <p><?= $data['stasiun_asal'] ?></p>
                </div>
                <div class="route-line"></div>
                <div class="st-box text-end">
                    <h2><?= strtoupper(substr($data['stasiun_tujuan'], 0, 3)) ?></h2>
                    <p><?= $data['stasiun_tujuan'] ?></p>
                </div>
            </div>

            <div class="detail-grid">
                <div class="info-group">
                    <label>Nama Penumpang</label>
                    <span><?= strtoupper($data['nama']) ?></span>
                </div>
                <div class="info-group">
                    <label>Nama Kereta</label>
                    <span><?= $kereta_tampil ?></span>
                </div>
                <div class="info-group">
                    <label>Kelas</label>
                    <span>EKSEKUTIF</span>
                </div>
                <div class="info-group">
                    <label>Tanggal Berangkat</label>
                    <span><?= $tgl_tampil ?></span>
                </div>
                <div class="info-group">
                    <label>Waktu Keberangkatan</label>
                    <span><?= $jam_tampil ?> WIB</span>
                </div>
                <div class="info-group">
                    <label>Nomor Peron</label>
                    <span>JALUR <?= ($id_transaksi % 4) + 1 ?></span>
                </div>
            </div>

            <div class="ticket-footer">
                <div class="d-flex align-items-center">
                    <div class="seat-highlight me-4">
                        <span class="label">Gerbong / Kursi</span>
                        <span class="value"><?= $wagon ?> / <?= $seat ?></span>
                    </div>
                    <div class="qr-mockup d-none d-md-block">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=BRK<?= $id_transaksi ?>" alt="QR Code">
                    </div>
                </div>

                <div class="booking-info">
                    <div class="status-pill">LUNAS / PAID</div>
                    <label class="d-block text-muted small fw-bold text-uppercase">Kode Booking</label>
                    <span class="booking-code">BRK<?= str_pad($id_transaksi, 5, '0', STR_PAD_LEFT) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 p-4 rounded-4 shadow-sm no-print" style="background: rgba(255,255,255,0.8); border-left: 6px solid var(--accent-orange); backdrop-filter: blur(5px);">
        <h6 class="fw-bold mb-2">Informasi Penting:</h6>
        <ul class="text-muted small mb-0 ps-3">
            <li>Tunjukkan QR Code atau Kode Booking kepada petugas boarding.</li>
            <li>Wajib membawa tanda pengenal asli (KTP/Passport) yang sah.</li>
            <li>Check-in paling lambat 30 menit sebelum keberangkatan.</li>
        </ul>
    </div>

    <div class="text-center mt-5 no-print">
        <button onclick="handlePrint()" class="btn btn-print btn-lg px-5 rounded-pill fw-bold shadow">
            <i class="fas fa-print me-2"></i> Cetak Tiket Sekarang
        </button>
    </div>
</div>

<script>
    function handlePrint() {
        // Memberikan sedikit jeda agar browser memproses styling print
        setTimeout(() => {
            window.print();
        }, 200);
    }
</script>

</body>
</html>