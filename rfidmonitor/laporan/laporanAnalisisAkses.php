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

    // FILTER
    $id_laporan = $_GET['id_laporan'] ?? '';
    $ruangan   = $_GET['ruangan'] ?? '';
    $pengguna  = $_GET['pengguna'] ?? '';
    $status    = $_GET['status'] ?? '';
    $tanggal   = $_GET['tanggal'] ?? '';

    $query = "
    SELECT l.*, p.nama_pengguna, r.nama_ruangan
    FROM laporan l
    JOIN pengguna p ON l.id_pengguna = p.id
    JOIN ruangan r ON l.id_ruangan = r.id
    WHERE 1=1
    ";

    if($id_laporan != ''){
        $query .= " AND l.id LIKE '%$id_laporan%'";
    }
    if($ruangan != ''){
        $query .= " AND l.id_ruangan = '$ruangan'";
    }
    if($pengguna != ''){
        $query .= " AND l.id_pengguna = '$pengguna'";
    }
    if($status != ''){
        if($status == 'Diizinkan'){
            $query .= " AND l.keputusan_akses = 1";
        } else if($status == 'Ditolak'){
            $query .= " AND l.keputusan_akses = 0";
        }
    }
    if($tanggal != ''){
        $query .= " AND DATE(l.waktu) = '$tanggal'";
    }

    $query .= " ORDER BY l.waktu DESC";

    $data = mysqli_query($conn, $query);

    // DROPDOWN
    $ruanganList = mysqli_query($conn, "SELECT * FROM ruangan");
    $penggunaList = mysqli_query($conn, "SELECT * FROM pengguna");
?>

<style>
    .filter-card {
        background: #1e293b;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 15px;
    }

    .form-box {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .form-box select,
    .form-box input,
    .form-box button {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 13px;
    }

    .form-box button {
        background: #3498db;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .table-wrapper {
        background: #1e293b;
        padding: 15px;
        border-radius: 12px;
    }

    .table-scroll {
        height: calc(100vh - 280px);
        overflow-y: auto;
        border-radius: 10px;
    }

    .table-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .table-scroll::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    thead th {
        position: sticky;
        top: 0;
        background: #0f172a;
        color: white;
        z-index: 5;
    }

    thead th:first-child {
        border-top-left-radius: 10px;
    }
    thead th:last-child {
        border-top-right-radius: 10px;
    }

    th, td {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    td {
        background: white;
        color: black;
    }

    tbody tr:nth-child(even) td {
        background: #f8fafc;
    }

    tbody tr:last-child td:first-child {
        border-bottom-left-radius: 10px;
    }
    tbody tr:last-child td:last-child {
        border-bottom-right-radius: 10px;
    }

    .status-yes {
        color: #16a34a;
        font-weight: bold;
    }
    .status-no {
        color: #dc2626;
        font-weight: bold;
    }

    .btn-analisis {
        background: #f39c12;
        color: white;
        padding: 6px 10px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .table-scroll {
            height: auto;
        }
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

<h1>Laporan Analisis Akses</h1>
<br>

<div class="filter-card">
    <form method="GET" class="form-box">
        <input type="hidden" name="page" value="laporan_fuzzy">

        <input type="text" name="id_laporan" placeholder="ID Laporan" value="<?= $id_laporan ?>">

        <select name="ruangan">
            <option value="">Semua Ruangan</option>
            <?php while($r = mysqli_fetch_assoc($ruanganList)){ ?>
                <option value="<?= $r['id'] ?>" <?= ($ruangan == $r['id'])?'selected':'' ?>>
                    <?= $r['nama_ruangan'] ?>
                </option>
            <?php } ?>
        </select>

        <select name="pengguna">
            <option value="">Semua Pengguna</option>
            <?php while($p = mysqli_fetch_assoc($penggunaList)){ ?>
                <option value="<?= $p['id'] ?>" <?= ($pengguna == $p['id'])?'selected':'' ?>>
                    <?= $p['nama_pengguna'] ?>
                </option>
            <?php } ?>
        </select>

        <select name="status">
            <option value="">Semua Status</option>
            <option value="Diizinkan" <?= ($status=='Diizinkan')?'selected':'' ?>>Diizinkan</option>
            <option value="Ditolak" <?= ($status=='Ditolak')?'selected':'' ?>>Ditolak</option>
        </select>

        <input type="date" name="tanggal" value="<?= $tanggal ?>">

        <button type="submit">🔍 Filter</button>
    </form>
</div>

<div class="table-wrapper">
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Laporan</th>
                    <th>Pengguna</th>
                    <th>Ruangan</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while($row = mysqli_fetch_assoc($data)){ ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nama_pengguna'] ?></td>
                    <td><?= $row['nama_ruangan'] ?></td>
                    <td><?= $row['waktu'] ?></td>
                    <td>
                        <?php if($row['keputusan_akses'] == 1){ ?>
                            <span class="status-yes">✔ Diizinkan</span>
                        <?php } else { ?>
                            <span class="status-no">✖ Ditolak</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="/action/laporan/laporanAnalisisAkses.php?id=<?= $row['id'] ?>" class="btn-analisis">
                            🔍 Analisis
                        </a>
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