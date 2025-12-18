<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../Components/NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';

$KartuStock = new Pengadaan();
$rows = $KartuStock->ReadQueryKartuStok();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Kartu Stok</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="card-title mb-4">Data Kartu Stok</h1>
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Jenis Transaksi</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Stock Akhir</th>
                    <th>ID Transaksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1;
                  foreach ($rows as $row): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= date('d-m-Y H:i:s', strtotime($row["created_at"])); ?></td>
                      <td><?= $row["nama_barang"]; ?></td>
                      <td>
                        <?php
                        switch ($row["jenis_transaksi"]) {
                          case 'P':
                            echo "Penerimaan";
                            break;
                          case 'J':
                            echo "Penjualan";
                            break;
                          case 'R':
                            echo "Retur";
                            break;
                          default:
                            echo $row["jenis_transaksi"];
                            break;
                        }
                        ?>
                      </td>
                      <td><?= $row["masuk"] > 0 ? $row["masuk"] : '-'; ?></td>
                      <td><?= $row["keluar"] > 0 ? $row["keluar"] : '-'; ?></td>
                      <td><strong><?= $row["stock"]; ?></strong></td>
                      <td><?= $row["idtransaksi"]; ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>