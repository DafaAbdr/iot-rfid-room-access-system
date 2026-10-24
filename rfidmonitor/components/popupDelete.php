<style>
    .popup-overlay-delete {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        display: none;
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

    .popup-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 15px;
    }

    .btn-cancel {
        background: #ef4444;
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        border: none;
    }

    .btn-cancel {
        cursor: pointer;
    }

    .btn-delete {
        background: #062f36;
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        text-decoration: none;
    }
</style>

<div class="popup-overlay-delete" id="deletePopup">
    <div class="popup-box">
        <h2>Yakin?</h2>
        <p>Data akan dihapus permanen</p>
        <div class="popup-actions">
            <button onclick="closeDeletePopup()" class="btn-cancel">Batal</button>
            <a id="deleteLink" class="btn-delete">Hapus</a>
        </div>
    </div>
</div>

<script>
function openDeletePopup(id, type = 'user') {
    document.getElementById("deletePopup").style.display = "flex";

    let url = "";

    if(type === 'user'){
        url = "/action/user/hapusUser.php?id=" + id;
    } else {
        url = "/action/ruangan/hapusRuangan.php?id=" + id;
    }

    document.getElementById("deleteLink").href = url;
}

function closeDeletePopup() {
    document.getElementById("deletePopup").style.display = "none";
}
</script>