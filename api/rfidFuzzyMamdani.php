<?php
error_reporting(0);
header("Content-Type: text/plain");

include "../koneksi/koneksi.php";

// ================= INPUT =================
$rfid = strtoupper(trim($_POST['rfid'] ?? ''));
$ruangan = trim($_POST['ruangan'] ?? '');

if($rfid == "" || $ruangan == ""){
    echo "ERR";
    exit;
}

// ================= USER =================
$stmt = $conn->prepare("SELECT * FROM pengguna WHERE UPPER(no_rfid)=?");
$stmt->bind_param("s", $rfid);
$stmt->execute();
$user = $stmt->get_result();

if(!$user || $user->num_rows == 0){
    echo "N|Tidak dikenal|0";
    exit;
}

$user = $user->fetch_assoc();

$id_pengguna = $user['id'];
$nama        = $user['nama_pengguna'];
$role        = (double)$user['role_level'];

// ================= RUANGAN =================
$stmt = $conn->prepare("SELECT * FROM ruangan WHERE id=?");
$stmt->bind_param("s", $ruangan);
$stmt->execute();
$room = $stmt->get_result();

if(!$room || $room->num_rows == 0){
    echo "ERR";
    exit;
}

$room = $room->fetch_assoc();
$sens = (double)$room['sensitivitas'];

// ================= FUNCTION ID =================
function generateID($conn, $table, $prefix, $field){
    $query = mysqli_query($conn, "SELECT $field FROM $table ORDER BY $field DESC LIMIT 1");

    if(!$query || mysqli_num_rows($query) == 0){
        return $prefix . "001";
    }

    $data = mysqli_fetch_assoc($query);
    $lastID = $data[$field];

    $number = (int) substr($lastID, strlen($prefix));
    $number++;

    return $prefix . str_pad($number, 3, "0", STR_PAD_LEFT);
}

// ================= CEK fuzzy_rule (CACHE) =================
// Sesuai diagram: Cek fuzzy_rule -> Ada => ambil nilai fuzzy & keputusan langsung
//                                 -> Tidak Ada => Hitung Fuzzy Mamdani lalu simpan
$stmtCek = $conn->prepare("
    SELECT akses, level FROM fuzzy_rule
    WHERE id_pengguna=? AND id_ruangan=? AND role_level=? AND sensitivitas=?
");
$stmtCek->bind_param("ssdd", $id_pengguna, $ruangan, $role, $sens);
$stmtCek->execute();
$cache = $stmtCek->get_result();

if($cache && $cache->num_rows > 0){

    // ============ Ada di cache: pakai hasil yang sudah ada ============
    $row       = $cache->fetch_assoc();
    $keputusan = (int)$row['akses'];
    $nilai     = (double)$row['level'];

} else {

    // ============ Tidak ada di cache: hitung Fuzzy Mamdani ============

    function rendah($x){
        if($x <= 0) return 1;
        if($x >= 5) return 0;
        return (5 - $x) / 5;
    }

    function tinggi($x){
        if($x <= 4) return 0;
        if($x >= 10) return 1;
        return ($x - 4) / 6;
    }

    // FUZZIFIKASI
    $role_rendah = rendah($role);
    $role_tinggi = tinggi($role);
    $sens_rendah = rendah($sens);
    $sens_tinggi = tinggi($sens);

    // ================= RULE BASE (baru) =================
    // R1 : Role Rendah AND Sensitivitas Rendah  -> Diizinkan
    // R2 : Role Rendah AND Sensitivitas Tinggi  -> Ditolak
    // R3 : Role Tinggi AND Sensitivitas Rendah  -> Diizinkan
    // R4 : Role Tinggi AND Sensitivitas Tinggi  -> Diizinkan
    $r1 = min($role_rendah, $sens_rendah); // Diizinkan
    $r2 = min($role_rendah, $sens_tinggi); // Ditolak
    $r3 = min($role_tinggi, $sens_rendah); // Diizinkan
    $r4 = min($role_tinggi, $sens_tinggi); // Diizinkan

    // ================= AGREGASI =================
    $izin  = max($r1, $r3, $r4);
    $tolak = $r2;

    // ================= DEFUZZIFIKASI =================

    // ===== IZIN (5–10)
    if($izin > 0){

        $z1 = 5 + (5 * $izin);

        $L1 = (($z1 - 5) * $izin) / 2;
        $L2 = ((10 - $z1) * $izin);

        $C1 = (5 + $z1 + $z1) / 3;
        $C2 = ($z1 + 10) / 2;

        $nilai_izin = (($L1 * $C1) + ($L2 * $C2)) / ($L1 + $L2);

    } else {
        $nilai_izin = 0;
    }

    // ===== TOLAK (0–5)
    if($tolak > 0){

        $z2 = 5 - (5 * $tolak);

        $L3 = ($z2 * $tolak);
        $L4 = ((5 - $z2) * $tolak) / 2;

        $C3 = ($z2 + 0) / 2;
        $C4 = ($z2 + 5 + 5) / 3;

        $nilai_tolak = (($L3 * $C3) + ($L4 * $C4)) / ($L3 + $L4);

    } else {
        $nilai_tolak = 0;
    }

    // ================= HASIL =================
    $nilai     = ($izin >= $tolak) ? $nilai_izin : $nilai_tolak;
    $nilai     = round($nilai, 2);
    $keputusan = ($nilai >= 5) ? 1 : 0;

    // ================= SIMPAN fuzzy_rule (CACHE BARU) =================
    $id_rule = generateID($conn, "fuzzy_rule", "FR", "id");

    $stmtIns = $conn->prepare("
        INSERT INTO fuzzy_rule
        (id, id_pengguna, id_ruangan, role_level, sensitivitas, akses, level)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtIns->bind_param("sssddid", $id_rule, $id_pengguna, $ruangan, $role, $sens, $keputusan, $nilai);
    $stmtIns->execute();
}

// ================= SIMPAN LAPORAN (SELALU) =================
$id_laporan = generateID($conn, "laporan", "L", "id");

$stmtLog = $conn->prepare("
    INSERT INTO laporan
    (id, id_pengguna, id_ruangan, waktu, nilai_fuzzy, keputusan_akses)
    VALUES (?, ?, ?, NOW(), ?, ?)
");
$stmtLog->bind_param("sssdi", $id_laporan, $id_pengguna, $ruangan, $nilai, $keputusan);
$stmtLog->execute();

// ================= OUTPUT =================
$status = ($keputusan == 1) ? "Y" : "N";

echo $status . "|" . $nama . "|" . round($nilai, 2);
?>
