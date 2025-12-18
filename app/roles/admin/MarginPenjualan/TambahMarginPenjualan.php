<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassMarginPenjualan.php';

$MarginPenjualan = new MarginPenjualan();
$users = $MarginPenjualan->GetAllUser();
$alert = '';

// Proses form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $persen = $_POST["persen"];
  $status = isset($_POST["status"]) ? 1 : 0;
  $iduser = $_SESSION["iduser"];

  // Validasi input
  if (empty($persen)) {
    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            Margin persen tidak boleh kosong!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
  } else {
    // Insert data ke database
    $MarginPenjualan->CreateQuery($persen, $status, $iduser);

    // Redirect ke halaman utama dengan parameter success
    header("Location: MarginPenjualan.php?success=tambah");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Margin Penjualan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Tambah Margin Penjualan</h1>
            <?= $alert ?>

            <form method="POST" action="">
              <div class="mb-3">
                <label for="persen" class="form-label">Margin Persen (%)</label>
                <input type="number" class="form-control" id="persen" name="persen" step="0.01" min="0" max="100" required>
                <div class="form-text">Masukkan nilai margin dalam persen (contoh: 10.5)</div>
                <input type="text" class="form-control" id="iduser" name="iduser" value="<?= $_SESSION["username"] ?>" hidden>
                <input type="checkbox" class="form-check-input" id="status" name="status" value="1" hidden checked>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
            <a href="MarginPenjualan.php" class="btn btn-secondary">Kembali</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>