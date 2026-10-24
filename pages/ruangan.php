<?php
    include __DIR__ . '/../koneksi/koneksi.php';
    $data = mysqli_query($conn, "SELECT * FROM ruangan");
?>

<h1 class="title">Kelola Ruangan</h1>
<form action="/action/ruangan/tambahRuangan.php" method="POST" class="form-box">
    <input type="text" name="nama_ruangan" placeholder="Nama Ruangan" required>
    <input type="text" name="detail_ruangan" placeholder="Detail Ruangan" required>
    <input type="number" step="0.01" name="sensitivitas" placeholder="Sensitivitas" required>

    <button type="submit" class="btn tambah">Tambah</button>
</form>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Ruangan</th>
                <th>Detail</th>
                <th>Sensitivitas</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = mysqli_fetch_assoc($data)) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nama_ruangan'] ?></td>
                <td><?= $row['detail_ruangan'] ?></td>
                <td><?= $row['sensitivitas'] ?></td>
                <td>
                    <a href="/action/ruangan/editRuangan.php?id=<?= $row['id'] ?>" class="btn edit">Edit</a>
                    <button class="btn hapus" onclick="openDeletePopup('<?= $row['id'] ?>', 'ruangan')">
                        Hapus
                    </button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<div class="bottom-bar">
    <a href="/action/ruangan/cetakRuangan.php">
        <button class="btn cetak">🖨️ Cetak</button>
    </a>
</div>

<style>
    body {
        overflow: hidden;
    }

    .title { 
        margin-bottom: 25px; 
    }

    .form-box {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .form-box input {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .table-wrapper { overflow-x: auto; }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;

    }

    .table-wrapper {
        height: 443px;
        overflow-y: scroll;
        overflow-x: auto;
        border-radius: 10px;
    }

    th, td {
        padding: 12px;
        text-align: center;
    }

    td {
        color: black;
    }

    thead {
        background: #0f172a;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    table th:nth-child(1),
    table td:nth-child(1) {
        width: 60px; 
    }

    table th:nth-child(2),
    table td:nth-child(2) {
        width: 200px; 
    }

    table th:nth-child(3),
    table td:nth-child(3) {
        width: 250px; 
    }

    table th:nth-child(4),
    table td:nth-child(4) {
        width: 120px;
    }

    table th:nth-child(5),
    table td:nth-child(5) {
        width: 180px;
    }

    .btn {
        min-width: 70px;
        text-align: center;
        padding: 10px 10px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
    }

    .edit { 
        background: orange; 
        color: white; 
        padding: 8px 15px; 
        text-decoration: none;
    }

    .hapus { 
        background: red; 
        color: white; 
        padding: 10px 15px;
    }

    .cetak { 
        background: #0ea5e9; 
        color: white; 
        margin-top: 15px; 
        padding: 10px 15px;
    }

    .tambah {
        background: #062f36; 
        color: white; 
        font-weight: bold; 
        padding: 10px 5px;
        }
</style>