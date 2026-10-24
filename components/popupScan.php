<style>
    .popup-overlay-scan {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 999999;
    }

    .popup-box-scan {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        text-align: center;
        width: 300px;
        color: black;
    }

    .popup-box-scan button {
        margin-top: 15px;
        padding: 10px 20px;
        background: red;
        color: white;
        border: none;
        border-radius: 8px;
    }

    button:hover {
        cursor:pointer;
    }
</style>

<div class="popup-overlay-scan" id="scanPopup">
    <div class="popup-box-scan">
        <h2>Scan Kartu</h2>
        <p>Silahkan tempelkan kartu RFID...</p>
        <button onclick="closeScan()">Batal</button>
    </div>
</div>

<script>
    function openScan(){
        document.getElementById("scanPopup").style.display = "flex";
    }

    function closeScan(){
        document.getElementById("scanPopup").style.display = "none";
    }

    // SCAN RFID
    let lastRFID = "";

    setInterval(() => {
        fetch('/api/getrfid.php?nocache=' + new Date().getTime())
        .then(res => res.text())
        .then(data => {

            if(data.includes("<!DOCTYPE")) return;

            if(data && data.trim() !== "" && data !== lastRFID){
                lastRFID = data;

                let input = document.getElementById("rfid");

                if(input){
                    input.value = data;
                    closeScan();
                }
            }
        })
        .catch(err => console.log(err));
    }, 1000);
</script>