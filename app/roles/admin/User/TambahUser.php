<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassUser.php';

$User = new User();
$roles = $User->GetAllRoles();
$alert = '';

if (isset($_POST["Tambah"])) {
  $username = $_POST["username"];
  $password = $_POST["password"];
  $idrole = $_POST["idrole"];

  $User->CreateQuery($username, $password, $idrole);

  // Redirect dengan parameter sukses
  header("Location: user.php?success=tambah");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah User</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Tambah User</h1>

            <?php
            // Tampilkan alert jika ada parameter success
            if (isset($_GET["success"]) && $_GET["success"] == "tambah") {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    User baru berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
            ?>

            <form method="post">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>

              <div class="mb-3">
                <label for="idrole" class="form-label">Role</label>
                <select class="form-select" id="idrole" name="idrole" required>
                  <option value="">Pilih Role</option>
                  <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['idrole'] ?>"><?= htmlspecialchars($role['nama_role']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="User.php" class="btn btn-secondary me-md-2">Kembali</a>
                <button type="submit" name="Tambah" class="btn btn-success">Tambah</button>
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