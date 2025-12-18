<?php
// Navbar untuk admin
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="../Components/DashbordAdmin.php">Sistem Manajemen Inventori</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Master Data
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../User/User.php">User</a></li>
            <li><a class="dropdown-item" href="../Role/Role.php">Role</a></li>
            <li><a class="dropdown-item" href="../Satuan/Satuan.php">Satuan</a></li>
            <li><a class="dropdown-item" href="../Barang/Barang.php">Barang</a></li>
            <li><a class="dropdown-item" href="../Vendor/Vendor.php">Vendor</a></li>
            <li><a class="dropdown-item" href="../MarginPenjualan/MarginPenjualan.php">Margin Penjualan</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Transaksi
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../Pengadaan/Pengadaan.php">Pengadaan</a></li>
            <li><a class="dropdown-item" href="../Penerimaan/Penerimaan.php">Penerimaan</a></li>
            <li><a class="dropdown-item" href="../Penjualan/Penjualan.php">Penjualan</a></li>
            <li><a class="dropdown-item" href="../KartuStock/KartuStock.php">Kartu Stock</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../../../public/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>