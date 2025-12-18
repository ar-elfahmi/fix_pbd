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


//tangkap id pengadaan yang dikirim lewat url
if (isset($_GET["id"])) {
    $idpengadaan = $_GET["id"];
}
$Pengadaan = new Pengadaan();
//ambil informasi pengadaan tersebut
$informasi = $Pengadaan->ReadQueryInfromasi($idpengadaan);
// 1 id pengadaan memugkinkan meiliki beberapa detail pengadaan, ambil data detail pengadaan yang bersangkutan
$rows = $Pengadaan->ReadQueryOne($idpengadaan);
$alert = '';

// Ambil semua barang untuk dropdown
$barang = new Barang;
$barang_rows = $barang->ReadQuery();

if (isset($_GET["success"])) {
    $action = $_GET["success"];
    $message = '';
    $type = 'success';

    switch ($action) {
        case 'tambah':
            $message = 'Pengadaan baru berhasil ditambahkan!';
            break;
        case 'edit':
            $message = 'Data pengadaan berhasil diedit!';
            break;
        case 'hapus':
            $message = 'Pengadaan berhasil dihapus!';
            $type = 'info';
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
    <title>Detail Pengadaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body class="bg-light">
    <div class="container">
        <!-- START FORM -->
        <div class="my-3 p-3 bg-body rounded card shadow">
            <?= $alert ?>
            <form method="post" action="TambahDetailPengadaan.php">
                <div class="mb-3 row">
                    <label for="idbarang" class="col-sm-2 col-form-label ">Barang</label>
                    <div class="col-sm-10">
                        <select class="form-select col-sm-10" id="idbarang" name="idbarang" required>
                            <option value="">Pilih Barang</option>
                            <?php foreach ($barang_rows as $barang): ?>
                                <option value="<?= $barang['idbarang'] ?>">
                                    <?= htmlspecialchars($barang['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="jumlah" class="col-sm-2 col-form-label ">Jumlah</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="jumlah">
                    </div>
                </div>
                <input type="hidden" name="idpengadaan" value="<?= $idpengadaan ?>">
                <div class="mb-3 row">
                    <div class="col-sm-10"><button type="submit" class="btn btn-primary" name="tambahData">SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- AKHIR FORM -->
        <!-- Informasi Pengadaan -->
        <div class="my-3 p-3 bg-body rounded card shadow">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Timestamp</th>
                        <th>Pembuat</th>
                        <th>Status</th>
                        <th>Vendor</th>
                        <th>Sub Total</th>
                        <th>PPN</th>
                        <th>Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <td><?= $informasi[0]["timestamp"] ?></td>
                    <td><?= $informasi[0]["pembuat"] ?></td>
                    <td><?php if ($informasi[0]["status"] = "A") {
                            echo "Aktif";
                        } else if ($informasi[0]["status"] != "A") {
                            echo "Non-Aktif";
                        } ?></td>
                    <td><?= $informasi[0]["nama_vendor"] ?></td>
                    <td>Rp.<?= $informasi[0]["subtotal_nilai"] ?></td>
                    <td><?= $informasi[0]["ppn"] ?>%</td>
                    <td>Rp.<?= $informasi[0]["total_nilai"] ?></td>
                </tbody>
            </table>
        </div>

        <!-- START DATA -->
        <div class="my-3 p-3 bg-body rounded card shadow">
            <h1>Detail Pengadaan</h1>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="col-md-1">No</th>
                        <th class="col-md-2">Barang</th>
                        <th class="col-md-2">Harga Satuan</th>
                        <th class="col-md-2">Jumlah</th>
                        <th class="col-md-2">Sub total</th>
                        <th class="col-md-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($rows as $row): ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= $row["nama_barang"] ?></td>
                            <td>Rp.<?= $row["harga_satuan"] ?></td>
                            <td><?= $row["jumlah"] ?></td>
                            <td>Rp.<?= $row["sub_total"] ?></td>
                            <td>
                                <a href="UpdateDetailPengadaan.php?id=<?= $row['iddetail_pengadaan'] ?>&id_pengadaan=<?= $idpengadaan ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="HapusDetailPengadaan.php?id=<?= $row['iddetail_pengadaan'] ?>&id_pengadaan=<?= $idpengadaan ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus detail pengadaan ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <!-- AKHIR DATA -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous">
    </script>

</body>

</html>