<?php
    include __DIR__ . '/../koneksi/koneksi.php';

    $data = mysqli_query($conn, "SELECT * FROM pengguna");

    if (isset($_GET['search']) && $_GET['search'] != '') {
        $keyword = mysqli_real_escape_string($conn, $_GET['search']);

        $data = mysqli_query($conn, "SELECT * FROM pengguna 
            WHERE nama_pengguna LIKE '%$keyword%' 
            OR no_rfid LIKE '%$keyword%'");
    } else {
        $data = mysqli_query($conn, "SELECT * FROM pengguna");
    }
?>

<h1 class="title">Kelola Pengguna</h1>

<form action="/action/user/tambahUser.php" method="POST" enctype="multipart/form-data" class="form-box">
    <input type="text" name="nama_pengguna" placeholder="Nama Pengguna" required>
    <div class="rfid-group">
        <input type="text" id="rfid" name="no_rfid" placeholder="No RFID" required>
        <button type="button" class="scan-btn" onclick="openScan()">Scan</button>
    </div>
    <input type="number" step="0.1" name="role_level" placeholder="Role Level" required>
    <select name="status" required>
        <option value="">Status</option>
        <option value="DKM">DKM</option>
        <option value="Pengurus">Pengurus</option>
        <option value="Marbot">Marbot</option>
        <option value="Ustadz">Ustadz</option>
        <option value="Keamanan">Keamanan</option>
        <option value="Jamaah">Jamaah</option>
        <option value="Tamu">Tamu</option>
    </select>
    <button type="submit" class="btn tambah">Tambah</button>
    <div class="upload-wrapper">
        <input type="file" name="photo" id="photoInput" accept="image/*" onchange="previewImage(event)" hidden>

        <div class="upload-preview" onclick="document.getElementById('photoInput').click()">
            <img id="preview" src="../source/profile.jpg">
            <p>Upload Foto</p>
        </div>
    </div>

</form>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>No RFID</th>
                <th>Role</th>
                <th>Status</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = mysqli_fetch_assoc($data)) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nama_pengguna'] ?></td>
                <td><?= $row['no_rfid'] ?></td>
                <td><?= $row['role_level'] ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                    <?php
                    $folder = __DIR__ . '/../source/profile/';
                    $default = '../source/profile.jpg';

                    if (!empty($row['photo']) && file_exists($folder . $row['photo'])) {
                        $foto = '../source/profile/' . $row['photo'];
                    } else {
                        $foto = $default;
                    }
                    ?>
                    <img src="<?= $foto ?>" 
                        style="width:40px; height:40px; border-radius:50%;">
                </td>
                <td>
        <a href="/action/user/editUser.php?id=<?= $row['id'] ?>" class="btn edit">Edit</a>

        <button class="btn hapus" onclick="openDeletePopup('<?= $row['id'] ?>','user')">
            Hapus
        </button>

        <button class="btn cetak" onclick="printUser('<?= $row['id'] ?>')">
            Cetak
        </button>
    </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<div class="bottom-bar">
    <a href="/action/user/cetakAllUser.php">
        <button class="btn cetak">🖨️ Cetak</button>
    </a>
    <form method="GET" class="search-box">
        <input type="hidden" name="page" value="user">

        <div class="search-group">
            <input type="text" name="search" placeholder="Cari pengguna..." 
       value="<?= $_GET['search'] ?? '' ?>">
            <button type="submit">Cari</button>
        </div>
    </form>

</div>

<style>
    body {
        overflow: hidden;
    }
    .title { margin-bottom: 25px; }

    .form-box {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .form-box input,
    .form-box select {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        height: 44px;
    }

    .rfid-group {
        display: flex;
        flex: 1;
    }

    .rfid-group input {
        flex: 1;
        border-radius: 8px 0 0 8px;
    }

    .scan-btn {
        padding: 12px 16px;
        background: #22c55e;
        color: white;
        border-radius: 0 8px 8px 0;
        border: none;
        cursor: pointer;
    }

    .tambah {
        background: #062f36;
        color: white;
        height: 44px;
        font-weight: bold;
        padding: 0 20px;
    }

    .tambah:hover {
        cursor: pointer;
    }

    .upload-wrapper {
        width: 100%;
        margin-top: 10px;
    }

    .upload-preview {
        width: 130px;
        height: 130px;
        border: 2px dashed #cbd5f5;
        border-radius: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        cursor: pointer;
    }

    .upload-preview img {
        width: 60px;
        margin-bottom: 5px;
    }

    .table-wrapper {
        height: 300px;
        overflow-y: auto;
        border-radius: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    th, td {
        padding: 12px;
        text-align: center;
    }

    thead {
        background: #020617;
        color: white;
    }

    thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #020617;
    }

    tbody td {
        color: black;
    }

    table td:nth-child(7) {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    table th:nth-child(1),
    table td:nth-child(1) {
        width: 60px;
    }

    table th:nth-child(2),
    table td:nth-child(2) {
        width: 150px;
        text-align: left;
    }

    table th:nth-child(3),
    table td:nth-child(3) {
        width: 180px;
    }

    table th:nth-child(4),
    table td:nth-child(4) {
        width: 100px;
    }

    table th:nth-child(5),
    table td:nth-child(5) {
        width: 80px;
    }

    table th:nth-child(6),
    table td:nth-child(6) {
        width: 80px;
    }

    table th:nth-child(7),
    table td:nth-child(7) {
        width: 190px;
    }

    .btn {
        min-width: 70px;
        text-align: center;
        padding: 15px 30px;
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
        padding: 10px 15px;
    }
    
    .cari { 
        background: #334155; 
        color: white; 
        padding: 10px 15px;
    }

    .bottom-bar {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }

    .search-group {
        display: flex;
        align-items: center;
    }

    .search-group input {
        padding: 12px;
        border-radius: 8px 0 0 8px;
        border: 1px solid #ccc;
        height: 44px;
        outline: none;
    }

    .search-group button {
        height: 44px;
        padding: 0 18px;
        background: #062f36;
        color: white;
        border: none;
        border-radius: 0 8px 8px 0;
        cursor: pointer;
    }

    .search-group button:hover {
        background: #0a4a54;
    }
</style>

<script>
    // PREVIEW FOTO
    function previewImage(event){
        const reader = new FileReader();
        reader.onload = function(){
            document.getElementById("preview").src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // SCAN RFID
    function openScan(){
        alert("Tempel kartu sekarang...");
    }

    function closeScan(){
        document.getElementById("scanPopup").style.display = "none";
    }

    function printUser(id) {
        let iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';

        iframe.src = '/action/user/cetakUser.php?id=' + id;

        document.body.appendChild(iframe);

        iframe.onload = function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    }

    setInterval(() => {
        fetch('../api/getRFID.php')
        .then(res => res.text())
        .then(data => {
            if(data !== ""){
                document.getElementById('rfid').value = data;
            }
        });
    }, 1000);
</script>