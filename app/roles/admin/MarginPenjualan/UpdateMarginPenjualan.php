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

// Ambil ID dari parameter URL
$idmargin_penjualan = isset($_GET["id"]) ? $_GET["id"] : '';

if (empty($idmargin_penjualan)) {
  header("Location: MarginPenjualan.php");
  exit;
}

// Ambil data untuk diisi di form
$rows = $MarginPenjualan->ReadEditQuery($idmargin_penjualan);
$row = $rows[0] ?? null;

if (!$row) {
  header("Location: MarginPenjualan.php");
  exit;
}

// Proses form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $persen = $_POST["persen"];
  $status = $_POST["status"];
  $iduser = $_SESSION["iduser"];

  // Validasi input
  if (empty($persen)) {
    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            Margin persen tidak boleh kosong!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
  } else {
    // Update data di database
    $MarginPenjualan->UpdateQuery($idmargin_penjualan, $persen, $status, $iduser);

    // Redirect ke halaman utama dengan parameter success
    header("Location: MarginPenjualan.php?success=edit");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Margin Penjualan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Edit Margin Penjualan</h1>
            <?= $alert ?>
            <a href="MarginPenjualan.php" class="btn btn-secondary mb-3">Kembali</a>

            <form method="POST" action="">
              <input type="hidden" name="idmargin_penjualan" value="<?= $idmargin_penjualan ?>">

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="persen" class="form-label">Margin Persen (%)</label>
                    <input type="number" class="form-control" id="persen" name="persen" step="0.01" min="0" max="100" value="<?= $row["persen"] ?>" required>
                    <div class="form-text">Masukkan nilai margin dalam persen (contoh: 10.5)</div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                      <option value="1" <?= $row["status"] == 1 ? 'selected' : '' ?>>Aktif</option>
                      <option value="0" <?= $row["status"] == 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="username" class="form-label">Pembuat</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= $row["username"] ?>" readonly>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="created_at" class="form-label">Tanggal Dibuat</label>
                    <input type="text" class="form-control" id="created_at" name="created_at" value="<?= date('d-m-Y H:i', strtotime($row["created_at"])) ?>" readonly>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="updated_at" class="form-label">Terakhir Diupdate</label>
                <input type="text" class="form-control" id="updated_at" name="updated_at" value="<?= date('d-m-Y H:i', strtotime($row["updated_at"])) ?>" readonly>
              </div>

              <button type="submit" class="btn btn-primary">Update</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>