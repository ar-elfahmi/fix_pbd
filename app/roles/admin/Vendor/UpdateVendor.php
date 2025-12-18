<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassVendor.php';

$Vendor = new Vendor();
$alert = '';

// Ambil data vendor yang akan diupdate
$idvendor = $_GET["id"];
$vendor_data = $Vendor->ReadEditQuery($idvendor);
$vendor_data = $vendor_data[0]; // Ambil data pertama

if(isset($_POST["editData"])){
    $nama_vendor = $_POST["nama_vendor"];
    $badan_hukum = $_POST["badan_hukum"];
    $status = $_POST["status"];
    
    $Vendor->UpdateQuery($idvendor, $nama_vendor, $badan_hukum, $status);
    
    // Redirect dengan parameter sukses
    header("Location: Vendor.php?success=edit");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Vendor</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Edit Vendor</h1>
            
            <?php 
            // Tampilkan alert jika ada parameter success
            if(isset($_GET["success"]) && $_GET["success"] == "edit"){
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    Data vendor berhasil diedit!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
            ?>
            
            <form method="post">
              <div class="mb-3">
                <label for="nama_vendor" class="form-label">Nama Vendor</label>
                <input type="text" class="form-control" id="nama_vendor" name="nama_vendor" 
                       value="<?= htmlspecialchars($vendor_data['nama_vendor']) ?>" required>
              </div>
              
              <div class="mb-3">
                <label for="badan_hukum" class="form-label">Badan Hukum</label>
                <select class="form-select" id="badan_hukum" name="badan_hukum" required>
                  <option value="">Pilih Status Badan Hukum</option>
                  <option value="Y" <?= $vendor_data['badan_hukum'] == 'Y' ? 'selected' : '' ?>>Ya</option>
                  <option value="N" <?= $vendor_data['badan_hukum'] == 'N' ? 'selected' : '' ?>>Tidak</option>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                  <option value="">Pilih Status</option>
                  <option value="A" <?= $vendor_data['status'] == 'A' ? 'selected' : '' ?>>Aktif</option>
                  <option value="N" <?= $vendor_data['status'] == 'N' ? 'selected' : '' ?>>Non-Aktif</option>
                </select>
              </div>
              
              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="Vendor.php" class="btn btn-secondary me-md-2">Kembali</a>
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