<?php
class Role{
  //deklarasi property
  private $idrole;
  private $nama_role;
  protected $db;
  protected $conn;

  //sambungkan ke database
  public function __construct(){
    $this->db = new Koneksi();
    $this->conn= $this->db->GetKoneksi();
  }
  
  //methode untuk CREATE
  public function CreateQuery($nama_role){
    $result = mysqli_query($this->conn, "INSERT INTO role (nama_role) VALUES ('$nama_role')");
  }
  
  //methode untuk READ
  public function ReadQuery(){
    $result = mysqli_query($this->conn, 
    'SELECT * FROM view_role ORDER BY nama_role ASC');
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit
  public function ReadEditQuery($idrole){
    $result = mysqli_query($this->conn, 
    "SELECT * FROM view_role WHERE idrole = $idrole");
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk UPDATE
  public function UpdateQuery($idrole, $nama_role){
    $result = mysqli_query($this->conn, 
    "UPDATE role SET nama_role = '$nama_role' WHERE idrole = $idrole");
  }

  //metode untuk DELETE
  public function DeleteQuery($idrole){
    $result = mysqli_query($this->conn, 
    "DELETE FROM role WHERE idrole = $idrole");
  }
}