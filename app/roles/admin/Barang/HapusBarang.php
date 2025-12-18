<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassBarang.php';

$Barang = new Barang();

// Ambil ID barang yang akan dihapus
$idbarang = $_GET["id"];

// Hapus data barang
$Barang->DeleteQuery($idbarang);

// Redirect dengan parameter sukses
header("Location: Barang.php?success=hapus");
exit;
?>