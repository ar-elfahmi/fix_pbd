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
$vendors = $Penerimaan->GetVendorData();
$users = $Penerimaan->GetUserData();
$pengadaans = $Penerimaan->ReadQuery(); // Untuk dropdown pengadaan

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idpengadaan = $_POST['idpengadaan'];
    $iduser = $_SESSION["iduser"];
    $status = $_POST['status'];


    // Insert data ke database
    $Penerimaan->CreateQueryPenerimaan($idpengadaan, $iduser);

    // Redirect ke halaman penerimaan dengan parameter success
    header("Location: Penerimaan.php?success=tambah");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penerimaan</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Tambah Penerimaan</h1>

                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="idpengadaan" class="form-label">Pengadaan</label>
                                    <select class="form-select" id="idpengadaan" name="idpengadaan" required>
                                        <option value=""> Pilih Pengadaan </option>
                                        <?php foreach ($pengadaans as $pengadaan): ?>
                                            <option value="<?= $pengadaan['idpengadaan'] ?>"><?= $pengadaan['nama_vendor'] ?> - <?= date('d-m-Y', strtotime($pengadaan['timestamp'])) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value=""> Pilih Status Penerimaan </option>
                                        <option value="A">Aktif</option>
                                        <option value="N">Non-Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="Penerimaan.php" class="btn btn-secondary">Batal</a>
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