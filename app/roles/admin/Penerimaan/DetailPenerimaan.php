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


//tangkap id penerimaan yang dikirim lewat url
if (isset($_GET["id"])) {
    $idpenerimaan = $_GET["id"];
}
$Penerimaan = new Pengadaan();
//ambil informasi penerimaan tersebut
$informasi = $Penerimaan->ReadQueryInfromasiPenerimaan($idpenerimaan);
// 1 id penerimaan memungkinkan memiliki beberapa detail penerimaan, ambil data detail penerimaan yang bersangkutan
$rows = $Penerimaan->ReadQueryOnePenerimaan($idpenerimaan);
$alert = '';

// Ambil semua barang yang ada di pengadaan sesuai dengan detail pengadaannya untuk dropdown
$barang = new Barang;
$barang_rows = $barang->ReadQuerySisaPenerimaan($informasi[0]["idpengadaan"]);

if (isset($_GET["success"])) {
    $action = $_GET["success"];
    $message = '';
    $type = 'success';

    switch ($action) {
        case 'tambah':
            $message = 'Penerimaan baru berhasil ditambahkan!';
            break;
        case 'edit':
            $message = 'Data penerimaan berhasil diedit!';
            break;
        case 'hapus':
            $message = 'Penerimaan berhasil dihapus!';
            $type = 'info';
            break;
        case 'gagal':
            $message = 'Sisa penerimaan tidak mencukupi!';
            $type = 'danger';
            break;
    }

    $alert = '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
        ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Penerimaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body class="bg-light">
    <div class="container">
        <!-- START FORM -->
        <div class="my-3 p-3 bg-body rounded card shadow">
            <?= $alert ?>
            <form method="post" action="TambahDetailPenerimaan.php">
                <div class="mb-3 row">
                    <label for="idbarang" class="col-sm-2 col-form-label ">Barang</label>
                    <div class="col-sm-10">
                        <select class="form-select col-sm-10" id="idbarang" name="idbarang" required>
                            <option value="">Pilih Barang</option>
                            <?php foreach ($barang_rows as $barang): ?>
                                <option value="<?= $barang['idbarang'] ?>">
                                    <?= $barang['nama_barang'] ?> -- sisa pemerimaan = <?= $barang['sisa_penerimaan'] ?> --
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="jumlah_terima" class="col-sm-2 col-form-label ">Jumlah Terima</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="jumlah_terima" required min=1>
                    </div>
                </div>
                <input type="hidden" name="idpenerimaan" value="<?= $idpenerimaan ?>">
                <input type="hidden" name="idpengadaan" value="<?= $informasi[0]["idpengadaan"] ?>">
                <div class="mb-3 row">
                    <div class="col-sm-10"><button type="submit" class="btn btn-secondary" name="tambahData">SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- AKHIR FORM -->
        <!-- Informasi Penerimaan -->
        <div class="my-3 p-3 bg-body rounded card shadow">

            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Tanggal Penerimaan dibuat</th>
                        <th>Nama Vendor</th>
                        <th>Penerima</th>
                        <th>Status</th>
                        <th>ID Pengadaan</th>
                    </tr>
                </thead>
                <tbody>
                    <td><?= $informasi[0]["created_at"] ?></td>
                    <td><?= $informasi[0]["nama_vendor"] ?></td>
                    <td><?= $informasi[0]["penerima"] ?></td>
                    <td><?php if ($informasi[0]["status"] == "A") {
                            echo "Not yet approved";
                        } else if ($informasi[0]["status"] == "N") {
                            echo "Approved (lock)";
                        } ?> </td>
                    <td><?= $informasi[0]["idpengadaan"] ?></td>
                </tbody>
            </table>
        </div>

        <!-- START DATA -->
        <div class="my-3 p-3 bg-body rounded card shadow">
            <h1>Detail Penerimaan</h1>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="col-md-1">No</th>
                        <th class="col-md-2">Barang</th>
                        <th class="col-md-3">Harga Satuan Terima</th>
                        <th class="col-md-3">Jumlah Terima</th>
                        <th class="col-md-3">Sub Total Terima</th>
                        <?php if ($informasi[0]["status"] == "A") { ?>
                            <th class="col-md-3">Aksi</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($rows as $row): ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= $row["nama_barang"] ?></td>
                            <td>Rp.<?= $row["harga_satuan_terima"] ?></td>
                            <td><?= $row["jumlah_terima"] ?></td>
                            <td>Rp.<?= $row["sub_total_terima"] ?></td>
                            <?php if ($informasi[0]["status"] == "A") { ?>
                                <td>
                                    <a href="UpdateDetailPenerimaan.php?id=<?= $row['iddetail_penerimaan'] ?>&id_penerimaan=<?= $idpenerimaan ?>&nama_barang=<?= $row['nama_barang'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="HapusDetailPenerimaan.php?id=<?= $row['iddetail_penerimaan'] ?>&id_penerimaan=<?= $idpenerimaan ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus detail penerimaan ini?');">Hapus</a>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($informasi[0]["status"] == "A") { ?>
                <form action="UpdatePenerimaan.php?id=<?= $informasi[0]['idpenerimaan'] ?>" method="post">
                    <input type="number" id="idpengadaan" name="idpengadaan" required value="<?= $informasi[0]['idpengadaan'] ?>" hidden>
                    <input type="text" id="status" name="status" required value="N" hidden>
                    <input type="number" id="idpenerimaan" hidden name="idpenerimaan" value=<?= $informasi[0]['idpenerimaan'] ?>>
                    <button type="submit" class="btn btn-primary">Approved</button>
                </form>
            <?php } ?>
            <a href="Penerimaan.php" class="btn btn-success mb-3">Kembali</a>
        </div>
        <!-- AKHIR DATA -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous">
    </script>

</body>

</html>