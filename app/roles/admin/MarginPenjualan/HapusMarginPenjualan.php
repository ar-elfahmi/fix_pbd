<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassMarginPenjualan.php';

$MarginPenjualan = new MarginPenjualan();

// Ambil ID dari parameter URL
$idmargin_penjualan = isset($_GET["id"]) ? $_GET["id"] : '';

if (empty($idmargin_penjualan)) {
  header("Location: MarginPenjualan.php");
  exit;
}

// Hapus data dari database
$MarginPenjualan->DeleteQuery($idmargin_penjualan);

// Redirect ke halaman utama dengan parameter success
header("Location: MarginPenjualan.php?success=hapus");
exit;
?>