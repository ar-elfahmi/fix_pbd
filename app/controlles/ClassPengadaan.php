<?php
class Pengadaan
{
  //deklarasi property
  private $idpengadaan;
  private $timestamp;
  private $user_iduser;
  private $status;
  private $vendor_idvendor;
  private $subtotal_nilai;
  private $ppn;
  private $total_nilai;
  protected $db;
  protected $conn;

  //sambungkan ke database
  public function __construct()
  {
    $this->db = new Koneksi();
    $this->conn = $this->db->GetKoneksi();
  }

  //methode untuk CREATE
  public function CreateQuery($user_iduser, $vendor_idvendor, $ppn, $status)
  {
    $result = mysqli_query($this->conn, "INSERT INTO `pengadaan` (`idpengadaan`, `timestamp`, `user_iduser`, `status`, `vendor_idvendor`, `subtotal_nilai`, `ppn`, `total_nilai`) VALUES (NULL, CURRENT_TIMESTAMP, '$user_iduser', '$status', '$vendor_idvendor', NULL, '$ppn', NULL);");
  }

  //methode untuk CREATE Detail Pengadaan
  public function CreateQueryDetailPengadaan($jumlah, $idbarang, $idpengadaan)
  {
    $query = "INSERT INTO detail_pengadaan (jumlah, idbarang, idpengadaan) 
              VALUES ($jumlah, $idbarang, $idpengadaan)";
    $result = mysqli_query($this->conn, $query);
    if (!$result) {
      die("Query gagal: " . mysqli_error($this->conn));
    }
    return $result;
  }


  //methode untuk READ
  public function ReadQuery()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * 
     FROM view_pengadaan p
     ORDER BY p.timestamp DESC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ semua data
  public function ReadQueryAll()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT * 
     FROM view_pengadaan_all p
     ORDER BY p.timestamp DESC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ One
  public function ReadQueryOne($idpengadaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_detail_pengadaan WHERE idpengadaan = $idpengadaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ Informasi
  public function ReadQueryInfromasi($idpengadaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_pengadaan_all WHERE idpengadaan = $idpengadaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit
  public function ReadEditQuery($idpengadaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_pengadaan_all WHERE idpengadaan = $idpengadaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ edit detail pengadaan
  public function ReadEditQueryDetailPengadaan($iddetail_pengadaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_detail_pengadaan WHERE iddetail_pengadaan = $iddetail_pengadaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk UPDATE
  public function UpdateQuery($idpengadaan, $user_iduser, $vendor_idvendor, $ppn, $status)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE `pengadaan` SET `status` = '$status', `ppn` = '$ppn', `vendor_idvendor` = '$vendor_idvendor', `user_iduser` = '$user_iduser' WHERE `pengadaan`.`idpengadaan` = $idpengadaan;"
    );
  }
  //methode untuk UPDATE detail pengadaan
  public function UpdateQueryDetailPengadaan($iddetail_pengadaan, $idbarang, $jumlah)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE `detail_pengadaan` SET `harga_satuan` = '0', `jumlah` = '$jumlah', `sub_total` = '0', `idbarang` = '$idbarang' WHERE `detail_pengadaan`.`iddetail_pengadaan` = $iddetail_pengadaan;"
    );
  }

