<?php
class Barang
{
  //deklarasi property
  private $idbarang;
  private $jenis;
  private $nama;
  private $idsatuan;
  private $status;
  protected $db;
  protected $conn;

  //sambungkan ke database
  public function __construct()
  {
    $this->db = new Koneksi();
    $this->conn = $this->db->GetKoneksi();
  }

  //methode untuk CREATE
  public function CreateQuery($jenis, $nama, $idsatuan, $status, $harga)
  {
    $result = mysqli_query(
      $this->conn,
      "INSERT INTO barang (jenis, nama, idsatuan, status, harga) VALUES ('$jenis', '$nama', $idsatuan, $status, $harga)"
    );
  }

  //methode untuk READ
  public function ReadQuery()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * 
     FROM view_barang b
     ORDER BY b.nama ASC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ ststus penerimaan
  public function ReadQueryStatusPenerimaan()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * 
     FROM view_status_penerimaan
     WHERE jumlah_terima > 0'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ stok barang
  public function ReadQueryStokBarang($idbarang)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT fn_hitung_stock($idbarang) as stock;"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ ststus penerimaan konfirmasi
  public function ReadQueryStatusPenerimaanKonfirmasi($idbarang)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * 
     FROM view_status_penerimaan
     WHERE jumlah_terima > 0 and idbarang = $idbarang"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ Sisa Penerimaan
  public function ReadQuerySisaPenerimaan($idpengadaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * 
     FROM view_sisa_penerimaan
     WHERE idpengadaan = $idpengadaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ Sisa Penerimaan Konfrimasi
  public function ReadQuerySisaPenerimaanKonfrimasi($idpengadaan, $idbarang)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * 
     FROM view_sisa_penerimaan
     WHERE idpengadaan = $idpengadaan and idbarang = $idbarang"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ Sisa Penerimaan Update
  public function ReadQuerySisaPenerimaanUpdate($idpengadaan, $nama_barang)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * 
     FROM view_sisa_penerimaan
     WHERE idpengadaan = $idpengadaan and nama_barang = '$nama_barang'"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ One
  public function ReadQueryOne($idbarang)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_barang WHERE idbarang = $idbarang"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ
  public function ReadQueryAll()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * 
     FROM view_barang_all b
     ORDER BY b.nama ASC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit
  public function ReadEditQuery($idbarang)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_barang_all WHERE idbarang = $idbarang"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk UPDATE
  public function UpdateQuery($idbarang, $jenis, $nama, $idsatuan, $status, $harga)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE barang SET jenis = '$jenis', nama = '$nama', idsatuan = $idsatuan, status = $status, harga = $harga WHERE idbarang = $idbarang"
    );
  }

  //metode untuk DELETE
  public function DeleteQuery($idbarang)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM barang WHERE idbarang = $idbarang"
    );
  }

  //methode untuk mendapatkan semua satuan
  public function GetAllSatuan()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * FROM view_satuan WHERE status = 1 ORDER BY nama_satuan ASC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
}
