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
$iddetail_penjualan = isset($_GET['id']) ? $_GET['id'] : '';
$idpenjualan = isset($_GET['id_penjualan']) ? $_GET['id_penjualan'] : '';

// Jika tidak ada ID, redirect ke halaman penjualan
if (empty($iddetail_penjualan)) {
  header("Location: Penjualan.php");
  exit;
}

// Hapus data dari database
$Penjualan->DeleteQueryDetailPenjualan($iddetail_penjualan);

// Redirect ke halaman penjualan dengan parameter success
header("Location: DetailPenjualan.php?id=$idpenjualan&success=hapus");
exit;