<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';

$Pengadaan = new Pengadaan();

// Ambil ID dari parameter URL
$iddetail_pengadaan = isset($_GET['id']) ? $_GET['id'] : '';
$idpengadaan = isset($_GET['id_pengadaan']) ? $_GET['id_pengadaan'] : '';

// Jika tidak ada ID, redirect ke halaman pengadaan
if (empty($iddetail_pengadaan)) {
  header("Location: Pengadaan.php");
  exit;
}

// Hapus data dari database
$Pengadaan->DeleteQueryDetailPengadaan($iddetail_pengadaan);

// Redirect ke halaman pengadaan dengan parameter success
header("Location: DetailPengadaan.php?id=$idpengadaan&success=hapus");
exit;
