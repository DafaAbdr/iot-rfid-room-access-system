<?php
    include __DIR__ . '/../../koneksi/koneksi.php';

    $id = $_GET['id'];
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM ruangan WHERE id='$id'"));
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Edit Ruangan</title>
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

            /* CARD */
            .form-box {
                background: #1e293b;
                padding: 30px;
                border-radius: 16px;
                width: 360px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.4);
                animation: fadeIn 0.4s ease;
            }

            /* TITLE */
            .form-box h2 {
                text-align: center;
                margin-bottom: 25px;
                color: #38bdf8;
            }

            /* INPUT */
            input {
                width: 100%;
                padding: 12px;
                margin: 10px 0;
                border-radius: 10px;
                border: 1px solid #334155;
                background: #0f172a;
                color: white;
                transition: 0.3s;
            }

            input::placeholder {
                color: #94a3b8;
            }

            input:focus {
                outline: none;
                border-color: #38bdf8;
                box-shadow: 0 0 8px rgba(56,189,248,0.5);
            }

            /* BUTTON */
            .btn {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 10px;
                margin-top: 12px;
                cursor: pointer;
                font-weight: bold;
                transition: 0.3s;
            }

            /* SIMPAN */
            .simpan {
                background: linear-gradient(90deg, #22c55e, #16a34a);
                color: white;
            }

            .simpan:hover {
                transform: scale(1.03);
                box-shadow: 0 5px 15px rgba(34,197,94,0.4);
            }

            /* BATAL */
            .batal {
                background: linear-gradient(90deg, #ef4444, #b91c1c);
                color: white;
            }

            .batal:hover {
                transform: scale(1.03);
                box-shadow: 0 5px 15px rgba(239,68,68,0.4);
            }

            /* ANIMASI */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
    <body>
        <div class="form-box">
            <h2>Edit Ruangan</h2>
            <form action="updateRuangan.php" method="POST">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <input type="text" name="nama_ruangan" 
                    value="<?= $data['nama_ruangan'] ?>" 
                    placeholder="Nama Ruangan" required>
                <input type="text" name="detail_ruangan" 
                    value="<?= $data['detail_ruangan'] ?>" 
                    placeholder="Detail Ruangan" required>
                <input type="number" step="0.1" name="sensitivitas" 
                    value="<?= $data['sensitivitas'] ?>" 
                    placeholder="Sensitivitas" required>
                <button type="submit" class="btn simpan">Simpan</button>
            </form>
            <a href="/loginPage/dashboard.php?page=ruangan">
                <button class="btn batal">Batal</button>
            </a>
        </div>
    </body>
</html>