<?php
    include __DIR__ . '/../../koneksi/koneksi.php';

    $id = $_GET['id'];
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengguna WHERE id='$id'"));
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Edit Pengguna</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', sans-serif;
                background: linear-gradient(135deg, #0f172a, #020617);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .form-box {
                background: #1e293b;
                padding: 30px;
                border-radius: 16px;
                width: 380px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.4);
                animation: fadeIn 0.4s ease;
            }

            .form-box h2 {
                text-align: center;
                margin-bottom: 20px;
                color: #38bdf8;
            }

            input, select {
                width: 100%;
                padding: 12px;
                margin: 10px 0;
                border-radius: 10px;
                border: 1px solid #334155;
                background: #0f172a;
                color: white;
            }

            input:focus, select:focus {
                outline: none;
                border-color: #38bdf8;
            }

            .rfid-group {
                display: flex;
                align-items: center;
            }

            .rfid-group input {
                flex: 1;
                border-radius: 10px 0 0 10px;
                height: 44px;
            }

            .scan-btn {
                height: 44px;
                padding: 0 15px;
                background: #22c55e;
                color: white;
                border: none;
                border-radius: 0 10px 10px 0;
                cursor: pointer;
            }

            .upload {
                text-align: center;
                margin-top: 10px;
            }

            .upload img {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                object-fit: cover;
                margin-bottom: 10px;
                cursor: pointer;
            }

            .btn {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 10px;
                margin-top: 10px;
                cursor: pointer;
                font-weight: bold;
            }

            .simpan {
                background: #22c55e;
                color: white;
            }

            .batal {
                background: #ef4444;
                color: white;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body>
        <div class="form-box">
            <h2>Edit Pengguna</h2>
            <form action="updateUser.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <div class="upload">
                    <?php
                    $foto = (!empty($data['photo']) && file_exists(__DIR__ . '/../../source/profile/' . $data['photo']))
                        ? '../../source/profile/' . $data['photo']
                        : '../../source/profile.jpg';
                    ?>
                    <img src="<?= $foto ?>" id="preview" onclick="document.getElementById('photo').click()">
                    <input type="file" name="photo" id="photo" hidden onchange="previewImage(event)">
                    <p style="color:white;">Klik untuk ganti foto</p>
                </div>
                <input type="text" name="nama_pengguna"
                    value="<?= $data['nama_pengguna'] ?>"
                    placeholder="Nama Pengguna" required>
                <div class="rfid-group">
                    <input type="text" id="rfid" name="no_rfid"
                        value="<?= $data['no_rfid'] ?>"
                        placeholder="No RFID" required>
                    <button type="button" class="scan-btn" onclick="openScan(event)">Scan</button>
                </div>
                <input type="number" name="role_level"
                    value="<?= $data['role_level'] ?>"
                    placeholder="Role Level" required>
                <select name="status" required>
                    <option value="DKM" <?= $data['status']=='DKM'?'selected':'' ?>>DKM</option>
                    <option value="Pengurus" <?= $data['status']=='Pengurus'?'selected':'' ?>>Pengurus</option>
                    <option value="Marbot" <?= $data['status']=='Marbot'?'selected':'' ?>>Marbot</option>
                    <option value="Ustadz" <?= $data['status']=='Ustadz'?'selected':'' ?>>Ustadz</option>
                    <option value="Keamanan" <?= $data['status']=='Keamanan'?'selected':'' ?>>Keamanan</option>
                    <option value="Jamaah" <?= $data['status']=='Jamaah'?'selected':'' ?>>Jamaah</option>
                    <option value="Tamu" <?= $data['status']=='Tamu'?'selected':'' ?>>Tamu</option>
                </select>
                <button type="submit" class="btn simpan">Simpan</button>
            </form>
            <a href="/loginPage/dashboard.php?page=user">
                <button class="btn batal">Batal</button>
            </a>
        </div>
    </body>
    <script>
        function previewImage(event){
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById("preview").src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
        function openScan(e){
            e.preventDefault(); //
            document.getElementById("scanPopup").style.display = "flex";
        }
        function closeScan(){
            document.getElementById("scanPopup").style.display = "none";
        }
    </script>
    <?php include __DIR__ . '/../../components/popupScan.php'; ?>
</html>