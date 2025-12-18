<?php
session_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassUser.php';

$User = new User();

// Ambil ID user yang akan dihapus
$iduser = $_GET["id"];

// Hapus data user
$User->DeleteQuery($iduser);

// Redirect dengan parameter sukses
header("Location: user.php?success=hapus");
exit;
?>