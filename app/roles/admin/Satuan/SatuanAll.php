<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassSatuan.php';

$Satuan = new Satuan();
$rows = $Satuan->ReadQueryAll();
$alert = '';

// Tampilkan alert jika ada parameter success
if(isset($_GET["success"])){
    $action = $_GET["success"];
    $message = '';
    $type = 'success';
    
    switch($action){
        case 'tambah':
            $message = 'Satuan baru berhasil ditambahkan!';
            break;
        case 'edit':
            $message = 'Data satuan berhasil diedit!';
            break;
        case 'hapus':
            $message = 'Satuan berhasil dihapus!';
            $type = 'info';
            break;
    }
    
    $alert = '<div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">
        '.$message.'
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Satuan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Data Satuan</h1>
            <?= $alert ?>
            <a href="TambahSatuan.php" class="btn btn-success mb-3">Tambah Satuan</a>
            <a href="Satuan.php" class="btn btn-success mb-3">Kembali</a>
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Nama Satuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach($rows as $row): ?>
                  <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row["nama_satuan"]); ?></td>
                    <td><?= $row["status"] == 1 ? 'Aktif' : 'Non-Aktif'; ?></td>
                    <td>
                      <a href="UpdateSatuan.php?id=<?= urlencode($row['idsatuan']) ?>" class="btn btn-warning btn-sm">Edit</a>
                      <a href="HapusSatuan.php?id=<?= urlencode($row['idsatuan']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus satuan ini?, jika iya hapus kartu stok terlebih dahulu jika tidak data tidak akan terhapus dan kartu stok tidak sesuai');">Hapus</a>
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