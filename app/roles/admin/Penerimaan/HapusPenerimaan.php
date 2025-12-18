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
$idpenerimaan = isset($_GET['id']) ? $_GET['id'] : '';

// Jika tidak ada ID, redirect ke halaman penerimaan
if (empty($idpenerimaan)) {
  header("Location: Penerimaan.php");
  exit;
}

// Hapus data dari database
$Penerimaan->DeleteQueryPenerimaan($idpenerimaan);

// Redirect ke halaman penerimaan dengan parameter success
header("Location: Penerimaan.php?success=hapus");
exit;