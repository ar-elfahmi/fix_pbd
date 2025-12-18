<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';

$Penjualan = new Pengadaan();
$users = $Penjualan->GetUserData();
$margins = $Penjualan->GetMargin();

// Ambil ID dari parameter URL
$idpenjualan = isset($_GET['id']) ? $_GET['id'] : '';

// Jika tidak ada ID, redirect ke halaman penjualan
if (empty($idpenjualan)) {
  header("Location: Penjualan.php");
  exit;
}

// Ambil data penjualan berdasarkan ID
$editData = $Penjualan->ReadEditQueryPenjualan($idpenjualan);

// Jika data tidak ditemukan, redirect ke halaman penjualan
if (empty($editData)) {
  header("Location: Penjualan.php");
  exit;
}

$editData = $editData[0]; // Ambil data pertama dari array

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $iduser = $_POST['iduser'];
  $idmargin_penjualan = $_POST['idmargin_penjualan'];
  $ppn = $_POST['ppn'];
  $subtotal_nilai = $editData['subtotal_nilai']; // Gunakan subtotal yang sudah ada
  $total_nilai = $editData['total_nilai']; // Gunakan total yang sudah ada
  // var_dump($idmargin_penjualan);

  // Update data di database
  $Penjualan->UpdateQueryPenjualan($idpenjualan, $iduser, $idmargin_penjualan, $subtotal_nilai, $ppn, $total_nilai);

  // Redirect ke halaman penjualan dengan parameter success
  header("Location: Penjualan.php?success=edit");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Penjualan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Edit Penjualan</h1>

            <form method="POST" action="">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="iduser" class="form-label">Pembuat: <b><?= $editData['kasir'] ?></b></label>
                  <input type="number" name="iduser" hidden value=1>
                  <!-- <select class="form-select" id="iduser" name="iduser" hidden>
                    <option value="">-- Pilih Kasir --</option>
                    <?php foreach ($users as $user): ?>
                      <option value="<?= $user['iduser'] ?>" <?= $user['iduser'] == $editData['iduser'] ? 'selected' : '' ?>>
                        <?= $user['username'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select> -->
                </div>
                <div class="col-md-6">
                  <label for="idmargin_penjualan" class="form-label">Margin Penjualan: <b><?= $margins[0]['persen'] ?>%</b></label>
                  <input type="number" name="idmargin_penjualan" value="<?= $margins[0]['idmargin_penjualan'] ?>" hidden>
                  <!-- <select class="form-select" id="idmargin_penjualan" name="idmargin_penjualan" required>
                    <option value="">-- Pilih Margin --</option>
                    <?php foreach ($margins as $margin): ?>
                      <option value="<?= $margin['idmargin_penjualan'] ?>" <?= $margin['idmargin_penjualan'] == $editData['idmargin_penjualan'] ? 'selected' : '' ?>>
                        <?= $margin['persen'] ?>%
                      </option>
                    <?php endforeach; ?>
                  </select> -->
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
                <a href="Penjualan.php" class="btn btn-secondary">Batal</a>
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