<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassBarang.php';
require '../../../controlles/ClassSatuan.php';

$Barang = new Barang();
$Satuan = new Satuan();
$rows = $Barang->ReadQuery();
$alert = '';

// Tampilkan alert jika ada parameter success
if (isset($_GET["success"])) {
  $action = $_GET["success"];
  $message = '';
  $type = 'success';

  switch ($action) {
    case 'tambah':
      $message = 'Barang baru berhasil ditambahkan!';
      break;
    case 'edit':
      $message = 'Data barang berhasil diedit!';
      break;
    case 'hapus':
      $message = 'Barang berhasil dihapus!';
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
  <title>Data Barang</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Data Barang</h1>
            <?= $alert ?>
            <a href="TambahBarang.php" class="btn btn-success mb-3">Tambah Barang</a>
            <a href="BarangAll.php" class="btn btn-success mb-3">View All</a>
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Jenis</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1;
                  foreach ($rows as $row): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $row["jenis"]; ?></td>
                      <td><?= $row["nama"]; ?></td>
                      <td><?= $row["nama_satuan"]; ?></td>
                      <td><?= $row["status"] == 1 ? 'Aktif' : 'Non-Aktif'; ?></td>
                      <td>Rp.<?= $row["harga"]; ?></td>
                      <td>
                        <a href="UpdateBarang.php?id=<?= $row['idbarang'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="HapusBarang.php?id=<?= $row['idbarang'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?, jika iya hapus kartu stok terlebih dahulu jika tidak data tidak akan terhapus dan kartu stok tidak sesuai');">Hapus</a>
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