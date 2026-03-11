<?php
// Konfigurasi Database - Sesuaikan dengan database server SAMP kamu
$db_host = "localhost";
$db_user = "root";
$db_pass = ""; 
$db_name = "server_823_web"; // Nama database kamu

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ucp_name = mysqli_real_escape_string($conn, $_POST['ucp_name']);
    $password = $_POST['password']; // Sebaiknya gunakan password_hash() untuk keamanan
    $user_ip  = $_SERVER['REMOTE_ADDR']; // Deteksi IP Pendaftar

    // 1. Cek apakah IP ini sudah pernah mendaftar
    $sql_cek_ip = "SELECT * FROM ucp_accounts WHERE ip_address = '$user_ip'";
    $result_ip  = mysqli_query($conn, $sql_cek_ip);

    if (mysqli_num_rows($result_ip) > 0) {
        // Jika IP sudah ada di database
        echo "<script>alert('Gagal! IP Anda ($user_ip) sudah terdaftar. 1 IP hanya boleh 1 UCP.'); window.history.back();</script>";
    } else {
        // 2. Cek apakah Nama UCP sudah dipakai orang lain
        $sql_cek_nama = "SELECT * FROM ucp_accounts WHERE ucp_name = '$ucp_name'";
        $result_nama  = mysqli_query($conn, $sql_cek_nama);

        if (mysqli_num_rows($result_nama) > 0) {
            echo "<script>alert('Gagal! Nama UCP sudah digunakan orang lain.'); window.history.back();</script>";
        } else {
            // 3. Masukkan data ke Database
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO ucp_accounts (ucp_name, password, ip_address) VALUES ('$ucp_name', '$hashed_pass', '$user_ip')";
            
            if (mysqli_query($conn, $sql_insert)) {
                echo "<script>alert('Pendaftaran Berhasil! Silakan login di SAMP dengan nama: $ucp_name'); window.location='index.html';</script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
}
mysqli_close($conn);
?>