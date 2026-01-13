<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('crud/cari_data.php'); 

$status_login = isset($_SESSION['session_active']) ? strtolower(trim($_SESSION['session_active'])) : 'no'; 

$val_asal = isset($_GET['asal']) ? htmlspecialchars($_GET['asal']) : '';
$val_tujuan = isset($_GET['tujuan']) ? htmlspecialchars($_GET['tujuan']) : '';
$val_tanggal = isset($_GET['tanggal']) ? htmlspecialchars($_GET['tanggal']) : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Jadwal - BERANGKATIN</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --accent-blue: #3b82f6;
            --accent-orange: #ff6b00;
            --bg-light: #f4f7fe;
            --sidebar-width: 280px;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-light);
            margin: 0;
            overflow-x: hidden;
        }

        .bg-fixed {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: url('assets/images/background1.jpg') no-repeat center center;
            background-size: cover; z-index: -2; 
        }

        .bg-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background: linear-gradient(180deg, rgba(244, 247, 254, 0.8) 0%, rgba(244, 247, 254, 0.95) 100%);
        }

        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            min-height: 100vh; 
            transition: all 0.3s;
        }

        .search-container {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.2);
            margin-bottom: 40px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .form-label-custom {
            color: rgba(255,255,255,0.9);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .glass-input {
            background: white !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 12px 15px !important;
            font-weight: 600;
        }

        .ticket-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.4);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ticket-card:hover {
            transform: translateY(-5px) scale(1.01);
            background: white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            border-color: var(--accent-blue);
        }

        .track-container {
            flex-grow: 1;
            padding: 0 40px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .track-line {
            height: 2px;
            background: #cbd5e1;
            width: 100%;
            position: relative;
        }

        .track-line::after {
            content: "🚂";
            position: absolute;
            top: -14px;
            left: 0;
            font-size: 1.2rem;
            transition: 1.5s cubic-bezier(0.45, 0, 0.55, 1);
        }

        .ticket-card:hover .track-line::after {
            left: 90%;
        }

        .station-box h4 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-blue);
            margin: 0;
        }

        .station-box p {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin: 0;
            text-transform: uppercase;
        }

        .price-tag {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--accent-orange);
        }

        .btn-booking {
            background: var(--primary-blue);
            color: white;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-booking:hover {
            background: var(--accent-orange);
            color: white;
            box-shadow: 0 8px 15px rgba(255, 107, 0, 0.3);
        }

        .breadcrumb-item a { color: var(--accent-blue); text-decoration: none; font-weight: 700; }
        .breadcrumb-item.active { color: #64748b; font-weight: 700; }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 20px; }
            .ticket-card { flex-direction: column; text-align: center; gap: 20px; }
            .track-container { width: 100%; padding: 20px 0; }
        }
    </style>
</head>
<body>

<div class="bg-fixed"></div>
<div class="bg-overlay"></div>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <div class="mb-5 animate__animated animate__fadeIn">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Cari Jadwal</li>
            </ol>
        </nav>
        <h2 class="fw-bold" style="color: var(--primary-blue);">Cari Jadwal Kereta 🚂</h2>
        <p class="text-muted">Pilih rute dan temukan kenyamanan perjalanan Anda.</p>
    </div>

    <div class="search-container animate__animated animate__fadeInUp">
        <form action="" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-custom">Asal</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-white"><i class="fas fa-train-departure text-primary"></i></span>
                        <select class="form-select glass-input shadow-none" name="asal">
                            <option value="">Pilih Stasiun Asal</option>
                            <option value="Jakarta (GMR)" <?= ($val_asal == 'Jakarta (GMR)') ? 'selected' : '' ?>>Jakarta (GMR)</option>
                            <option value="Bandung (BDG)" <?= ($val_asal == 'Bandung (BDG)') ? 'selected' : '' ?>>Bandung (BDG)</option>
                            <option value="Surabaya (SBI)" <?= ($val_asal == 'Surabaya (SBI)') ? 'selected' : '' ?>>Surabaya (SBI)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Tujuan</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-white"><i class="fas fa-train-arrival text-success"></i></span>
                        <select class="form-select glass-input shadow-none" name="tujuan">
                            <option value="">Pilih Stasiun Tujuan</option>
                            <option value="Surabaya (SBI)" <?= ($val_tujuan == 'Surabaya (SBI)') ? 'selected' : '' ?>>Surabaya (SBI)</option>
                            <option value="Yogyakarta (YK)" <?= ($val_tujuan == 'Yogyakarta (YK)') ? 'selected' : '' ?>>Yogyakarta (YK)</option>
                            <option value="Bandung (BDG)" <?= ($val_tujuan == 'Bandung (BDG)') ? 'selected' : '' ?>>Bandung (BDG)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Tanggal</label>
                    <input type="date" class="form-control glass-input shadow-none" name="tanggal" value="<?= $val_tanggal ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning w-100 p-3 shadow fw-bold border-0" style="border-radius: 12px; background: var(--accent-orange); color:white;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0"><i class="fas fa-list-ul me-2"></i>Jadwal Tersedia (<?= count($results ?? []) ?>)</h5>
        <?php if($val_asal || $val_tujuan || $val_tanggal): ?>
            <a href="cari_jadwal.php" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">Reset Filter</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($results)): ?>
        <?php $delay = 100; foreach ($results as $row): ?>
        <div class="ticket-card shadow-sm" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            
            <div style="min-width: 180px;">
                <p class="text-primary fw-bold mb-1" style="font-size: 0.8rem;">
                    <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($row['tanggal'])) ?>
                </p>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($row['nama_kereta']) ?></h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                    <?= htmlspecialchars($row['kelas'] ?? 'Eksekutif') ?>
                </span>
            </div>

            <div class="track-container">
                <div class="station-box text-center">
                    <h4><?= date('H:i', strtotime($row['jam_berangkat'])) ?></h4>
                    <p><?= htmlspecialchars($row['stasiun_asal']) ?></p>
                </div>
                <div class="track-info text-center flex-grow-1 px-3">
                    <div class="track-line"></div>
                </div>
                <div class="station-box text-center">
                    <h4><?= date('H:i', strtotime($row['jam_tiba'])) ?></h4>
                    <p><?= htmlspecialchars($row['stasiun_tujuan']) ?></p>
                </div>
            </div>

            <div class="text-end ps-4" style="border-left: 2px dashed #e2e8f0; min-width: 200px;">
                <p class="text-muted small mb-1 fw-bold">Harga per orang</p>
                <div class="price-tag mb-3">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                
                <?php 
                    $link_pilih = ($status_login === 'yes') ? "beli_tiket.php?id_jadwal=".$row['id_jadwal'] : "login.php?pesan=wajib_login";
                ?>
                <a href="<?= htmlspecialchars($link_pilih) ?>" class="btn-booking">
                    Pilih Kursi <i class="fas fa-chevron-right ms-2 small"></i>
                </a>
            </div>
        </div>
        <?php $delay += 50; endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
            <img src="https://illustrations.popsy.co/blue/searching.svg" alt="Not Found" style="width: 200px;" class="mb-4">
            <h5 class="text-muted fw-bold">Jadwal tidak ditemukan</h5>
            <p class="text-muted small">Silakan pilih rute atau tanggal yang lain.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
</body>
</html>