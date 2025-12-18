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
$alert = '';

// Ambil semua satuan untuk dropdown
$satuan_rows = $Satuan->ReadQuery();

if (isset($_POST["tambahData"])) {
  $jenis = $_POST["jenis"];
  $nama = $_POST["nama"];
  $idsatuan = $_POST["idsatuan"];
  $status = $_POST["status"];
  $harga = $_POST["harga"];

  $Barang->CreateQuery($jenis, $nama, $idsatuan, $status, $harga);

  // Redirect dengan parameter sukses
  header("Location: Barang.php?success=tambah");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Barang</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Tambah Barang</h1>

            <?php
            // Tampilkan alert jika ada parameter success
            if (isset($_GET["success"]) && $_GET["success"] == "tambah") {
              echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    Barang baru berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
            ?>

            <form method="post">
              <div class="mb-3">
                <label for="jenis" class="form-label">Jenis</label>
                <select class="form-select" id="jenis" name="jenis" required>
                  <option value="">Pilih Jenis</option>
                  <option value="F">Food</option>
                  <option value="D">Drink</option>
                  <option value="S">Staff</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="nama" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
              </div>
              <div class="mb-3">
                <label for="harga" class="form-label">Harga Barang</label>
                <input type="text" class="form-control" id="harga" name="harga" required>
              </div>

              <div class="mb-3">
                <label for="idsatuan" class="form-label">Satuan</label>
                <select class="form-select" id="idsatuan" name="idsatuan" required>
                  <option value="">Pilih Satuan</option>
                  <?php foreach ($satuan_rows as $satuan): ?>
                    <option value="<?= $satuan['idsatuan'] ?>">
                      <?= htmlspecialchars($satuan['nama_satuan']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                  <option value="">Pilih Status</option>
                  <option value="1">Aktif</option>
                  <option value="0">Non-Aktif</option>
                </select>
              </div>

              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="Barang.php" class="btn btn-secondary me-md-2">Kembali</a>
                <button type="submit" name="tambahData" class="btn btn-success">Tambah</button>
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