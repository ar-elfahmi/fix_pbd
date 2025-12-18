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
$rows = $MarginPenjualan->ReadQueryAll();
$alert = '';

// Tampilkan alert jika ada parameter success
if (isset($_GET["success"])) {
  $action = $_GET["success"];
  $message = '';
  $type = 'success';

  switch ($action) {
    case 'tambah':
      $message = 'Margin penjualan baru berhasil ditambahkan!';
      break;
    case 'edit':
      $message = 'Data margin penjualan berhasil diedit!';
      break;
    case 'hapus':
      $message = 'Margin penjualan berhasil dihapus!';
      $type = 'info';
      break;
  }

  $alert = '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
        ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Margin Penjualan - Semua Data</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Data Margin Penjualan - Semua Data</h1>
            <?= $alert ?>
            <a href="TambahMarginPenjualan.php" class="btn btn-success mb-3">Tambah Margin Penjualan</a>
            <a href="MarginPenjualan.php" class="btn btn-success mb-3">Kembali</a>
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Margin Persen (%)</th>
                    <th>Status</th>
                    <th>Pembuat</th>
                    <th>Tanggal Dibuat</th>
                    <th>Tanggal Diupdate</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1;
                  foreach ($rows as $row): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $row["persen"]; ?>%</td>
                      <td><?= $row["status"] == 1 ? 'Aktif' : 'Non-Aktif'; ?></td>
                      <td><?= $row["username"]; ?></td>
                      <td><?= date('d-m-Y H:i', strtotime($row["created_at"])); ?></td>
                      <td><?= date('d-m-Y H:i', strtotime($row["updated_at"])); ?></td>
                      <td>
                        <a href="UpdateMarginPenjualan.php?id=<?= $row['idmargin_penjualan'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="HapusMarginPenjualan.php?id=<?= $row['idmargin_penjualan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus margin penjualan ini?');">Hapus</a>
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