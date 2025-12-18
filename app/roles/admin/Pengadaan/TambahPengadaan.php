<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';

$Pengadaan = new Pengadaan();
$vendors = $Pengadaan->GetVendorData();
$users = $Pengadaan->GetUserData();

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $iduser = $_SESSION["iduser"];
  $idvendor = $_POST['idvendor'];
  $ppn = $_POST['ppn'];
  $status = $_POST['status'];


  // Insert data ke database
  $Pengadaan->CreateQuery($iduser, $idvendor, $ppn, $status);

  // Redirect ke halaman pengadaan dengan parameter success
  header("Location: Pengadaan.php?success=tambah");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Pengadaan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Tambah Pengadaan</h1>

            <form method="POST" action="">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="idvendor" class="form-label">Vendor</label>
                  <select class="form-select" id="idvendor" name="idvendor" required>
                    <option value=""> Pilih Vendor </option>
                    <?php foreach ($vendors as $vendor): ?>
                      <option value="<?= $vendor['idvendor'] ?>"><?= $vendor['nama_vendor'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status" required>
                    <option value=""> Pilih Status Pengadaan </option>
                    <option value="A">Aktif</option>
                    <option value="N">Non-Aktif</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <label for="ppn" class="form-label">PPN (%)</label>
                <input type="number" class="form-control" id="ppn" name="ppn" value="11" step="0.01" required>
              </div>
              <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="Pengadaan.php" class="btn btn-secondary">Batal</a>
              </div>
          </div>

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