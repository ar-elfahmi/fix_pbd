<?php
class Satuan
{
  //deklarasi property
  private $idsatuan;
  private $nama_satuan;
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
  public function CreateQuery($nama_satuan, $status)
  {
    $result = mysqli_query(
      $this->conn,
      "INSERT INTO satuan (nama_satuan, status) VALUES ('$nama_satuan', $status)"
    );
  }

  //methode untuk READ
  public function ReadQuery()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * FROM view_satuan ORDER BY nama_satuan ASC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ ALl
  public function ReadQueryAll()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * FROM view_satuan_all ORDER BY nama_satuan ASC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit
  public function ReadEditQuery($idsatuan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_satuan_all WHERE idsatuan = $idsatuan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk UPDATE
  public function UpdateQuery($idsatuan, $nama_satuan, $status)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE satuan SET nama_satuan = '$nama_satuan', status = $status WHERE idsatuan = $idsatuan"
    );
  }

  //metode untuk DELETE
  public function DeleteQuery($idsatuan)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM satuan WHERE idsatuan = $idsatuan"
    );
  }
}
