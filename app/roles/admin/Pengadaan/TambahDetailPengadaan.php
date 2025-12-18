<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../../public/login.php");
  exit;
}

require '../../../config/Koneksi.php';
require '../../../controlles/ClassPengadaan.php';
require '../../../controlles/ClassBarang.php';

// Proses form jika ada data yang dikirim
if (isset($_POST["tambahData"])) {
  $idpengadaan = $_POST["idpengadaan"];
  $idbarang = $_POST["idbarang"];
  $jumlah = $_POST["jumlah"];

  // // ambil detail id barang
  // $detailBarang = new Barang;
  // $baris = $detailBarang->ReadQueryOne($idbarang);
  // // var_dump($baris);
  // $harga_satuan = $baris[0]["harga"];
  // $sub_total = $harga_satuan * $jumlah;

  // kirim query 
  $Pengadaan = new Pengadaan;
  $Pengadaan->CreateQueryDetailPengadaan($jumlah, $idbarang, $idpengadaan);

  // Redirect dengan parameter sukses
  header("Location: DetailPengadaan.php?id=$idpengadaan&success=tambah");
  exit;
}
