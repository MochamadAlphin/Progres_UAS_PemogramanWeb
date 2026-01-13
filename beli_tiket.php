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

$id_jadwal = isset($_GET['id_jadwal']) ? (int)$_GET['id_jadwal'] : 0;

if ($id_jadwal <= 0) {
    echo "<script>alert('Jadwal tidak ditemukan!'); window.location.href='cari_jadwal.php';</script>";
    exit();
}

$nama_kereta = $_GET['kereta'] ?? '';
$harga_asli  = $_GET['harga'] ?? '0';

$query_jadwal = mysqli_query($conn, "SELECT * FROM jadwal WHERE id_jadwal = $id_jadwal");
if ($data_jadwal = mysqli_fetch_assoc($query_jadwal)) {
    if (empty($nama_kereta)) {
        $nama_kereta = $data_jadwal['nama_kereta'] ?? $data_jadwal['kereta'] ?? 'Kereta Api';
    }
    if ($harga_asli == '0') {
        $harga_asli = $data_jadwal['harga'];
    }
} else if (empty($nama_kereta)) {
    $nama_kereta = "Kereta Tidak Diketahui";
}

$harga_numeric = (int)str_replace(['.', ','], '', $harga_asli);

$u_nama = $u_nik = $u_hp = $u_email = '';
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id_user'");
if ($row_user = mysqli_fetch_assoc($query_user)) {
    $u_nama  = $row_user['nama_lengkap'] ?? $row_user['nama'] ?? '';
    $u_nik   = $row_user['nik'] ?? ''; 
    $u_hp    = $row_user['no_hp'] ?? $row_user['hp'] ?? ''; 
    $u_email = $row_user['email'] ?? '';
}

