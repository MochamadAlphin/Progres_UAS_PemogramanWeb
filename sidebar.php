<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$s_active = $_SESSION['session_active'] ?? $_COOKIE['session_active'] ?? 'no';
$user_name = $_SESSION['user_name'] ?? $_COOKIE['user_name'] ?? 'Tamu';

$is_guest = ($s_active !== 'yes');

if ($is_guest) {
    $display_name = "Tamu";
    $display_status = "GUEST MODE";
    $initials = "G";
    $avatar_bg = "#6c757d"; 
} else {
    $display_name = $user_name;
    $display_status = "PLATINUM PASS";
    $initials = strtoupper(substr($display_name, 0, 2));
    $avatar_bg = "#1e3a8a"; 
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar - BERANGKATIN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=JetBrains+Mono:wght@500&display=swap');

        :root {
            --primary-blue: #1e3a8a;
            --accent-orange: #ff6b00;
            --bg-light: #f4f7fe;
            --text-muted: #8a92a6;
            --ticket-border: #e2e8f0;
        }

        .sidebar { 
            width: 280px; 
            height: 95vh; 
            background: white; 
            position: fixed; 
            margin: 20px;
            padding: 0; 
            border-radius: 24px;
            display: flex;
            flex-direction: column;
            box-shadow: 15px 0 35px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #fff;
            z-index: 1000;
        }

        .logo-container {
            padding: 0;
            background: var(--primary-blue);
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 180px; 
            z-index: 10;
        }

        .logo-img {
            width: 260px; 
            height: 260px;
            position: absolute;
            top: 50%; 
            left: 50%;
            transform: translate(-50%, -50%);
            display: block;
            object-fit: contain;
            filter: drop-shadow(0 10px 25px rgba(0,0,0,0.2));
        }

        .logo-container::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 20px;
            background-image: radial-gradient(circle, transparent 10px, white 10px);
            background-size: 30px 40px;
            background-position: 0 -20px;
            z-index: 2;
        }

        .nav-menu {
            padding: 25px 20px 10px 20px; 
            flex-grow: 1;
        }

        .nav-link { 
            display: flex;
            align-items: center;
            color: var(--text-muted); 
            padding: 14px 20px; 
            border-radius: 12px; 
            margin-bottom: 6px; 
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary-blue);
            background: #f1f5f9;
        }

        .nav-link.active { 
            background: #f8fafc;
            color: var(--primary-blue) !important;
            border: 1px solid var(--ticket-border);
        }

        .nav-link.locked {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .lock-icon {
            margin-left: auto;
            font-size: 0.8rem;
        }

        .divider {
            border-top: 2px dashed #e2e8f0;
            margin: 15px 25px;
        }

        .sidebar-footer {
            padding: 20px;
            background: #fafafa;
        }

        .user-profile {
            background: white;
            padding: 12px;
            border-radius: 16px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .avatar-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'JetBrains Mono', monospace;
            font-weight: bold;
        }

        .user-info .name {
            font-size: 0.85rem;
            font-weight: 800;
            color: #1a202c;
            display: block;
        }

        .user-info .status {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.65rem;
            color: var(--accent-orange);
            text-transform: uppercase;
        }

        .logout-btn {
            background: white;
            color: #e53e3e !important;
            border: 1px solid #fed7d7;
            justify-content: center;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .logout-btn:hover { background: #fff5f5; }
        
        .login-btn-guest {
            border-color: var(--primary-blue);
            color: var(--primary-blue) !important;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="assets/images/logo.png" alt="Logo" class="logo-img">
    </div>

    <nav class="nav-menu">
        <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
            <span style="margin-right:12px; font-size: 1.1rem;">📊</span> Dashboard
        </a>
        
        <a class="nav-link <?= ($current_page == 'cari_jadwal.php') ? 'active' : '' ?>" href="cari_jadwal.php">
            <span style="margin-right:12px; font-size: 1.1rem;">🔍</span> Cari Jadwal
        </a>
        
        <a class="nav-link <?= ($is_guest) ? 'locked' : (($current_page == 'tiket_saya.php') ? 'active' : '') ?>" href="tiket_saya.php">
            <span style="margin-right:12px; font-size: 1.1rem;">🎫</span> Tiket Saya
            <?= ($is_guest) ? '<i class="fas fa-lock lock-icon"></i>' : '' ?>
        </a>

        <a class="nav-link <?= ($is_guest) ? 'locked' : (($current_page == 'voucher.php') ? 'active' : '') ?>" href="voucher.php">
            <span style="margin-right:12px; font-size: 1.1rem;">🎁</span> Voucher Saya
            <?= ($is_guest) ? '<i class="fas fa-lock lock-icon"></i>' : '' ?>
        </a>
        
        <a class="nav-link <?= ($is_guest) ? 'locked' : (($current_page == 'profil.php') ? 'active' : '') ?>" href="profil.php">
            <span style="margin-right:12px; font-size: 1.1rem;">👤</span> Profil
            <?= ($is_guest) ? '<i class="fas fa-lock lock-icon"></i>' : '' ?>
        </a>
    </nav>

    <div class="divider"></div>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="avatar-box" style="background-color: <?= $avatar_bg ?>;">
                <?= $initials ?>
            </div>
            <div class="user-info">
                <span class="name"><?= $display_name ?></span>
                <span class="status" style="<?= ($is_guest) ? 'color: #888;' : '' ?>">
                    <?= $display_status ?>
                </span>
            </div>
        </div>

        <?php if ($is_guest): ?>
            <a class="logout-btn login-btn-guest" href="login.php">
                <i class="fas fa-sign-in-alt me-2"></i> <span>Login Akun</span>
            </a>
        <?php else: ?>
            <a class="logout-btn" href="./crud/logout.php">
                <i class="fas fa-power-off me-2"></i> <span>Keluar Sistem</span>
            </a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>