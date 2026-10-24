<?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    include __DIR__ . "/../koneksi/koneksi.php";

    // AMBIL JABATAN & NAMA DARI SESI LOGIN (diisi oleh proses login dari tabel admin)
    $namaLogin    = $_SESSION['username'] ?? '-';
    $jabatanLogin = $_SESSION['jabatan'] ?? '-';

    // FORMAT TANGGAL INDONESIA (hari, tanggal bulan tahun)
    function tanggalIndo($timestamp = null) {
        $hari  = ['Minggu','Senin','Selasa','Rabu','Kamis',"Jum'at",'Sabtu'];
        $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $timestamp = $timestamp ?? time();
        return $hari[(int)date('w', $timestamp)] . ', ' . date('d', $timestamp) . ' ' . $bulan[(int)date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }
    $tanggalCetak = tanggalIndo();

    $data = mysqli_query($conn, "
        SELECT l.*, p.nama_pengguna, r.nama_ruangan
        FROM laporan l
        JOIN pengguna p ON l.id_pengguna = p.id
        JOIN ruangan r ON l.id_ruangan = r.id
        ORDER BY l.waktu DESC
    ");
?>

<h1 class="title">Laporan Keseluruhan</h1>
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pengguna</th>
                    <th>Ruangan</th>
                    <th>Waktu</th>
                    <th>Fuzzy</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while($row = mysqli_fetch_assoc($data)) { ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama_pengguna']; ?></td>
                    <td><?= $row['nama_ruangan']; ?></td>
                    <td><?= $row['waktu']; ?></td>
                    <td><?= $row['nilai_fuzzy']; ?></td>
                    <td>
                        <span class="<?= $row['keputusan_akses'] == 1 ? 'success' : 'failed' ?>">
                            <?= $row['keputusan_akses'] == 1 ? 'Diizinkan' : '✖ Ditolak' ?>
                        </span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="ttd-box">
    <div class="ttd-inner">
        <p>Jakarta, <?= $tanggalCetak ?></p>
        <p><?= htmlspecialchars($jabatanLogin) ?></p>
        <div class="ttd-spasi"></div>
        <p><strong><?= htmlspecialchars($namaLogin) ?></strong></p>
    </div>
</div>
<br>
<div class="bottom-bar">
    <button onclick="printLaporan()" class="btn cetak">🖨️ Cetak</button>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

<style>
    body {
        font-family: 'Segoe UI';
        background: #0f172a;
    }

    .title {
        margin-bottom: 20px;
        color: white;
    }

    .card {
        background: #1e293b;
        padding: 20px;
        border-radius: 12px;
    }

    .table-wrapper {
        height: 450px;
        overflow-y: auto;
        overflow-x: auto;
        border-radius: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        color: black;
    }

    thead {
        background: #0f172a;
        color: white;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    th, td {
        padding: 12px;
        text-align: center;
    }

    tbody tr {
        background: white;
    }

    td {
        border-bottom: 1px solid #e5e7eb;
    }

    tbody tr:hover {
        background: #f1f5f9;
    }

    .success {
        color: #166534;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: bold;
    }

    .failed {

        color: #991b1b;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: bold;
    }

    .btn {
        padding: 10px 15px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
    }

    .cetak {
        background: #0ea5e9;
        color: white;
    }

    .bottom-bar {
        margin-top: 15px;
    }

    .ttd-box {
        display: none;
    }

    @media print {
        .ttd-box {
            display: flex;
            justify-content: flex-end;
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 40px;
        }

        .ttd-box .ttd-inner {
            color: #000 !important;
        }
    }

    .ttd-box .ttd-inner {
        text-align: center;
        min-width: 220px;
        color: white;
    }

    .ttd-box .ttd-inner p {
        margin: 2px 0;
    }

    .ttd-spasi {
        height: 70px;
    }
</style>

<script>
    function printLaporan(){
        let frame = document.getElementById('printFrame');

        frame.src = '/action/laporan/laporanKeseluruhan.php';

        frame.onload = function(){
            frame.contentWindow.focus();
            frame.contentWindow.print();
        }
    }
</script>