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
$alert = '';

// Ambil data satuan yang akan diupdate
$idsatuan = $_GET["id"];
$satuan_data = $Satuan->ReadEditQuery($idsatuan);
$satuan_data = $satuan_data[0]; // Ambil data pertama

if(isset($_POST["editData"])){
    $nama_satuan = $_POST["nama_satuan"];
    $status = $_POST["status"];
    
    $Satuan->UpdateQuery($idsatuan, $nama_satuan, $status);
    
    // Redirect dengan parameter sukses
    header("Location: Satuan.php?success=edit");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Satuan</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Edit Satuan</h1>
            
            <?php 
            // Tampilkan alert jika ada parameter success
            if(isset($_GET["success"]) && $_GET["success"] == "edit"){
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    Data satuan berhasil diedit!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
            ?>
            
            <form method="post">
              <div class="mb-3">
                <label for="nama_satuan" class="form-label">Nama Satuan</label>
                <input type="text" class="form-control" id="nama_satuan" name="nama_satuan" 
                       value="<?= htmlspecialchars($satuan_data['nama_satuan']) ?>" required>
              </div>
              
              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                  <option value="">Pilih Status</option>
                  <option value="1" <?= $satuan_data['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                  <option value="0" <?= $satuan_data['status'] == 0 ? 'selected' : '' ?>>Non-Aktif</option>
                </select>
              </div>
              
              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="Satuan.php" class="btn btn-secondary me-md-2">Kembali</a>
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