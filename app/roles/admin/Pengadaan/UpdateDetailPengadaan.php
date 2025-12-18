<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../../public/login.php");
    exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';
require '../../../controlles/ClassBarang.php';

$Pengadaan = new Pengadaan();

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $iddetail_pengadaan = $_POST["iddetail_pengadaan"];
    $idbarang = $_POST["idbarang"];
    $jumlah = $_POST["jumlah"];
    $idpengadaan = $_POST["idpengadaan"];
    var_dump($iddetail_pengadaan);
    var_dump($idpengadaan);



    // Update data di database
    $Pengadaan->UpdateQueryDetailPengadaan($iddetail_pengadaan, $idbarang, $jumlah);

    // Redirect ke halaman pengadaan dengan parameter success
    header("Location: DetailPengadaan.php?id=$idpengadaan&success=edit");
    exit;
}

// Ambil ID dari parameter URL
$iddetail_pengadaan = isset($_GET['id']) ? $_GET['id'] : '';
$idpengadaan = isset($_GET['id_pengadaan']) ? $_GET['id_pengadaan'] : '';

// Jika tidak ada ID, redirect ke halaman pengadaan
if (empty($iddetail_pengadaan)) {
    header("Location: Pengadaan.php");
    exit;
}

// Ambil data detail pengadaan berdasarkan ID
$editData = $Pengadaan->ReadEditQueryDetailPengadaan($iddetail_pengadaan);
// var_dump($editData);

// Jika data tidak ditemukan, redirect ke halaman pengadaan
if (empty($editData)) {
    header("Location: Pengadaan.php");
    exit;
}

$editData = $editData[0]; // Ambil data pertama dari array
// Ambil semua barang untuk dropdown
$barang = new Barang;
$barang_rows = $barang->ReadQuery();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Detail Pengadaan</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Edit Detail Pengadaan</h1>
                        <!-- START FORM -->
                        <form method="post" action="UpdateDetailPengadaan.php">
                            <div class="mb-3 row">
                                <label for="idbarang" class="col-sm-2 col-form-label ">Barang</label>
                                <div class="col-sm-10">
                                    <select class="form-select col-sm-10" id="idbarang" name="idbarang" required>
                                        <option value="">Pilih Barang</option>
                                        <?php foreach ($barang_rows as $barang): ?>
                                            <option value="<?= $barang['idbarang'] ?>" <?= $barang['nama'] == $editData['nama_barang'] ? 'selected' : '' ?>>
                                                <?= $barang['nama'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="jumlah" class="col-sm-2 col-form-label ">Jumlah</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="jumlah" required min="0" value="<?= $editData['jumlah'] ?>">
                                    <input type="number" name="idpengadaan" value="<?= $idpengadaan ?>" hidden>
                                    <input type="number" name="iddetail_pengadaan" value="<?= $iddetail_pengadaan ?>" hidden>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-sm-10"><button type="submit" class="btn btn-primary" name="tambahData">Update</button>
                                </div>
                            </div>
                        </form>
                        <!-- AKHIR FORM -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>