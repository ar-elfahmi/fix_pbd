<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassVendor.php';

$Vendor = new Vendor();

// Ambil ID vendor yang akan dihapus
$idvendor = $_GET["id"];

// Hapus data vendor
$Vendor->DeleteQuery($idvendor);

// Redirect dengan parameter sukses
header("Location: Vendor.php?success=hapus");
exit;
?>