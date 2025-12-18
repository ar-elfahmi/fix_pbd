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


//tangkap id penjualan yang dikirim lewat url
if (isset($_GET["id"])) {
    $idpenjualan = $_GET["id"];
}
$Penjualan = new Pengadaan();
//ambil informasi penjualan tersebut
$informasi = $Penjualan->ReadQueryInfromasiPenjualan($idpenjualan);
// 1 id penjualan memungkinkan memiliki beberapa detail penjualan, ambil data detail penjualan yang bersangkutan
$rows = $Penjualan->ReadQueryOnePenjualan($idpenjualan);
$alert = '';

// Ambil semua barang untuk dropdown
$barang = new Barang;
$barang_rows = $barang->ReadQueryStatusPenerimaan();

if (isset($_GET["success"])) {
    $action = $_GET["success"];
    $message = '';
    $type = 'success';

    switch ($action) {
        case 'tambah':
            $message = 'Penjualan baru berhasil ditambahkan!';
            break;
        case 'edit':
            $message = 'Data penjualan berhasil diedit!';
            break;
        case 'hapus':
            $message = 'Penjualan berhasil dihapus!';
            $type = 'info';
            break;
        case 'gagal':
            $message = 'Stok barang tidak cukup';
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
    <title>Detail Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body class="bg-light">
    <div class="container">
        <!-- START FORM -->
        <div class="my-3 p-3 bg-body rounded card shadow">
            <?= $alert ?>
            <form method="post" action="TambahDetailPenjualan.php">
                <div class="mb-3 row">
                    <label for="idbarang" class="col-sm-2 col-form-label ">Barang</label>
                    <div class="col-sm-10">
                        <select class="form-select col-sm-10" id="idbarang" name="idbarang" required>
                            <option value="">Pilih Barang</option>
                            <?php foreach ($barang_rows as $brg): ?>
                                <option value="<?= $brg['idbarang'] ?>">
                                    <?= $brg['nama_barang'] ?> -- stock: <?php $temp = $barang->ReadQueryStokBarang($brg['idbarang']);
                                                                            echo $temp[0]["stock"] ?> --
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="jumlah" class="col-sm-2 col-form-label ">Jumlah</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="jumlah" required>
                    </div>
                </div>
                <input type="hidden" name="idpenjualan" value="<?= $idpenjualan ?>">
                <div class="mb-3 row">
                    <div class="col-sm-10"><button type="submit" class="btn btn-primary" name="tambahData">SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- AKHIR FORM -->
        <!-- Informasi Penjualan -->
        <div class="my-3 p-3 bg-body rounded card shadow">

            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Tanggal Penjualan dibuat</th>
                        <th>Kasir</th>
                        <th>Margin %</th>
                        <th>PPN</th>
                        <th>Sub Total</th>
                        <th>Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $informasi[0]["created_at"] ?> </td>
                        <td><?= $informasi[0]["kasir"] ?></td>
                        <td><?= $informasi[0]["margin_persen"] ?>%</td>
                        <td><?= $informasi[0]["ppn"] ?>%</td>
                        <td>Rp.<?= $informasi[0]["subtotal_nilai"] ?></td>
                        <td>Rp.<?= $informasi[0]["total_nilai"] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- START DATA -->
        <div class="my-3 p-3 bg-body rounded card shadow">
            <h1>Detail Penjualan</h1>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="col-md-1">No</th>
                        <th class="col-md-2">Barang</th>
                        <th class="col-md-3">Harga Satuan</th>
                        <th class="col-md-3">Jumlah</th>
                        <th class="col-md-3">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($rows as $row): ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= $row["nama_barang"] ?></td>
                            <td><?= $row["harga_satuan"] ?></td>
                            <td><?= $row["jumlah"] ?></td>
                            <td><?= $row["subtotal"] ?></td>
                            <td>
                                <a href="UpdateDetailPenjualan.php?id=<?= $row['iddetail_penjualan'] ?>&id_penjualan=<?= $idpenjualan ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="HapusDetailPenjualan.php?id=<?= $row['iddetail_penjualan'] ?>&id_penjualan=<?= $idpenjualan ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus detail penjualan ini?');">Hapus</a>
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