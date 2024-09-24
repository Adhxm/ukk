<?php
if (!isset($_SESSION['userID'])) {
    // Simpan pesan error di session
    $_SESSION['flash_message'] = 'Not login yet.';
    $_SESSION['flash_type'] = 'error'; // Tipe pesan (error, success, dll.)
    
    // Redirect ke halaman login
    header("Location: ?page=login");
    exit();
}

$userID = $_SESSION['userID'];

// Ambil data user dari database
$sql = "SELECT email, password FROM user WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $email = htmlspecialchars($row['email']);
    $password = htmlspecialchars($row['password']);
} else {
    echo "User not found.";
    exit();
}

// Fungsi untuk menghapus akun
if (isset($_POST['drop_account'])) {
    $sql_delete = "DELETE FROM user WHERE userID = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $userID);

    if ($stmt_delete->execute()) {
        // Hapus sesi setelah akun dihapus
        session_destroy();
        // Redirect ke halaman login
        header("Location: ?page=login");
        exit();
    } else {
        echo "Terjadi kesalahan saat menghapus akun.";
    }
}

// Fungsi update profil (dari kode sebelumnya)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Validasi input
    $newEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Email tidak valid.";
    } elseif (strlen($newPassword) < 6) {
        $error_message = "Password harus lebih dari 6 karakter.";
    } elseif ($newPassword !== $confirmPassword) {
        $error_message = "Password dan konfirmasi password tidak cocok.";
    } else {
        // Update email dan password di database tanpa enkripsi password
        $sql_update = "UPDATE user SET email = ?, password = ? WHERE userID = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $newEmail, $newPassword, $userID);

        if ($stmt_update->execute()) {
            $success_message = "Profil berhasil diperbarui.";
            // Refresh data terbaru
            $email = $newEmail;
            $password = $newPassword;
        } else {
            $error_message = "Terjadi kesalahan saat memperbarui profil.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
        }
        .toggle-password {
            cursor: pointer;
        }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="eye" viewBox="0 0 16 16">
      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
      </symbol>
      <symbol id="eye-slash" viewBox="0 0 16 16">
      <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
      <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
      <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>   
      </symbol>
</svg>
    <div class="container text-center mt-5 w-50">
        <img src="assets/img/sosiess-removebg.png" alt="" width="" height="100" class="me-2 position-center mb-4">
        <h2 class="text-center">Profil User</h2>

        <!-- Pesan Error -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Pesan Sukses -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Informasi User -->
        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="email" value="<?php echo $email; ?>" readonly>
          <label for="email">Email</label>
        </div>
        <div class="form-floating mb-3">
          <div class="input-group">
            <input type="password" class="form-control" id="password" value="<?php echo $password; ?>" readonly>
            <button class="btn btn-outline-secondary toggle-password" type="button"> <svg class="bi" width="1.2rem" height="1.2rem"><use href="#eye-slash"></use></svg></button>
          </div>
        </div>
        <!-- Form Update Profil -->
        <form action="" method="POST">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="newEmail" placeholder="name@example.com" value="<?php echo $email; ?>" required>
                <label for="newEmail">New Email</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="password" id="newPassword" placeholder="Password Baru" required>
                <label for="newPassword">New Password</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="confirm_password" id="confirmPassword" placeholder="Konfirmasi Password" required>
                <label for="confirmPassword">Confirm Password</label>
            </div>
            <button class="btn btn-primary w-100" type="submit" name="update">Update Profil</button>
        </form>
        
        <!-- Tombol Drop Akun -->
        <form action="" method="POST">
            <label class="form mt-5">
                <p class="text-center align-item-center">Wanna delete this account? <button class="btn btn-danger py-1" type="submit" name="drop_account">Drop</button></p>
            </label>
        </form>
    </div>
    <script>
    document.querySelector('.toggle-password').addEventListener('click', function() {
        var passwordInput = document.getElementById('password');
        var icon = this.querySelector('svg use');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.setAttribute('href', '#eye'); // Ubah ikon menjadi mata
        } else {
            passwordInput.type = 'password';
            icon.setAttribute('href', '#eye-slash'); // Ubah ikon menjadi mata tertutup
        }
    });
    </script>

</body>
</html>
