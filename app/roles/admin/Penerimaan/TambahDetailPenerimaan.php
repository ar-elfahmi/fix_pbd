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
  $idpenerimaan = $_POST["idpenerimaan"];
  $idpengadaan = $_POST["idpengadaan"];
  $idbarang = $_POST["idbarang"];
  $jumlah_terima = $_POST["jumlah_terima"];

  $Penerimaan = new Pengadaan();
  //ambil informasi penerimaan tersebut
  $informasi = $Penerimaan->ReadQueryInfromasiPenerimaan($idpenerimaan);

  // ambil sisa yang bisa diterima
  $barang = new Barang;
  $barang_rows = $barang->ReadQuerySisaPenerimaanKonfrimasi($informasi[0]["idpengadaan"], $idbarang);
  $sisa_terima = $barang_rows[0]['sisa_penerimaan'];


  if ($jumlah_terima <= $sisa_terima) {
    // kirim query 
    $Penerimaan = new Pengadaan;
    $Penerimaan->CreateQueryDetailPenerimaan($jumlah_terima, $idbarang, $idpenerimaan);

    // Redirect dengan parameter sukses
    header("Location: DetailPenerimaan.php?id=$idpenerimaan&success=tambah");
    exit;
  }
  // Redirect dengan parameter sukses
  header("Location: DetailPenerimaan.php?id=$idpenerimaan&success=gagal");
}
