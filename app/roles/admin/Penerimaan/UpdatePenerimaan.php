<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';

$Penerimaan = new Pengadaan();
$vendors = $Penerimaan->GetVendorData();
$users = $Penerimaan->GetUserData();
$pengadaans = $Penerimaan->ReadQuery(); // Untuk dropdown pengadaan

// Ambil ID dari parameter URL
$idpenerimaan = isset($_GET['id']) ? $_GET['id'] : '';

// Jika tidak ada ID, redirect ke halaman penerimaan
if (empty($idpenerimaan)) {
  header("Location: Penerimaan.php");
  exit;
}

// Ambil data penerimaan berdasarkan ID
$editData = $Penerimaan->ReadEditQueryPenerimaan($idpenerimaan);

// Jika data tidak ditemukan, redirect ke halaman penerimaan
if (empty($editData)) {
  header("Location: Penerimaan.php");
  exit;
}

$editData = $editData[0]; // Ambil data pertama dari array

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $idpengadaan = $_POST['idpengadaan'];
  $iduser = $_SESSION["iduser"];
  $status = $_POST['status'];
  $idpenerimaan = $_POST['idpenerimaan'];

  // Update data di database
  $Penerimaan->UpdateQueryPenerimaan($idpenerimaan, $idpengadaan, $iduser, $status);

  // Redirect ke halaman penerimaan dengan parameter success
  header("Location: Penerimaan.php?success=edit");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Penerimaan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Edit Penerimaan</h1>

            <form method="POST" action="">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="tampil" class="form-label">Nama Vendor: <?= $editData['nama_vendor'] ?></label>
                  <br>
                  <label for="tampil" class="form-label">ID Pengadaan: <?= $editData['idpengadaan'] ?></label>
                  <input type="number" id="idpengadaan" name="idpengadaan" required readonly value="<?= $editData['idpengadaan'] ?>" hidden>
                  <input type="number" id="idpenerimaan" hidden name="idpenerimaan" value=<?= $editData['idpenerimaan'] ?>>
                </div>
                <div class="col-md-6">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status" required>
                    <option value="A" <?= $editData['status'] == 'A' ? 'selected' : '' ?>>Not yet approved</option>
                    <option value="N" <?= $editData['status'] == 'N' ? 'selected' : '' ?>>Approved</option>
                  </select>
                </div>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="Penerimaan.php" class="btn btn-secondary">Batal</a>
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