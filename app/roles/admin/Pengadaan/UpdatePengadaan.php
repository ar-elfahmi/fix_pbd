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

// Ambil ID dari parameter URL
$idpengadaan = isset($_GET['id']) ? $_GET['id'] : '';

// Jika tidak ada ID, redirect ke halaman pengadaan
if (empty($idpengadaan)) {
  header("Location: Pengadaan.php");
  exit;
}

// Ambil data pengadaan berdasarkan ID
$editData = $Pengadaan->ReadEditQuery($idpengadaan);

// Jika data tidak ditemukan, redirect ke halaman pengadaan
if (empty($editData)) {
  header("Location: Pengadaan.php");
  exit;
}

$editData = $editData[0]; // Ambil data pertama dari array

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $user_iduser = $_SESSION["iduser"];
  $vendor_idvendor = $_POST['vendor_idvendor'];
  $status = $_POST['status'];
  $ppn = $_POST['ppn'];

  // Update data di database
  $Pengadaan->UpdateQuery($idpengadaan, $user_iduser, $vendor_idvendor, $ppn, $status);

  // Redirect ke halaman pengadaan dengan parameter success
  header("Location: Pengadaan.php?success=edit");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Pengadaan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Edit Pengadaan</h1>

            <form method="POST" action="">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="vendor_idvendor" class="form-label">Vendor</label>
                  <select class="form-select" id="vendor_idvendor" name="vendor_idvendor" required>
                    <option value="">-- Pilih Vendor --</option>
                    <?php foreach ($vendors as $vendor): ?>
                      <option value="<?= $vendor['idvendor'] ?>" <?= $vendor['nama_vendor'] == $editData['nama_vendor'] ? 'selected' : '' ?>>
                        <?= $vendor['nama_vendor'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status" required>
                    <option value="A" <?= $editData['status'] == 'A' ? 'selected' : '' ?>>Aktif</option>
                    <option value="N" <?= $editData['status'] == 'N' ? 'selected' : '' ?>>Non-Aktif</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4">
                  <label for="ppn" class="form-label">PPN (%)</label>
                  <input type="number" class="form-control" id="ppn" name="ppn"
                    value="<?= $editData['ppn'] ?>" step="0.01" required>
                </div>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="Pengadaan.php" class="btn btn-secondary">Batal</a>
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