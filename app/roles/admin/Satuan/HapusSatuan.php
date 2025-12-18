<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassSatuan.php';

$Satuan = new Satuan();

// Ambil ID satuan yang akan dihapus
$idsatuan = $_GET["id"];

// Hapus data satuan
$Satuan->DeleteQuery($idsatuan);

// Redirect dengan parameter sukses
header("Location: Satuan.php?success=hapus");
exit;
?>