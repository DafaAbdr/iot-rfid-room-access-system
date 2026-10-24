<?php
    $username = $_COOKIE['username'] ?? '';
    $password = $_COOKIE['password'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <title>Login | RFID Monitor</title>
        <link rel="icon" href="../source/LogoMasjid.png" type="image/png">
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
                flex-wrap: wrap;
            }

            .left {
                width: 50%;
                background: #0f172a;
                color: white;
                padding: 40px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .left-top {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .left-top img {
                width: 50px;
            }

            .left-top p {
                font-weight: bold;
                font-size: 20px;
            }

            .left-middle {
                max-width: 700px;
            }

            .left-middle h1 {
                font-size: 45px;
                margin-bottom: 15px;
            }

            .left-middle p {
                opacity: 0.8;
                line-height: 1.5;
            }

            .left-bottom {
                font-size: 15px;
                opacity: 0.7;
            }

            .right {
                width: 50%;
                background: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .form-box {
                width: 80%;
            }

            .form-box h1 {
                margin-bottom: 10px;
            }

            .form-box p {
                font-size: 14px;
                margin-bottom: 20px;
                color: #666;
            }

            input {
                width: 100%;
                padding: 12px;
                margin: 8px 0;
                border-radius: 8px;
                border: 1px solid #ccc;
            }

            select {
                width: 100%;
                padding: 12px;
                margin: 8px 0;
                border-radius: 8px;
                border: 1px solid #ccc;
                background: white;
                font-family: 'Segoe UI', sans-serif;
                color: #333;
            }

            button {
                width: 100%;
                padding: 12px;
                background: #0f172a;;
                color: white;
                border: none;
                border-radius: 8px;
                margin-top: 10px;
                cursor: pointer;
                font-weight: bold;
            }

            button:hover {
                background: #024b58;
            }

            .password-box {
                position: relative;
            }

            .password-box input {
                padding-right: 45px;
            }

            .password-box span {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                display: flex;
            }

            .eye-icon {
                width: 22px;
                height: 22px;
                stroke: #555;
                stroke-width: 2;
                fill: none;
            }

            .remember {
                display: flex;
                align-items: center;
                margin-top: 5px;
                font-size: 14px;
            }

            .remember input {
                width: auto;
                margin-right: 8px;
                cursor: pointer;
            }

            .toggle {
                text-align: center;
                margin-top: 15px;
                font-size: 14px;
            }

            .toggle span {
                color: #062f36;
                cursor: pointer;
                font-weight: bold;
            }

            @media (max-width: 1024px) {
                .form-box {
                    width: 90%;
                }
                .left-middle h1 {
                    font-size: 32px;
                }
            }

            @media (max-width: 768px) {
                body {
                    flex-direction: column;
                }
                .left {
                    width: 100%;
                    height: auto;
                    padding: 20px;
                }
                .right {
                    width: 100%;
                    height: auto;
                    padding: 20px;
                }
                .left-middle h1 {
                    font-size: 24px;
                }
                .left-top img {
                    width: 40px;
                }
                .form-box {
                    width: 100%;
                }
            }

            .popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.6);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 999;
            }

            .popup-box {
                background: white;
                padding: 30px;
                border-radius: 15px;
                text-align: center;
                width: 300px;
                animation: fadeIn 0.3s ease;
            }

            .popup-box h1 {
                color: red;
                margin-bottom: 10px;
            }

            .popup-box h3 {
                color: #333;
                margin-bottom: 10px;
                opacity: 0.8;
            }

            .popup-box button {
                padding: 10px 20px;
                background: #062f36;
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
            }

            .popup-box button:hover {
                background: #041f24;
            }

            @keyframes fadeIn {
                from {
                    transform: scale(0.8);
                    opacity: 0;
                }
                to {
                    transform: scale(1);
                    opacity: 1;
                }
            }
        </style>
    </head>
    <body>
        <div class="left">
            <div class="left-top">
                <img src="../source/LogoMasjid.png" alt="logo">
                <p>Masjid Ibnu Sabil</p>
            </div>

            <div class="left-middle">
                <h1>Selamat Datang di Sistem Monitoring Akses Ruangan</h1>
                <p>Pemanfaatan RFID untuk identifikasi dan pembatasan akses ruangan berbasis IoT</p>
            </div>

            <div class="left-bottom">
                <p>© 2026 Dafa Abdurrohman. All rights reserved</p>
            </div>
        </div>
        <div class="right">
            <div class="form-box">
                <h1 id="title">Masuk ke Akun</h1>
                <p>Silahkan masukkan akun anda untuk melanjutkan</p>
                <form action="proses.php" method="POST" id="loginForm">
                    <input type="hidden" name="type" value="login">
                    <input type="text" name="username" value="<?php echo $username; ?>" placeholder="Username" required>

                    <div class="password-box">
                        <input type="password" name="password" id="passwordLogin" value="<?php echo $password; ?>" placeholder="Password" required>
                        <span onclick="togglePassword('passwordLogin', this)">
                            <svg class="eye-icon" viewBox="0 0 24 24">
                                <path d="M2 2l20 20M10.58 10.58a2 2 0 002.83 2.83M9.88 4.24A10.94 10.94 0 0112 4c5 0 9.27 3.11 11 8-1.05 2.94-3.17 5.19-5.74 6.32M6.53 6.53C4.06 8.07 2.36 10.28 1 12c.69 1.06 1.62 2.28 2.74 3.47M12 8a4 4 0 014 4"/>
                            </svg>
                        </span>
                    </div>

                    <div class="remember">
                        <label>
                            <input type="checkbox" name="remember" <?php if(isset($_COOKIE['username'])) echo "checked"; ?>> Ingat Saya
                        </label>
                    </div>

                    <button type="submit">Masuk</button>
                </form>
                <form action="proses.php" method="POST" id="registerForm" style="display:none;" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="register">
                    <input type="text" name="username" placeholder="Username" required>

                    <div class="password-box">
                        <input type="password" name="password" id="passwordRegister" placeholder="Password" required>
                        <span onclick="togglePassword('passwordRegister', this)">
                            <svg class="eye-icon" viewBox="0 0 24 24">
                                <path d="M2 2l20 20M10.58 10.58a2 2 0 002.83 2.83M9.88 4.24A10.94 10.94 0 0112 4c5 0 9.27 3.11 11 8-1.05 2.94-3.17 5.19-5.74 6.32M6.53 6.53C4.06 8.07 2.36 10.28 1 12c.69 1.06 1.62 2.28 2.74 3.47M12 8a4 4 0 014 4"/>
                            </svg>
                        </span>
                    </div>
                    <input type="file" name="foto" accept="image/*" required>

                    <select name="jabatan" required>
                        <option value="" disabled selected>Pilih Jabatan</option>
                        <option value="Ketua DKM">Ketua DKM</option>
                        <option value="Sekretaris DKM">Sekretaris DKM</option>
                        <option value="Bendahara DKM">Bendahara DKM</option>
                        <option value="Pengurus DKM">Pengurus DKM</option>
                        <option value="Marbot">Marbot</option>
                    </select>

                    <button type="submit">Daftar</button>
                </form>
                <div class="toggle">
                    <span onclick="toggleForm()">Belum punya akun? Daftar disini</span>
                </div>
            </div>
        </div>

    <script>
        function toggleForm() {
            let login = document.getElementById("loginForm");
            let register = document.getElementById("registerForm");
            let title = document.getElementById("title");
            let toggle = document.querySelector(".toggle span");

            if (login.style.display === "none") {
                login.style.display = "block";
                register.style.display = "none";
                title.innerText = "Masuk ke Akun";
                toggle.innerText = "Belum punya akun? Daftar disini";
            } else {
                login.style.display = "none";
                register.style.display = "block";
                title.innerText = "Daftar Akun";
                toggle.innerText = "Sudah punya akun? Login";
            }
        }

        function togglePassword(id, el) {
            let input = document.getElementById(id);

            if (input.type === "password") {
                input.type = "text";
                el.innerHTML = `
                <svg class="eye-icon" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>`;
            } else {
                input.type = "password";
                el.innerHTML = `
                <svg class="eye-icon" viewBox="0 0 24 24">
                    <path d="M2 2l20 20M10.58 10.58a2 2 0 002.83 2.83M9.88 4.24A10.94 10.94 0 0112 4c5 0 9.27 3.11 11 8-1.05 2.94-3.17 5.19-5.74 6.32M6.53 6.53C4.06 8.07 2.36 10.28 1 12c.69 1.06 1.62 2.28 2.74 3.47M12 8a4 4 0 014 4"/>
                </svg>`;
            }
        }

        function closePopup() {
            window.location.href = "auth.php";
        }

    </script>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'login'): ?>
        <div class="popup-overlay">
            <div class="popup-box">
                <h1>Login Gagal</h1>
                <h3>Username dan Password salah</h3>
                <button onclick="closePopup()">Kembali</button>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // SUCCESS REGISTER
        if (isset($_GET['success']) && $_GET['success'] == 'register') {
            include '../components/popupBerhasilDaftar.php';
        }

        // ERROR REGISTER
        if (isset($_GET['error']) && $_GET['error'] == 'register') {
            include '../components/popupGagalDaftar.php';
        }

        // ERROR UPLOAD
        if (isset($_GET['error']) && $_GET['error'] == 'upload') {
            include '../components/popupUploadGagal.php';
        }
    ?>
    </body>
</html>