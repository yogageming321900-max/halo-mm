<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "ecoscan_db"; // Sudah sinkron menggunakan ecoscan_db

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal."]);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// LOGIKA REGISTRASI
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['username'], $_POST['password'])) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap."]);
        exit;
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']); 

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Username/Password tidak boleh kosong."]);
        exit;
    }

    // Cek duplikasi username
    $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $cek->store_result();
    
    if ($cek->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username sudah terdaftar!"]);
        $cek->close();
        exit;
    }
    $cek->close();

    // Simpan permanen ke database
    $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (?, ?, ?)");
    $nama_lengkap = "Petugas " . $username;
    $stmt->bind_param("sss", $username, $password, $nama_lengkap);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Pendaftaran Berhasil! Akun disimpan permanen."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan user ke database."]);
    }
    $stmt->close();
}

// LOGIKA LOGIN
elseif ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Akses diberikan!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Username atau password salah!"]);
    }
    $stmt->close();
}

$conn->close();
?>