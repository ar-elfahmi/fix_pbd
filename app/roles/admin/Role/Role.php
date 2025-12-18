<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassRole.php';

//READ Database
$Role = new Role();
$rows = $Role->ReadQuery();

// Notifikasi aksi
$alert = '';
if(isset($_POST["Tambah"])){
  $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
    Role baru berhasil ditambahkan!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
if(isset($_POST["editData"])){
  $alert = '<div class="alert alert-info alert-dismissible fade show" role="alert">
    Data role berhasil diedit!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Role</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Data Role</h1>
            <?= $alert ?>
            <a href="TambahRole.php" class="btn btn-success mb-3">Tambah Role</a>
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Nama Role</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach($rows as $row): ?>
                  <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row["nama_role"]); ?></td>
                    <td>
                      <a href="UpdateRole.php?id=<?= urlencode($row['idrole']) ?>" class="btn btn-warning btn-sm">Edit</a>
                      <a href="HapusRole.php?id=<?= urlencode($row['idrole']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus role ini?');">Hapus</a>
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