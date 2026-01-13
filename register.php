<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - BERANGKATIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { 
            background-image: url('assets/images/bg_depan.jpg') !important; 
            background-repeat: no-repeat !important;
            background-position: center center !important;
            background-attachment: fixed !important;
            background-size: cover !important;
            background-color: #e9ecef; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            font-family: 'Poppins', sans-serif; 
            padding: 40px 20px; 
            margin: 0;
            overflow-x: hidden;
        }
        .ticket-container {
            background: white; border-radius: 20px; overflow: hidden; display: flex;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
            max-width: 950px; width: 100%; margin: auto;
            animation: slideInLeft; animation-duration: 0.8s;
        }
        .ticket-left {
            background: #2d2a70; color: white; padding: 40px; flex: 0 0 300px;
            border-right: 2px dashed rgba(255,255,255,0.2); display: flex;
            flex-direction: column; justify-content: center; align-items: center; position: relative;
        }
        .ticket-left::before, .ticket-left::after {
            content: ''; position: absolute; right: -15px; width: 30px; height: 30px;
            background: #e9ecef; border-radius: 50%; z-index: 10;
        }
        .ticket-left::before { top: -15px; }
        .ticket-left::after { bottom: -15px; }
        .ticket-right { padding: 40px 50px; flex-grow: 1; background: white; }
        .form-control, .form-select {
            border: none; border-bottom: 2px solid #e2e8f0; border-radius: 0;
            padding: 8px 5px; background: transparent; transition: 0.3s; font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus { box-shadow: none; border-color: #ed6b23; }
        .btn-reg {
            background: #ed6b23; color: white; border-radius: 8px; padding: 12px;
            border: none; font-weight: bold; width: 100%; margin-top: 20px; transition: all 0.3s ease;
        }
        .btn-reg:hover { background: #d45a1d; transform: scale(1.02); box-shadow: 0 5px 15px rgba(237, 107, 35, 0.3); }
        .brand-font { font-weight: 800; letter-spacing: -0.5px; }
    </style>
</head>
<body>

<div class="ticket-container animate__animated animate__fadeInUp">
    <div class="ticket-left d-none d-md-flex text-center">
        <div class="mb-4">
            <i class="fas fa-train fa-4x mb-3 animate__animated animate__pulse animate__infinite"></i>
        </div>
        <h4 class="brand-font">AYO DAFTAR</h4>
        <p class="small opacity-75">Daftarkan diri Anda untuk kemudahan memesan tiket kereta api.</p>
    </div>

    <div class="ticket-right">
        <div class="text-center text-md-start mb-4">
            <h5 class="m-0 brand-font" style="color: #2d2a70;">
                BERA<span style="color: #ed6b23;">NGK</span>ATIN
            </h5>
        </div>

        <h3 class="fw-bold mb-1" style="color: #2d2a70;">Registrasi Penumpang</h3>
        <p class="text-muted mb-4 small">Pastikan data sesuai dengan KTP asli.</p>

        <form action="crud/proses_register.php" method="POST">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="small fw-bold text-muted">NAMA LENGKAP</label>
                    <input type="text" name="nama" class="form-control" placeholder="Budi Santoso" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold text-muted">NOMOR NIK</label>
                    <input type="text" name="nik" class="form-control" placeholder="16 Digit NIK" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold text-muted">NOMOR TELEPON</label>
                    <input type="tel" name="telp" class="form-control" placeholder="0812xxxx" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="small fw-bold text-muted">EMAIL</label>
                    <input type="email" name="email" class="form-control" placeholder="budi@email.com" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="small fw-bold text-muted">GENDER</label>
                    <select name="gender" class="form-select" required>
                        <option value="" selected disabled>Pilih</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold text-muted">PASSWORD</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small fw-bold text-muted">KONFIRMASI</label>
                    <input type="password" name="konfirmasi" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" name="register" class="btn-reg">KONFIRMASI PENDAFTARAN</button>
            <div class="text-center mt-4">
                <small class="text-muted">Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold" style="color: #2d2a70;">Login Disini</a></small>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>