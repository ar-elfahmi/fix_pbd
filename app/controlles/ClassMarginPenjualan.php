<?php
class MarginPenjualan
{
  //deklarasi property
  private $idmargin_penjualan;
  private $persen;
  private $status;
  private $iduser;
  private $created_at;
  private $updated_at;
  protected $db;
  protected $conn;

  //sambungkan ke database
  public function __construct()
  {
    $this->db = new Koneksi();
    $this->conn = $this->db->GetKoneksi();
  }

  //methode untuk CREATE
  public function CreateQuery($persen, $status, $iduser)
  {
    $query = "CALL sp_insert_margin('$persen', '$status', '$iduser')";
    return mysqli_query($this->conn, $query);
  }

  //methode untuk READ
  public function ReadQuery()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * 
     FROM view_margin_penjualan 
     ORDER BY created_at DESC'
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
      'SELECT * 
     FROM view_margin_penjualan_all 
     ORDER BY created_at DESC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit
  public function ReadEditQuery($idmargin_penjualan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_margin_penjualan_all WHERE idmargin_penjualan = $idmargin_penjualan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk UPDATE
  public function UpdateQuery($id, $persen, $status)
  {
    $query = "CALL sp_update_margin('$id', '$persen', '$status')";
    return mysqli_query($this->conn, $query);
  }

  //metode untuk DELETE
  public function DeleteQuery($idmargin_penjualan)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM margin_penjualan WHERE idmargin_penjualan = $idmargin_penjualan"
    );
  }

  //methode untuk mendapatkan semua user
  public function GetAllUser()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * FROM view_user ORDER BY username ASC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
}
