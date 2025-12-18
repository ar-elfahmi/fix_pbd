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
$rows = $Penerimaan->ReadQueryPenerimaanAll();
$alert = '';

// Tampilkan alert jika ada parameter success
if (isset($_GET["success"])) {
  $action = $_GET["success"];
  $message = '';
  $type = 'success';

  switch ($action) {
    case 'tambah':
      $message = 'Penerimaan baru berhasil ditambahkan!';
      break;
    case 'edit':
      $message = 'Data penerimaan berhasil diedit!';
      break;
    case 'hapus':
      $message = 'Penerimaan berhasil dihapus!';
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
  <title>Data Penerimaan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Data Penerimaan</h1>
            <?= $alert ?>
            <a href="TambahPenerimaan.php" class="btn btn-success mb-3">Tambah Penerimaan</a>
            <a href="Penerimaan.php" class="btn btn-success mb-3">View</a>
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Tanggal Penerimaan</th>
                    <th>Penerima</th>
                    <th>Status</th>
                    <th>Vendor</th>
                    <th>ID Pengadaan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1;
                  foreach ($rows as $row): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= date('d-m-Y H:i:s', strtotime($row["created_at"])); ?></td>
                      <td><?= $row["penerima"]; ?></td>
                      <td><?= $row["status"] == 'A' ? 'Not yet approved' : 'Approved'; ?></td>
                      <td><?= $row["nama_vendor"]; ?></td>
                      <td><?= $row["idpengadaan"]; ?></td>
                      <td>
                        <a href="DetailPenerimaan.php?id=<?= $row['idpenerimaan'] ?>" class="btn btn-primary btn-sm">Detail</a>
                        <a href="UpdatePenerimaan.php?id=<?= $row['idpenerimaan'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <!-- <a href="HapusPenerimaan.php?id=<?= $row['idpenerimaan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus penerimaan ini?, Hapus detail penerimaan terrlebih dahulu agar Katru Stok tetap sesuai, klik batal jika ingin hapus detail penerimaan terrlebih dahulu');">Hapus</a> -->
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