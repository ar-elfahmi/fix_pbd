<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassRole.php';

$Role = new Role();

// Ambil ID role yang akan dihapus
$idrole = $_GET["id"];

// Hapus data role
$Role->DeleteQuery($idrole);

// Redirect dengan parameter sukses
header("Location: Role.php?success=hapus");
exit;
?>