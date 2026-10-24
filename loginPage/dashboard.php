<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: auth.php");
        exit;
    }

    $allowed = [
        'home',
        'dashboard',
        'user',
        'ruangan',
        'laporan_keseluruhan',
        'laporan_akses',
        'laporan_fuzzy'
    ];

    $page = $_GET['page'] ?? 'home';

    if (!in_array($page, $allowed)) {
        $page = 'home';
    }

    switch($page){
        case 'laporan_keseluruhan':
            $file = __DIR__ . "/../laporan/laporanKeseluruhan.php";
            break;

        case 'laporan_akses':
            $file = __DIR__ . "/../laporan/laporanAkses.php";
            break;

        case 'laporan_fuzzy':
            $file = __DIR__ . "/../laporan/laporanAnalisisAkses.php";
            break;

        default:
            $file = __DIR__ . "/../pages/$page.php";
            break;
    }

    $isLaporan = in_array($page, [
        'laporan_keseluruhan',
        'laporan_akses',
        'laporan_fuzzy'
    ]);
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../source/LogoMasjid.png" type="image/png">
        <title>RFID Monitor</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Segoe UI', sans-serif;
            }

            body {
                display: flex;
                min-height: 100vh;
                background: #0f172a;
                color: #fff;
            }

            .sidebar {
                width: 260px;
                background: linear-gradient(180deg, #0f172a, #020617);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 20px;
            }

            .sidebar h2 {
                margin-bottom: 30px;
                font-size: 22px;
                color: #38bdf8;
                cursor: pointer;
            }

            .sidebar h2:hover {
                color: #0ea5e9;
            }

            .menu a {
                display: block;
                padding: 12px;
                margin: 6px 0;
                text-decoration: none;
                color: #cbd5f5;
                border-radius: 8px;
                transition: 0.2s;
            }

            .menu a:hover {
                background: #1e293b;
                color: #fff;
            }

            .menu-item {
                position: relative;
            }

            .dropdown-toggle {
                display: block;
                padding: 12px;
                margin: 6px 0;
                color: #cbd5f5;
                border-radius: 8px;
                cursor: pointer;
            }

            .dropdown-toggle:hover {
                background: #1e293b;
                color: #fff;
            }

            .submenu {
                max-height: 0;
                overflow: hidden;
                transition: all 0.3s ease;
                padding-left: 10px;
            }

            .submenu a {
                font-size: 13px;
                padding: 10px;
                margin: 4px 0;
                background: #020617;
                border-radius: 6px;
            }

            .submenu a:hover {
                background: #1e293b;
                padding-left: 15px;
            }

            .menu-item.active .submenu {
                max-height: 300px;
            }

            .content {
                flex: 1;
                padding: 30px;
                overflow-x: auto;
            }

            .card {
                background: #1e293b;
                padding: 20px;
                border-radius: 10px;
                max-width: 100%;
                box-shadow: 0 4px 10px rgba(0,0,0,0.3);
                margin-top: 20px;
            }

            .profile {
                border-top: 1px solid #1e293b;
                padding-top: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .profile img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
            }

            .profile p {
                font-size: 14px;
            }

            .logout {
                display: block;
                font-size: 13px;
                color: #ef4444;
                text-decoration: none;
            }

            .logout:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="sidebar">
            <div>
                <h2 onclick="window.location.href='?page=home'">Monitoring RFID</h2>

                <div class="menu">
                    <a href="?page=dashboard">🏠 Dashboard</a>
                    <a href="?page=user">👤 Kelola Pengguna</a>
                    <a href="?page=ruangan">🏢 Kelola Ruangan</a>

                    <div class="menu-item dropdown <?= $isLaporan ? 'active' : '' ?>">
                        <a href="#" class="dropdown-toggle">📊 Laporan ▾</a>
                        <div class="submenu">
                            <a href="?page=laporan_keseluruhan">📄 Laporan Keseluruhan</a>
                            <a href="?page=laporan_akses">🔐 Laporan Akses</a>
                            <a href="?page=laporan_fuzzy">📈 Analisis Akses</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile">
                <?php
                $foto = $_SESSION['foto'] ?? 'default.png';
                ?>

                <img src="../source/profile/<?php echo $foto; ?>" alt="profile">
                <div>
                    <p><b><?= $_SESSION['username']; ?></b></p>
                    <p style="opacity:0.7; font-size:12px;"><?= $_SESSION['jabatan'] ?? '-'; ?></p>
                    <a class="logout" href="auth.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="content">
        <?php
        if (file_exists($file)) {
            include $file;
        } else {
            echo "<h2>Halaman tidak ditemukan</h2>";
        }
        ?>
        </div>

        <script>
            document.querySelectorAll('.dropdown-toggle').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.parentElement.classList.toggle('active');
                });
            });
        </script>

        <?php include __DIR__ . '/../components/popupStatus.php'; ?>
        <?php include __DIR__ . '/../components/popupDelete.php'; ?>
        <?php include __DIR__ . '/../components/popupScan.php'; ?>
    </body>
</html>