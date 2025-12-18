<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassVendor.php';

$Vendor = new Vendor();
$alert = '';

if (isset($_POST["Tambah"])) {
  $nama_vendor = $_POST["nama_vendor"];
  $badan_hukum = $_POST["badan_hukum"];
  $status = $_POST["status"];

  $Vendor->CreateQuery($nama_vendor, $badan_hukum, $status);

  // Redirect dengan parameter sukses
  header("Location: Vendor.php?success=tambah");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Vendor</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Tambah Vendor</h1>

            <?php
            // Tampilkan alert jika ada parameter success
            if (isset($_GET["success"]) && $_GET["success"] == "tambah") {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Vendor baru berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
            ?>

            <form method="post">
              <div class="mb-4">
                <label for="nama_vendor" class="label">Vendor</label>
                <input type="text" name="nama_vendor" id="nama_vendor" required>
              </div>

              <div class="mb-3">
                <label for="badan_hukum" class="form-label">Badan Hukum</label>
                <select class="form-select" id="badan_hukum" name="badan_hukum" required>
                  <option value="">Pilih Status Badan Hukum</option>
                  <option value="Y">Ya</option>
                  <option value="N">Tidak</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="status" class="form-label">Status Vendor</label>
                <select class="form-select" id="status" name="status" required>
                  <option value="">Pilih Status</option>
                  <option value="A">Aktif</option>
                  <option value="N">Non-Aktif</option>
                </select>
              </div>

              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="Vendor.php" class="btn btn-secondary me-md-2">Kembali</a>
                <button type="submit" name="Tambah" class="btn btn-success">Tambah</button>
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