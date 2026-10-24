<?php 
    $status = $_GET['status'] ?? null;
    $aksi = $_GET['aksi'] ?? null;

    if ($status && $aksi):
?>

<style>
    .popup-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999999;
    }

    .popup-box {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        text-align: center;
        width: 320px;
        color: black;
    }

    .popup-box.success h2 { color: green; }
    .popup-box.error h2 { color: red; }

    .popup-box button {
        margin-top: 15px;
        padding: 10px 20px;
        background: #062f36;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }
</style>

<div class="popup-overlay">
    <div class="popup-box <?= $status ?>">
        <h2><?= ($status == 'success') ? 'Berhasil!' : 'Gagal!' ?></h2>
        <p>
            <?php
            if ($status == 'success') {
                if ($aksi == 'tambah') echo "Data berhasil ditambahkan";
                elseif ($aksi == 'edit') echo "Data berhasil diubah";
                elseif ($aksi == 'hapus') echo "Data berhasil dihapus";
            } else {
                if ($aksi == 'tambah') echo "Gagal menambahkan data";
                elseif ($aksi == 'edit') echo "Gagal mengubah data";
                elseif ($aksi == 'hapus') echo "Gagal menghapus data";
            }
            ?>
        </p>
        <button onclick="goBack()">OK</button>
    </div>
</div>

<script>
    function goBack(){
        window.location.href = "?page=<?= $_GET['page'] ?>";
    }
</script>
<?php endif; ?>