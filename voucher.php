<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/connection.php';

if (!$conn) {
    die("Koneksi database gagal. Periksa config/connection.php");
}

// 2. Query data voucher aktif
$query = "SELECT * FROM vouchers WHERE status = 'aktif' AND tgl_expired >= CURDATE() ORDER BY tgl_expired ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    $error_db = "Gagal mengambil data. Pastikan tabel 'vouchers' sudah ada di database.";
}

function getGradient($index) {
    $gradients = [
        'linear-gradient(135deg, #1e3a8a, #3b82f6)', 
        'linear-gradient(135deg, #0d9488, #2dd4bf)', 
        'linear-gradient(135deg, #7c3aed, #a78bfa)', 
        'linear-gradient(135deg, #c2410c, #f97316)'
    ];
    return $gradients[$index % count($gradients)];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Saya - BERANGKATIN</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --bg-light: #f4f7fe;
            --sidebar-width: 280px;
        }
        
        body { 
            background-color: var(--bg-light); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            overflow-x: hidden; 
        }
        
        .bg-fixed { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: url('assets/images/background1.jpg') no-repeat center center; background-size: cover; z-index: -2; }
        .bg-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background: linear-gradient(180deg, rgba(244, 247, 254, 0.8) 0%, rgba(244, 247, 254, 0.95) 100%); }
        
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            min-height: 100vh; 
            position: relative; 
            transition: all 0.3s;
        }

        .breadcrumb-item a { 
            color: var(--secondary-blue); /* Home jadi biru */
            font-weight: 700; 
            text-decoration: none; 
            transition: 0.2s;
        }
        .breadcrumb-item a:hover { 
            color: var(--primary-blue); 
            text-decoration: underline; 
        }
        .breadcrumb-item.active { 
            color: #64748b !important; /* Voucher Saya jadi abu-abu */
            font-weight: 600; 
        }

        /* VOUCHER CARD DESIGN */
        .voucher-card { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(10px);
            border-radius: 24px; 
            display: flex; 
            overflow: hidden; 
            border: 1px solid rgba(255,255,255,0.5); 
            margin-bottom: 25px; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            position: relative; 
        }
        
        .voucher-card:hover { 
            transform: translateX(10px); 
            box-shadow: 0 20px 40px rgba(30, 58, 138, 0.12); 
            border-color: var(--secondary-blue); 
            background: rgba(255, 255, 255, 0.9); 
        }
        
        .voucher-left { 
            color: white; 
            padding: 30px; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            min-width: 180px; 
            text-align: center; 
            position: relative; 
        }
        
        .voucher-left::after { 
            content: ""; 
            position: absolute; 
            right: -10px; 
            top: 0; 
            bottom: 0; 
            width: 20px; 
            background-image: radial-gradient(circle, var(--bg-light) 8px, transparent 8px); 
            background-size: 20px 30px; 
            background-position: 10px 0; 
            z-index: 3; 
        }
        
        .voucher-right { 
            padding: 25px 40px; 
            flex-grow: 1; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        
        .promo-code { 
            font-family: 'JetBrains Mono', monospace; 
            background: #f1f5f9; 
            padding: 10px 18px; 
            border-radius: 12px; 
            border: 2px dashed #cbd5e1; 
            color: var(--primary-blue); 
            font-size: 1.1rem; 
            display: inline-block; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.2s;
        }

        .promo-code:hover {
            background: #e2e8f0;
            border-color: var(--secondary-blue);
        }
        
        .btn-use { 
            background: var(--primary-blue); 
            color: white; 
            border: none; 
            padding: 12px 25px; 
            border-radius: 14px; 
            font-weight: 700; 
            transition: 0.3s; 
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
        }
        
        .btn-use:hover { 
            background: var(--secondary-blue); 
            transform: translateY(-2px); 
            color: white; 
        }

        @media (max-width: 1200px) {
            .voucher-right { flex-direction: column; align-items: flex-start; gap: 20px; }
            .voucher-right .text-end { text-align: left !important; width: 100%; }
        }

        @media (max-width: 992px) { 
            .main-content { margin-left: 0; padding: 20px; } 
            .voucher-card { flex-direction: column; } 
            .voucher-left::after { display: none; } 
            .voucher-left { min-width: 100%; padding: 25px; border-radius: 24px 24px 0 0; } 
            .voucher-right { border-radius: 0 0 24px 24px; }
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
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Voucher Saya</li>
                    </ol>
                </nav>
                <h2 class="fw-bold m-0" style="color: var(--primary-blue); font-weight: 800;">Promo & Penawaran 🎁</h2>
                <p class="text-muted m-0">Gunakan voucher di bawah ini saat checkout untuk perjalanan lebih hemat.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-11">
                <?php 
                if (isset($error_db)) {
                    echo "<div class='alert alert-danger shadow-sm animate__animated animate__shakeX'>$error_db</div>";
                } 
                elseif ($result && mysqli_num_rows($result) > 0) {
                    $i = 0;
                    while ($row = mysqli_fetch_assoc($result)) { 
                        $display_potongan = ($row['potongan_harga'] <= 100) ? $row['potongan_harga'] . "%" : "Rp " . number_format($row['potongan_harga'], 0, ',', '.');
                        $label_potongan = ($row['potongan_harga'] <= 100) ? "DISKON" : "POTONGAN";
                        $left_text = ($row['potongan_harga'] <= 100) ? $row['potongan_harga'] . "%" : number_format($row['potongan_harga']/1000, 0) . "rb";
                        $gradient = getGradient($i);
                ?>
                    <div class="voucher-card" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                        <div class="voucher-left" style="background: <?= $gradient ?>;">
                            <h2 class="mb-0" style="font-weight: 800; font-size: 2.5rem;"><?= $left_text ?></h2>
                            <small class="opacity-75 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1.5px;"><?= $label_potongan ?></small>
                        </div>

                        <div class="voucher-right">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h5 class="fw-bold mb-0" style="color: #1e293b; font-size: 1.25rem;">Hemat <?= $display_potongan ?></h5>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2" style="font-size: 0.65rem;">SISA KUOTA: <?= $row['kuota'] ?></span>
                                </div>
                                <p class="text-muted small mb-3">Klik kode untuk menyalin dan gunakan pada halaman pembayaran.</p>
                                <div class="promo-code" onclick="copyText('<?= $row['kode_voucher'] ?>', this.parentElement.parentElement.querySelector('.btn-use'))" title="Klik untuk menyalin">
                                    <i class="fas fa-ticket-alt me-2 opacity-50"></i><?= $row['kode_voucher'] ?>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <p class="small text-muted mb-2"><i class="far fa-clock me-1"></i> Berlaku s/d <b><?= date('d M Y', strtotime($row['tgl_expired'])) ?></b></p>
                                <button class="btn-use" onclick="copyText('<?= $row['kode_voucher'] ?>', this)">
                                    <i class="fas fa-copy me-1"></i> SALIN KODE
                                </button>
                            </div>
                        </div>
                    </div>
                <?php 
                    $i++;
                    } 
                } else { ?>
                    <div class="text-center py-5 rounded-4" style="background: rgba(255,255,255,0.4); border: 2px dashed #cbd5e1;" data-aos="zoom-in">
                        <img src="https://cdn-icons-png.flaticon.com/512/4072/4072217.png" width="80" class="mb-3 opacity-50" alt="Empty">
                        <h4 class="fw-bold text-muted">Belum ada voucher tersedia</h4>
                        <p class="text-muted small">Cek kembali nanti untuk promo menarik lainnya!</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        function copyText(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = btn.innerHTML;
                const originalBg = btn.style.background;
                
                btn.innerHTML = '<i class="fas fa-check"></i> TERSALIN';
                btn.style.background = '#10b981'; 
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = originalBg; 
                }, 2000);
            });
        }
    </script>
</body>
</html>