<?php
class Vendor
{
  //deklarasi property
  private $idvendor;
  private $nama_vendor;
  private $badan_hukum;
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
  public function CreateQuery($nama_vendor, $badan_hukum, $status)
  {
    $result = mysqli_query(
      $this->conn,
      "INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES ('$nama_vendor', '$badan_hukum', '$status')"
    );
  }


  //methode untuk READ
  public function ReadQuery()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * FROM view_vendor ORDER BY nama_vendor ASC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ All
  public function ReadQueryAll()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * FROM view_vendor_all ORDER BY nama_vendor ASC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit
  public function ReadEditQuery($idvendor)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_vendor_all WHERE idvendor = $idvendor"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk UPDATE
  public function UpdateQuery($idvendor, $nama_vendor, $badan_hukum, $status)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE vendor SET nama_vendor = '$nama_vendor', badan_hukum = '$badan_hukum', status = '$status' WHERE idvendor = $idvendor"
    );
  }

  //metode untuk DELETE
  public function DeleteQuery($idvendor)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM vendor WHERE idvendor = $idvendor"
    );
  }
}
