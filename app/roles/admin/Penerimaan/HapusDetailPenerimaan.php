<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';

$Penerimaan = new Pengadaan();

// Ambil ID dari parameter URL
$iddetail_penerimaan = isset($_GET['id']) ? $_GET['id'] : '';
$idpenerimaan = isset($_GET['id_penerimaan']) ? $_GET['id_penerimaan'] : '';

// Jika tidak ada ID, redirect ke halaman penerimaan
if (empty($iddetail_penerimaan)) {
  header("Location: Penerimaan.php");
  exit;
}

// Hapus data dari database
$Penerimaan->DeleteQueryDetailPenerimaan($iddetail_penerimaan);

// Redirect ke halaman penerimaan dengan parameter success
header("Location: DetailPenerimaan.php?id=$idpenerimaan&success=hapus");
exit;