$query_voucher = mysqli_query($conn, "SELECT * FROM vouchers WHERE status = 'aktif' AND kuota > 0 AND tgl_expired >= CURDATE()");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Tiket - BERANGKATIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary-blue: #1e3a8a; --accent-orange: #ff6b00; --bg-light: #f4f7fe; }
        
        /* BACKGROUND SETTINGS */
        body { 
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif; 
            padding: 40px 0; 
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
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(244, 247, 254, 0.88); /* Overlay putih biru transparan */
            z-index: -1;
        }

        .checkout-card { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(10px);
            border-radius: 20px; 
            border: 1px solid rgba(255, 255, 255, 0.5); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            padding: 30px; 
        }

        .ticket-info-banner { background: linear-gradient(90deg, #1e3a8a, #3b82f6); color: white; border-radius: 15px; padding: 20px; margin-bottom: 30px; }
        .form-label { font-weight: 700; font-size: 0.75rem; color: #64748b; letter-spacing: 0.5px; }
        .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #e2e8f0; background-color: rgba(248, 250, 252, 0.8); }
        
        .payment-method { border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; margin-bottom: 10px; background: white; }
        .payment-method:hover { border-color: var(--primary-blue); background-color: #f0f7ff; }
        .payment-check:checked + .payment-method { border-color: var(--primary-blue); background-color: #f0f7ff; box-shadow: 0 0 0 1px var(--primary-blue); }
        .payment-check { display: none; }
        
        .btn-pay { background: var(--accent-orange); color: white; border: none; border-radius: 12px; padding: 16px; width: 100%; font-weight: 700; font-size: 1rem; transition: 0.3s; margin-top: 20px; cursor: pointer; }
        .btn-pay:hover { background: #e66000; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 107, 0, 0.3); }
        .sticky-summary { position: sticky; top: 20px; }
    </style>
</head>
<body>

<div class="bg-fixed"></div>
<div class="bg-overlay"></div>

<div class="container" style="max-width: 850px;">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="cari_jadwal.php" class="text-decoration-none text-muted fw-bold small">← KEMBALI KE PENCARIAN</a>
        <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill">🔒 Secure Checkout</span>
    </div>

    <form id="formTiket" action="crud/proses_beli.php" method="POST">
        <input type="hidden" name="id_user" value="<?= (int)$id_user ?>">
        <input type="hidden" name="id_jadwal" value="<?= (int)$id_jadwal ?>">
        <input type="hidden" name="harga_asli" value="<?= (int)$harga_numeric ?>">
        <input type="hidden" name="total_bayar" id="total_bayar_input" value="<?= (int)$harga_numeric ?>">

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="checkout-card">
                    <h5 class="fw-bold mb-4">Data Penumpang</h5>
                    <div class="mb-3">
                        <label class="form-label text-uppercase">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($u_nama) ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-uppercase">NIK / No. KTP</label>
                            <input type="text" class="form-control" name="nik" value="<?= $u_nik ?>" placeholder="16 digit" required maxlength="16" pattern="\d{16}">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-uppercase">No. HP</label>
                            <input type="tel" class="form-control" name="hp" value="<?= $u_hp ?>" placeholder="0812..." required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-uppercase">Alamat Email</label>
                        <input type="email" class="form-control" name="email" value="<?= $u_email ?>" placeholder="tiket@email.com" required>
                    </div>

                    <hr class="my-4" style="border-style: dashed;">
                    
                    <h5 class="fw-bold mb-3">Pakai Promo</h5>
                    <div class="mb-4">
                        <label class="form-label text-uppercase">Pilih Voucher</label>
                        <select class="form-select" name="id_voucher" id="select_voucher">
                            <option value="" data-potongan="0">Tidak Menggunakan Voucher</option>
                            <?php while($v = mysqli_fetch_assoc($query_voucher)): ?>
                                <option value="<?= $v['id_voucher'] ?>" data-potongan="<?= $v['potongan_harga'] ?>">
                                    <?= htmlspecialchars($v['kode_voucher']) ?> - (Rp <?= number_format($v['potongan_harga'], 0, ',', '.') ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <h5 class="fw-bold mb-3">Metode Pembayaran</h5>
                    <input type="radio" name="pay_method" id="qris" value="QRIS" class="payment-check" checked>
                    <label for="qris" class="payment-method">
                        <span class="me-3">📱</span>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">QRIS / E-Wallet</div>
                            <div class="text-muted" style="font-size: 0.7rem;">GoPay, OVO, Dana</div>
                        </div>
                    </label>

                    <input type="radio" name="pay_method" id="va" value="Virtual Account" class="payment-check">
                    <label for="va" class="payment-method">
                        <span class="me-3">🏦</span>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">Virtual Account</div>
                            <div class="text-muted" style="font-size: 0.7rem;">BCA, Mandiri, BNI</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="checkout-card sticky-summary">
                    <h5 class="fw-bold mb-3">Ringkasan Tiket</h5>
                    <div class="ticket-info-banner">
                        <div class="fw-bold" style="font-size: 1.1rem;"><?= htmlspecialchars($nama_kereta) ?></div>
                        <div class="small opacity-75">Tiket Kereta Api • <?= date('d M Y') ?></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Harga Tiket</span>
                        <span class="fw-bold small">Rp <?= number_format($harga_numeric, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Potongan Voucher</span>
                        <span class="fw-bold small text-danger" id="display_potongan">- Rp 0</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">Total Bayar</span>
                        <h4 class="fw-bold text-primary mb-0" id="display_total">Rp <?= number_format($harga_numeric, 0, ',', '.') ?></h4>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agree" required>
                        <label class="form-check-label text-muted" for="agree" style="font-size: 0.75rem;">
                            Saya setuju dengan S&K yang berlaku.
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-pay" id="btnBayar">BAYAR SEKARANG</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const selectVoucher = document.getElementById('select_voucher');
    const displayPotongan = document.getElementById('display_potongan');
    const displayTotal = document.getElementById('display_total');
    const totalBayarInput = document.getElementById('total_bayar_input');
    const formTiket = document.getElementById('formTiket');
    const hargaAsli = <?= $harga_numeric ?>;

    selectVoucher.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const potongan = parseInt(selectedOption.getAttribute('data-potongan')) || 0;
        const totalBaru = Math.max(0, hargaAsli - potongan);
        
        displayPotongan.innerText = "- Rp " + potongan.toLocaleString('id-ID');
        displayTotal.innerText = "Rp " + totalBaru.toLocaleString('id-ID');
        totalBayarInput.value = totalBaru;
    });

    formTiket.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!document.getElementById('agree').checked) {
            Swal.fire('Perhatian', 'Anda harus menyetujui syarat & ketentuan.', 'info');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: "Lanjutkan pemesanan tiket <?= htmlspecialchars($nama_kereta) ?>?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e3a8a',
            confirmButtonText: 'Ya, Bayar!',
            cancelButtonText: 'Cek Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                formTiket.submit();
            }
        });
    });
</script>
</body>
</html>