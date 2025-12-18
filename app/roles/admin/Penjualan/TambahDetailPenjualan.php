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
  $idpenjualan = $_POST["idpenjualan"];
  $idbarang = $_POST["idbarang"];
  $jumlah = $_POST["jumlah"];

  // jika stok cukup
  $barang = new Barang;
  $barang_rows = $barang->ReadQueryStokBarang($idbarang);

  if ($barang_rows[0]['stock'] >= $jumlah) {
    // kirim query
    $Penjualan = new Pengadaan;
    $Penjualan->CreateQueryDetailPenjualan($jumlah, $idpenjualan, $idbarang);

    // Redirect dengan parameter sukses
    header("Location: DetailPenjualan.php?id=$idpenjualan&success=tambah");
    exit;
  }
  header("Location: DetailPenjualan.php?id=$idpenjualan&success=gagal");
  exit;
}
