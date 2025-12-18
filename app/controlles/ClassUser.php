<?php
class User{
  //deklarasi property
  private $iduser;
  private $username;
  private $password;
  private $idrole;
  protected $db;
  protected $conn;

  //sambungkan ke database
  public function __construct(){
    $this->db = new Koneksi();
    $this->conn= $this->db->GetKoneksi();
  }
  
  //methode untuk CREATE
  public function CreateQuery($username, $password, $idrole){
    $result = mysqli_query($this->conn, 
    "INSERT INTO user (username, password, idrole) VALUES ('$username', '$password', $idrole)");
  }
  
  //methode untuk READ
  public function ReadQuery(){
    $result = mysqli_query($this->conn, 
    'SELECT u.iduser, u.username, u.nama_role 
     FROM view_user u
     ORDER BY u.username ASC');
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit
  public function ReadEditQuery($iduser){
    $result = mysqli_query($this->conn, 
    "SELECT * FROM view_user WHERE iduser = $iduser");
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk UPDATE
  public function UpdateQuery($iduser, $username, $password, $idrole){
    $result = mysqli_query($this->conn, 
    "UPDATE user SET username = '$username', password = '$password', idrole = $idrole WHERE iduser = $iduser");
  }

  //metode untuk LOGIN
  public function LoginQuery($username, $password){
    $result = mysqli_query($this->conn,
    "SELECT * FROM user
    WHERE username = '$username' AND password = '$password'");
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
      $rows[] = $row;
    }
    return $rows[0] ?? false;
  }

  //metode untuk DELETE
  public function DeleteQuery($iduser){
    $result = mysqli_query($this->conn,
    "DELETE FROM user WHERE iduser = $iduser");
  }

  //methode untuk mendapatkan semua role
  public function GetAllRoles(){
    $result = mysqli_query($this->conn, 
    'SELECT * FROM view_role ORDER BY nama_role ASC');
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
      $rows[] = $row;
    }
    return $rows;
  }
}