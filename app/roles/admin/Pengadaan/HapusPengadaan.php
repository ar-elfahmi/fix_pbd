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
$idpengadaan = isset($_GET['id']) ? $_GET['id'] : '';

// Jika tidak ada ID, redirect ke halaman pengadaan
if (empty($idpengadaan)) {
  header("Location: Pengadaan.php");
  exit;
}

// Hapus data dari database
$Pengadaan->DeleteQuery($idpengadaan);

// Redirect ke halaman pengadaan dengan parameter success
header("Location: Pengadaan.php?success=hapus");
exit;
