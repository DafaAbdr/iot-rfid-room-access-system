<?php 
    include __DIR__ . "/../koneksi/koneksi.php"; 
?>

<h1 class="title">Dashboard</h1>
<div class="cards">
    <div class="card blue">
        <p>Pengguna</p>
        <h2 id="pengguna">0</h2>
    </div>

    <div class="card green">
        <p>Ruangan</p>
        <h2 id="ruangan">0</h2>
    </div>

    <div class="card orange">
        <p>Total Akses</p>
        <h2 id="akses">0</h2>
    </div>

    <div class="card red">
        <p>Ruangan Terfavorit</p>
        <h2 id="bulan">-</h2>
    </div>
</div>
<div class="card log-card">
    <div class="log-header">
        <h2>Aktivitas Akses</h2>

        <select id="filter" onchange="loadLog()" class="filter-select">
            <option value="hari">Hari Ini</option>
            <option value="minggu">Minggu Ini</option>
            <option value="bulan">Bulan Ini</option>
        </select>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pengguna</th>
                    <th>Ruangan</th>
                    <th>Waktu</th>
                    <th>Fuzzy</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="logTable"></tbody>
        </table>
    </div>
</div>

<style>
    html, body {
        height: 100%;
        margin: 0;
        overflow: hidden;
        font-family: Arial, sans-serif;
    }

    .title {
        margin-bottom: 20px;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .card {
        padding: 18px;
        border-radius: 12px;
        color: white;
        text-align: center;
    }

    .card h2 {
        margin-top: 5px;
        font-size: 22px;
    }

    .log-card {
        background: #1e293b;
        height: calc(85vh - 180px);
        display: flex;
        flex-direction: column;
        border-radius: 12px;
        padding: 15px;
    }

    .log-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .log-header h2 {
        color: white;
    }

    .filter-select {
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        background: #0f172a;
        color: white;
        cursor: pointer;
    }

    .table-wrapper {
        flex: 1;
        overflow-y: auto;
        overflow-x: auto;
        background: #0f172a;
        border-radius: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        color: black;
    }

    thead {
        background: #020617;
        position: sticky;
        top: 0;
        color: white;
    }

    th, td {
        padding: 12px;
        text-align: center;
        font-size: 14px;
    }

    th {
        border-bottom: 2px solid #334155;
    }

    td {
        border-bottom: 1px solid #1e293b;
    }

    tbody tr:nth-child(even) {
        background: #f8fafc;
    }

    tbody tr:hover {
        background: #e2e8f0;
    }

    .status-yes {
        color: #22c55e;
        font-weight: bold;
    }

    .status-no {
        color: #ef4444;
        font-weight: bold;
    }

    .table-wrapper::-webkit-scrollbar {
        width: 8px;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 10px;
    }

    .blue { background: #3498db; }
    .green { background: #2ecc71; }
    .orange { background: #f39c12; }
    .red { background: #e74c3c; }

    @media(max-width:768px){
        body { overflow: auto; }

        .cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .log-card {
            height: auto;
        }
    }
</style>

<script>
    function loadData(){
        fetch('../api/getDashboard.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById('pengguna').innerText = data.pengguna;
            document.getElementById('ruangan').innerText = data.ruangan;
            document.getElementById('akses').innerText = data.akses;
            document.getElementById('bulan').innerText = data.bulan_ini;
        });
    }

    function loadLog(){
        let filter = document.getElementById('filter').value;

        fetch('../api/getLogDashboard.php?filter=' + filter)
        .then(res => res.json())
        .then(data => {

            let html = "";

            data.forEach(row => {
                html += `
                <tr>
                    <td>${row.id}</td>
                    <td>${row.nama_pengguna}</td>
                    <td>${row.nama_ruangan}</td>
                    <td>${row.waktu}</td>
                    <td>${row.nilai_fuzzy}</td>
                    <td class="${row.keputusan_akses == 1 ? 'status-yes' : 'status-no'}">
                        ${row.keputusan_akses == 1 ? '✔ Diizinkan' : '✖ Ditolak'}
                    </td>
                </tr>`;
            });

            document.getElementById('logTable').innerHTML = html;
        });
    }

    setInterval(loadLog, 2000);
    loadLog();
    loadData();
</script>