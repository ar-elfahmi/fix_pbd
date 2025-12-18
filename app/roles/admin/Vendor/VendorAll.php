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
$rows = $Vendor->ReadQueryAll();

// Notifikasi aksi
$alert = '';
if (isset($_POST["Tambah"])) {
  $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
    Vendor baru berhasil ditambahkan!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
if (isset($_POST["editData"])) {
  $alert = '<div class="alert alert-info alert-dismissible fade show" role="alert">
    Data vendor berhasil diedit!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Vendor</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Data Vendor</h1>
            <?= $alert ?>
            <a href="TambahVendor.php" class="btn btn-success mb-3">Tambah Vendor</a>
            <a href="Vendor.php" class="btn btn-success mb-3">Kembali</a>
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Nama Vendor</th>
                    <th>Badan Hukum</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1;
                  foreach ($rows as $row): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= htmlspecialchars($row["nama_vendor"]); ?></td>
                      <td><?= $row["badan_hukum"] == 'Y' ? 'Ya' : 'Tidak'; ?></td>
                      <td><?= $row["status"] == 'A' ? 'Aktif' : 'Non-Aktif'; ?></td>
                      <td>
                        <a href="UpdateVendor.php?id=<?= urlencode($row['idvendor']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="HapusVendor.php?id=<?= urlencode($row['idvendor']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus vendor ini?');">Hapus</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>