  //metode untuk DELETE
  public function DeleteQuery($idpengadaan)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM pengadaan WHERE idpengadaan = $idpengadaan"
    );
  }
  //metode untuk DELETE detail pengadaan
  public function DeleteQueryDetailPengadaan($iddetail_pengadaan)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM detail_pengadaan WHERE iddetail_pengadaan = $iddetail_pengadaan"
    );
  }

  //methode untuk mendapatkan semua vendor
  //methode untuk READ Vendor aktif menggunakan sp
  public function GetVendorData()
  {
    $query = 'call GetVendorData';
    $result = mysqli_query($this->conn, $query);
    if (mysqli_more_results($this->conn)) {
      mysqli_next_result($this->conn);
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk mendapatkan semua user
  public function GetUserData()
  {
    $query = 'call GetUserData';
    $result = mysqli_query($this->conn, $query);
    if (mysqli_more_results($this->conn)) {
      mysqli_next_result($this->conn);
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk mendapatkan semua user
  public function GetMargin()
  {
    $result = mysqli_query($this->conn, 'SELECT * FROM view_margin_penjualan Limit 1');
    if (mysqli_more_results($this->conn)) {
      mysqli_next_result($this->conn);
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  //methode untuk READ Penerimaan
  public function ReadQueryPenerimaan()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT *
     FROM view_penerimaan p
     WHERE status = "A"
     ORDER BY p.created_at DESC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ semua data penerimaan
  public function ReadQueryPenerimaanAll()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT *
     FROM view_penerimaan p
     ORDER BY p.created_at DESC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ One Penerimaan
  public function ReadQueryOnePenerimaan($idpenerimaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_detail_penerimaan WHERE idpenerimaan = $idpenerimaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ Informasi Penerimaan
  public function ReadQueryInfromasiPenerimaan($idpenerimaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_penerimaan WHERE idpenerimaan = $idpenerimaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit penerimaan
  public function ReadEditQueryPenerimaan($idpenerimaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_penerimaan WHERE idpenerimaan = $idpenerimaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit detail penerimaan
  public function ReadEditQueryDetailPenerimaan($iddetail_penerimaan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_detail_penerimaan WHERE iddetail_penerimaan = $iddetail_penerimaan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk CREATE Penerimaan
  public function CreateQueryPenerimaan($idpengadaan, $iduser)
  {
    $result = mysqli_query($this->conn, "INSERT INTO `penerimaan` (`idpenerimaan`, `created_at`, `status`, `idpengadaan`, `iduser`) VALUES (NULL, CURRENT_TIMESTAMP, 'A', '$idpengadaan', '$iduser');");
  }

  //methode untuk CREATE Detail Penerimaan
  public function CreateQueryDetailPenerimaan($jumlah_terima, $idbarang, $idpenerimaan)
  {
    $query = "INSERT INTO detail_penerimaan (jumlah_terima, idbarang, idpenerimaan)
              VALUES ($jumlah_terima, $idbarang, $idpenerimaan)";
    $result = mysqli_query($this->conn, $query);
    if (!$result) {
      die("Query gagal: " . mysqli_error($this->conn));
    }
    return $result;
  }

  //methode untuk UPDATE Penerimaan
  public function UpdateQueryPenerimaan($idpenerimaan, $idpengadaan, $iduser, $status)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE `penerimaan` SET `status` = '$status', `idpengadaan` = '$idpengadaan', `iduser` = '$iduser' WHERE `penerimaan`.`idpenerimaan` = $idpenerimaan;"
    );
  }

  //methode untuk UPDATE detail penerimaan
  public function UpdateQueryDetailPenerimaan($iddetail_penerimaan, $idbarang, $jumlah_terima)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE `detail_penerimaan` SET `jumlah_terima` = '$jumlah_terima', `idbarang` = '$idbarang' WHERE `detail_penerimaan`.`iddetail_penerimaan` = $iddetail_penerimaan;"
    );
  }

  //metode untuk DELETE Penerimaan
  public function DeleteQueryPenerimaan($idpenerimaan)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM penerimaan WHERE idpenerimaan = $idpenerimaan"
    );
  }

  //metode untuk DELETE detail penerimaan
  public function DeleteQueryDetailPenerimaan($iddetail_penerimaan)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM detail_penerimaan WHERE iddetail_penerimaan = $iddetail_penerimaan"
    );
  }

  //methode untuk READ Kartu Stok
  public function ReadQueryKartuStok()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT *
     FROM view_kartu_stok ks
     ORDER BY ks.idkartu_stok DESC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ Penjualan
  public function ReadQueryPenjualan()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT *
     FROM view_penjualan p
     ORDER BY p.created_at DESC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ semua data penjualan
  public function ReadQueryPenjualanAll()
  {
    $result = mysqli_query(
      $this->conn,
      'SELECT *
     FROM view_penjualan p
     ORDER BY p.created_at DESC'
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ One Penjualan
  public function ReadQueryOnePenjualan($idpenjualan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_detail_penjualan WHERE idpenjualan = $idpenjualan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ Informasi Penjualan
  public function ReadQueryInfromasiPenjualan($idpenjualan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_penjualan WHERE idpenjualan = $idpenjualan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit penjualan
  public function ReadEditQueryPenjualan($idpenjualan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_penjualan WHERE idpenjualan = $idpenjualan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk READ edit detail penjualan
  public function ReadEditQueryDetailPenjualan($iddetail_penjualan)
  {
    $result = mysqli_query(
      $this->conn,
      "SELECT * FROM view_detail_penjualan WHERE iddetail_penjualan = $iddetail_penjualan"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  //methode untuk CREATE Penjualan
  public function CreateQueryPenjualan($iduser, $idmargin_penjualan, $ppn)
  {
    $result = mysqli_query($this->conn, "INSERT INTO `penjualan` (`idpenjualan`, `created_at`,  `ppn`, `iduser`, `idmargin_penjualan`) VALUES (NULL, CURRENT_TIMESTAMP, '$ppn', '$iduser', $idmargin_penjualan);");
  }

  //methode untuk CREATE Detail Penjualan
  public function CreateQueryDetailPenjualan($jumlah, $idpenjualan, $idbarang)
  {
    $query = "INSERT INTO detail_penjualan (jumlah, idpenjualan, idbarang)
              VALUES ($jumlah, $idpenjualan, $idbarang)";
    $result = mysqli_query($this->conn, $query);
    if (!$result) {
      die("Query gagal: " . mysqli_error($this->conn));
    }
    return $result;
  }

  //methode untuk UPDATE Penjualan
  public function UpdateQueryPenjualan($idpenjualan, $iduser, $idmargin_penjualan, $subtotal_nilai, $ppn, $total_nilai)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE `penjualan` SET `iduser` = '$iduser', `idmargin_penjualan` = '$idmargin_penjualan', `subtotal_nilai` = '$subtotal_nilai', `ppn` = '$ppn', `total_nilai` = '$total_nilai' WHERE `penjualan`.`idpenjualan` = $idpenjualan;"
    );
  }

  //methode untuk UPDATE detail penjualan
  public function UpdateQueryDetailPenjualan($iddetail_penjualan, $harga_satuan, $jumlah, $subtotal, $idbarang)
  {
    $result = mysqli_query(
      $this->conn,
      "UPDATE `detail_penjualan` SET `harga_satuan` = '$harga_satuan', `jumlah` = '$jumlah', `subtotal` = '$subtotal', `idbarang` = '$idbarang' WHERE `detail_penjualan`.`iddetail_penjualan` = $iddetail_penjualan;"
    );
  }

  //metode untuk DELETE Penjualan
  public function DeleteQueryPenjualan($idpenjualan)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM penjualan WHERE idpenjualan = $idpenjualan"
    );
  }

  //metode untuk DELETE detail penjualan
  public function DeleteQueryDetailPenjualan($iddetail_penjualan)
  {
    $result = mysqli_query(
      $this->conn,
      "DELETE FROM detail_penjualan WHERE iddetail_penjualan = $iddetail_penjualan"
    );
  }
}
