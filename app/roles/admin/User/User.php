<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassRole.php';
require '../../../controlles/ClassUser.php';

$Role = new Role();
$User = new User();

$role_rows = $Role->ReadQuery();
$user_rows = $User->ReadQuery();
$alert = '';

// Tampilkan alert jika ada parameter success
if (isset($_GET["success"])) {
  $action = $_GET["success"];
  $message = '';
  $type = 'success';

  switch ($action) {
    case 'tambah':
      $message = 'Data berhasil ditambahkan!';
      break;
    case 'edit':
      $message = 'Data berhasil diedit!';
      break;
    case 'hapus':
      $message = 'Data berhasil dihapus!';
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
  <title>Data Master</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Data User</h1>
            <?= $alert ?>
            <a href="TambahUser.php" class="btn btn-success mb-3">Tambah User</a>
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1;
                  foreach ($user_rows as $row): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= htmlspecialchars($row["username"]); ?></td>
                      <td><?= htmlspecialchars($row["nama_role"]); ?></td>
                      <td>
                        <a href="UpdateUser.php?id=<?= urlencode($row['iduser']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="HapusUser.php?id=<?= urlencode($row['iduser']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?');">Hapus</a>
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