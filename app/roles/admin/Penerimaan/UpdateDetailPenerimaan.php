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

$Penerimaan = new Pengadaan();

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $iddetail_penerimaan = $_POST["iddetail_penerimaan"];
    $idbarang = $_POST["idbarang"];
    $jumlah_terima = $_POST["jumlah_terima"];
    $idpenerimaan = $_POST["idpenerimaan"];
    $jumlah_terima_maksimal = $_POST["jumlah_terima_maksimal"];

    if ($jumlah_terima <= $jumlah_terima_maksimal) {
        // Update data di database
        $Penerimaan->UpdateQueryDetailPenerimaan($iddetail_penerimaan, $idbarang, $jumlah_terima);

        // Redirect ke halaman penerimaan dengan parameter success
        header("Location: DetailPenerimaan.php?id=$idpenerimaan&success=edit");
        exit;
    }
    header("Location: DetailPenerimaan.php?id=$idpenerimaan&success=gagal");
    exit;
}

// Ambil ID dari parameter URL
$iddetail_penerimaan = isset($_GET['id']) ? $_GET['id'] : '';
$idpenerimaan = isset($_GET['id_penerimaan']) ? $_GET['id_penerimaan'] : '';
$nama_barang = isset($_GET['nama_barang']) ? $_GET['nama_barang'] : '';

// ambil informasi pengadaan
$informasi = $Penerimaan->ReadQueryInfromasiPenerimaan($idpenerimaan);

// Jika tidak ada ID, redirect ke halaman penerimaan
if (empty($iddetail_penerimaan)) {
    header("Location: Penerimaan.php");
    exit;
}

// Ambil data detail penerimaan berdasarkan ID
$editData = $Penerimaan->ReadEditQueryDetailPenerimaan($iddetail_penerimaan);

// Jika data tidak ditemukan, redirect ke halaman penerimaan
if (empty($editData)) {
    header("Location: Penerimaan.php");
    exit;
}

$editData = $editData[0]; // Ambil data pertama dari array
// Ambil semua barang untuk dropdown
$barang = new Barang;
$barang_rows = $barang->ReadQuerySisaPenerimaanUpdate($informasi[0]["idpengadaan"], $nama_barang);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Detail Penerimaan</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Edit Detail Penerimaan</h1>
                        <!-- START FORM -->
                        <form method="post" action="UpdateDetailPenerimaan.php">
                            <div class="mb-3 row">
                                <label for="idbarang" class="col-sm-5 col-form-label ">Barang: <b><?= $editData['nama_barang'] ?></b></label>
                                <label for="tampil" class="col-sm-5 col-form-label ">Maksimal penerimaan: <b><?= ($barang_rows[0]['sisa_penerimaan'] ?? 0) + $editData['jumlah_terima'] ?></b></label>
                                <input type="number" name="jumlah_terima_maksimal" value="<?= $barang_rows[0]['sisa_penerimaan'] + $editData['jumlah_terima'] ?>" hidden>
                                <div class="col-sm-10">
                                    <select class="form-select col-sm-10" id="idbarang" name="idbarang" required hidden>
                                        <option value="">Barang</option>
                                        <?php foreach ($barang_rows as $barang): ?>
                                            <option value="<?= $barang['idbarang'] ?>" <?= $barang['nama_barang'] == $editData['nama_barang'] ? 'selected' : '' ?>>
                                                <?= $barang['nama_barang'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="jumlah_terima" class="col-sm-5 col-form-label ">Jumlah Terima</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="jumlah_terima" required min="0" value="<?= $editData['jumlah_terima'] ?>">
                                    <input type="number" name="idpenerimaan" value="<?= $idpenerimaan ?>" hidden>
                                    <input type="number" name="iddetail_penerimaan" value="<?= $iddetail_penerimaan ?>" hidden>
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