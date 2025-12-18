<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassRole.php';

$Role = new Role();
$alert = '';

// Ambil data role yang akan diupdate
$idrole = $_GET["id"];
$role_data = $Role->ReadEditQuery($idrole);
$role_data = $role_data[0]; // Ambil data pertama

if(isset($_POST["editData"])){
    $nama_role = $_POST["nama_role"];
    
    $Role->UpdateQuery($idrole, $nama_role);
    
    // Redirect dengan parameter sukses
    header("Location: Role.php?success=edit");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Role</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Edit Role</h1>
            
            <?php 
            // Tampilkan alert jika ada parameter success
            if(isset($_GET["success"]) && $_GET["success"] == "edit"){
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    Data role berhasil diedit!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
            ?>
            
            <form method="post">
              <div class="mb-3">
                <label for="nama_role" class="form-label">Nama Role</label>
                <input type="text" class="form-control" id="nama_role" name="nama_role" 
                       value="<?= htmlspecialchars($role_data['nama_role']) ?>" required>
              </div>
              
              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="Role.php" class="btn btn-secondary me-md-2">Kembali</a>
                <button type="submit" name="editData" class="btn btn-warning">Update</button>
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