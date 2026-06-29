<?php
session_start();
require_once "../connection/db.php";

// Ambil data dari form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validasi form kosong
if (empty($username) || empty($password)) {
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Username dan password tidak boleh kosong!',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = '../../admin/login.php';
            });
        </script>
    </body>
    </html>";
    exit;
}

try {
    // Ambil data user dari database
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password === $password) {
        // Login sukses
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_id'] = $user['id'];
        header("Location: ../../admin/index.php");
        exit;
    } else {
        // Username/password salah
        echo "
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login gagal!',
                    text: 'Username atau password salah!',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = '../../admin/login.php';
                });
            </script>
        </body>
        </html>";
        exit;
    }
} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit;
}
