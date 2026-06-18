<?php
header('Content-Type: application/json');

// ========================================================
// 1. KONEKSI DATABASE (NAMA DATABASE SUDAH DISAMAKAN: ecoscan_db)
// ========================================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ecoscan_db"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal dilakukan."]);
    exit;
}

// ========================================================
// 2. CEK & BUAT TABEL OTOMATIS (Jika tabel belum ada)
// ========================================================
$query_tabel = "CREATE TABLE IF NOT EXISTS riwayat_scan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    benda_nama VARCHAR(255) NOT NULL,
    bahan VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    akurasi VARCHAR(10) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($query_tabel);

// ========================================================
// 3. LOGIKA UTAMA: OTOMATIS HAPUS DATA YANG SUDAH LEBIH DARI 1 BULAN
// ========================================================
$sql_hapus = "DELETE FROM riwayat_scan WHERE timestamp < NOW() - INTERVAL 1 MONTH";
$conn->query($sql_hapus);

// ========================================================
// 4. PROSES SIMPAN DATA BARU (POST)
// ========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['benda_nama'], $_POST['bahan'], $_POST['kategori'], $_POST['akurasi'])) {
        echo json_encode(["status" => "error", "message" => "Data post tidak lengkap."]);
        exit;
    }

    $benda_nama = $_POST['benda_nama'];
    $bahan = $_POST['bahan'];
    $kategori = $_POST['kategori'];
    $akurasi = $_POST['akurasi'];

    $stmt = $conn->prepare("INSERT INTO riwayat_scan (benda_nama, bahan, kategori, akurasi) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $benda_nama, $bahan, $kategori, $akurasi);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Data berhasil disimpan permanen."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data ke database."]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// ========================================================
// 5. PROSES AMBIL DATA UNTUK DITAMPILKAN DI WEB (GET)
// ========================================================
$sql_ambil = "SELECT * FROM riwayat_scan ORDER BY timestamp ASC";
$result = $conn->query($sql_ambil);

$data_riwayat = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data_riwayat[] = $row;
    }
}

echo json_encode(["status" => "success", "data" => $data_riwayat]);
$conn->close();
?>