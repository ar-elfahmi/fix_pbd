<?php
//koneksi data base
class Koneksi
{
  // deklarasi property
  private $NamaServer = "localhost";
  private $NamaHost = "root";
  private $Password = "";
  private $NamaDatabase = "fix_pbd";
  private $conn;

  public function __construct()
  {
    $this->conn = mysqli_connect($this->NamaServer, $this->NamaHost, $this->Password, $this->NamaDatabase);
  }

  public function GetKoneksi()
  {
    return $this->conn;
  }
}
