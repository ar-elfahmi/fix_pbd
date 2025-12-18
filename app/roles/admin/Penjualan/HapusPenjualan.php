<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';

$Penjualan = new Pengadaan();

// Ambil ID dari parameter URL
$idpenjualan = isset($_GET['id']) ? $_GET['id'] : '';

// Jika tidak ada ID, redirect ke halaman penjualan
if (empty($idpenjualan)) {
  header("Location: Penjualan.php");
  exit;
}

// Hapus data dari database
$Penjualan->DeleteQueryPenjualan($idpenjualan);

// Redirect ke halaman penjualan dengan parameter success
header("Location: Penjualan.php?success=hapus");
exit;