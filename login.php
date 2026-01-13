<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BERANGKATIN</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body { 
            background-image: url('assets/images/bg_depan.jpg?v=<?php echo time(); ?>') !important; 
            background-repeat: no-repeat !important;
            background-position: center center !important;
            background-attachment: fixed !important;
            background-size: cover !important;
            background-color: #e9ecef;
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
            margin: 0;
            overflow-x: hidden;
        }

        .ticket-container {
            background: white; border-radius: 20px; overflow: hidden;
            display: flex; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            max-width: 800px; width: 100%; margin: auto;
        }

        .ticket-left {
            background: #ed6b23; color: white; padding: 40px;
            flex: 0 0 280px; border-right: 2px dashed rgba(255,255,255,0.3);
            display: flex; flex-direction: column; justify-content: center;
            align-items: center; position: relative;
        }

        .ticket-left::before, .ticket-left::after {
            content: ''; position: absolute; right: -15px; width: 30px; height: 30px;
            background: #e9ecef; border-radius: 50%;
        }
        .ticket-left::before { top: -15px; }
        .ticket-left::after { bottom: -15px; }

        .ticket-right { padding: 50px; flex-grow: 1; background: white; }

        .form-control {
            border: none; border-bottom: 2px solid #e2e8f0; border-radius: 0;
            padding: 10px 5px; transition: 0.3s;
        }
        .form-control:focus { box-shadow: none; border-color: #2d2a70; }

        .btn-login {
            background: #2d2a70; color: white; border-radius: 8px;
            padding: 12px; border: none; font-weight: bold;
            width: 100%; margin-top: 20px; transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: #1a1846; transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 42, 112, 0.3);
        }

        .btn-guest {
            background: #f8f9fa; color: #2d2a70; border-radius: 8px;
            padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;
            width: 100%; margin-top: 10px; transition: 0.3s;
            text-decoration: none; display: inline-block; text-align: center; 
            font-size: 0.9rem; cursor: pointer;
        }
        .btn-guest:hover { background: #e2e8f0; color: #000; }

        .brand-font { font-weight: 800; }
    </style>
</head>
<body>

<div class="ticket-container animate__animated animate__fadeInDown">
    <div class="ticket-left d-none d-md-flex text-center">
        <i class="fas fa-ticket-alt fa-4x mb-3" style="transform: rotate(-45deg);"></i>
        <h4 class="brand-font">AYO BERANGKAT</h4>
        <p class="small opacity-75">Siap untuk perjalanan selanjutnya?</p>
    </div>

    <div class="ticket-right">
        <div class="mb-4">
            <h5 class="m-0 brand-font" style="color: #2d2a70;">
                BERA<span style="color: #ed6b23;">NGK</span>ATIN
            </h5>
        </div>

        <h3 class="fw-bold mb-1" style="color: #2d2a70;">Masuk Akun</h3>
        <p class="text-muted mb-4 small">Gunakan email terdaftar Anda</p>

        <form id="loginForm" autocomplete="off">
            <div class="mb-3">
                <label class="small fw-bold text-muted">EMAIL ADDRESS</label>
                <input type="email" name="email" class="form-control" placeholder="email@anda.com" required>
            </div>

            <div class="mb-4">
                <label class="small fw-bold text-muted">PASSWORD</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <span id="btnText">MASUK SEKARANG</span>
            </button>
            
            <button type="button" id="btnGuest" class="btn-guest">MASUK TANPA LOGIN</button>

            <div class="text-center mt-4">
                <small class="text-muted">
                    Belum punya akun?
                    <a href="register.php" class="text-decoration-none fw-bold" style="color: #ed6b23;">
                        Daftar Disini
                    </a>
                </small>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const btn = document.getElementById('btnLogin');
    const btnText = document.getElementById('btnText');

    function resetLoginButton() {
        btn.disabled = false;
        btnText.innerText = "MASUK SEKARANG";
    }

    window.addEventListener('pageshow', function(event) {
        resetLoginButton();
    });

    function clearAllLoginData() {
        const cookies = ["session_active", "user_name", "user_id"];
        cookies.forEach(name => {
            document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
        });
    }

    function setAuthCookie(name, value) {
        // Expire 1 jam, gunakan path=/ agar bisa dibaca di semua folder (termasuk folder crud)
        document.cookie = `${name}=${value}; max-age=3600; path=/`;
    }

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        btn.disabled = true;
        btnText.innerText = "Memproses...";

        clearAllLoginData();

        const formData = new FormData(this);
        
        try {
            const response = await fetch('crud/proses_login.php', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            const jsonStart = text.indexOf('{');
            if (jsonStart === -1) throw new Error("Format JSON Salah");
            const result = JSON.parse(text.substring(jsonStart));

            if (result.status === 'success') {
                setAuthCookie("session_active", "yes");
                
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: result.message, timer: 1200, showConfirmButton: false
                }).then(() => {
                    window.location.replace('dashboard.php');
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal Login', text: result.message });
                resetLoginButton();
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error', text: "Terjadi kesalahan koneksi." });
            resetLoginButton();
        }
    });

    document.getElementById('btnGuest').addEventListener('click', async function() {
        try {
            await fetch('crud/logout.php');
        } catch (err) {}

        clearAllLoginData();

        setAuthCookie("session_active", "no");
        setAuthCookie("user_name", "Tamu");
        setAuthCookie("user_id", "0");

        window.location.replace('dashboard.php');
    });
</script>

</body>
</html>