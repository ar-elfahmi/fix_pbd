<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require 'NavbarAdmin.php';
require '../../../config/Koneksi.php';
require '../../../controlles/ClassBarang.php';
require '../../../controlles/ClassVendor.php';
require '../../../controlles/ClassSatuan.php';
require '../../../controlles/ClassUser.php';

$Barang = new Barang();
$Vendor = new Vendor();
$Satuan = new Satuan();
$User = new User();

// Hitung jumlah data
$jumlah_barang = count($Barang->ReadQuery());
$jumlah_vendor = count($Vendor->ReadQuery());
$jumlah_satuan = count($Satuan->ReadQuery());
$jumlah_user = count($User->ReadQuery());
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .dashboard-card {
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .dashboard-card:hover {
      transform: translateY(-5px);
    }

    .card-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }

    .stat-number {
      font-size: 2rem;
      font-weight: bold;
    }

    .quick-actions {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 1.5rem;
    }

    .action-btn {
      transition: all 0.3s ease;
    }

    .action-btn:hover {
      transform: scale(1.05);
    }
  </style>
</head>

<body class="bg-light">
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <h1 class="mb-4">Dashboard Admin</h1>

        <!-- Statistics Cards -->
        <div class="row mb-4">
          <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center text-white bg-primary">
              <div class="card-body">
                <div class="card-icon">
                  <i class="fas fa-box"></i>
                </div>
                <h5 class="card-title">Total Barang</h5>
                <p class="stat-number"><?= $jumlah_barang ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center text-white bg-success">
              <div class="card-body">
                <div class="card-icon">
                  <i class="fas fa-truck"></i>
                </div>
                <h5 class="card-title">Total Vendor</h5>
                <p class="stat-number"><?= $jumlah_vendor ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center text-white bg-warning">
              <div class="card-body">
                <div class="card-icon">
                  <i class="fas fa-weight"></i>
                </div>
                <h5 class="card-title">Total Satuan</h5>
                <p class="stat-number"><?= $jumlah_satuan ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center text-white bg-info">
              <div class="card-body">
                <div class="card-icon">
                  <i class="fas fa-users"></i>
                </div>
                <h5 class="card-title">Total User</h5>
                <p class="stat-number"><?= $jumlah_user ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
          <div class="col-12">
            <div class="card quick-actions">
              <div class="card-body">
                <h4 class="card-title mb-4">Aksi Cepat</h4>
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <a href="../Barang/Barang.php" class="btn btn-primary action-btn w-100">
                      <i class="fas fa-box me-2"></i>Kelola Barang
                    </a>
                  </div>
                  <div class="col-md-3 mb-3">
                    <a href="../Vendor/Vendor.php" class="btn btn-success action-btn w-100">
                      <i class="fas fa-truck me-2"></i>Kelola Vendor
                    </a>
                  </div>
                  <div class="col-md-3 mb-3">
                    <a href="../Satuan/Satuan.php" class="btn btn-warning action-btn w-100">
                      <i class="fas fa-weight me-2"></i>Kelola Satuan
                    </a>
                  </div>
                  <div class="col-md-3 mb-3">
                    <a href="../User/DataMaster.php" class="btn btn-info action-btn w-100">
                      <i class="fas fa-users me-2"></i>Kelola User
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Aktivitas Terbaru</h4>
                <div class="list-group">
                  <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        <span>Barang baru ditambahkan</span>
                      </div>
                      <small class="text-muted">2 menit yang lalu</small>
                    </div>
                  </div>
                  <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <i class="fas fa-edit text-warning me-2"></i>
                        <span>Data vendor diperbarui</span>
                      </div>
                      <small class="text-muted">15 menit yang lalu</small>
                    </div>
                  </div>
                  <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <i class="fas fa-user-plus text-info me-2"></i>
                        <span>User baru terdaftar</span>
                      </div>
                      <small class="text-muted">1 jam yang lalu</small>
                    </div>
                  </div>
                </div>
              </div>
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