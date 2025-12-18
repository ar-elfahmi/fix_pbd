-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2025 at 02:50 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coba`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserData` ()   BEGIN
    -- Menjalankan query untuk mengambil semua data dari view_vendor
    SELECT * FROM view_user;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetvendorData` ()   BEGIN
    -- Menjalankan query untuk mengambil semua data dari view_vendor
    SELECT * FROM view_vendor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_check_pengadaan_complete` (IN `p_idpengadaan` INT)   BEGIN
    DECLARE v_total_sisa INT DEFAULT 0;
    
    -- Hitung total sisa penerimaan dari semua barang
    SELECT COALESCE(SUM(sisa_penerimaan), 0) INTO v_total_sisa
    FROM view_status_penerimaan
    WHERE idpengadaan = p_idpengadaan;
    
    -- Jika semua barang sudah diterima (total sisa = 0)
    IF v_total_sisa = 0 THEN
        -- Update status pengadaan menjadi 'S' (Selesai)
        UPDATE pengadaan
        SET status = 'N'
        WHERE idpengadaan = p_idpengadaan;
    ELSE
        -- Jika masih ada sisa, pastikan status 'A' (Aktif)
        UPDATE pengadaan
        SET status = 'A'
        WHERE idpengadaan = p_idpengadaan 
          AND status != 'A';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_margin` (IN `p_persen` DOUBLE, IN `p_status` TINYINT, IN `p_iduser` INT)   BEGIN
    -- Jika status = 1, non-aktifkan margin lain
    IF p_status = 1 THEN
        UPDATE margin_penjualan SET status = 0 WHERE status = 1;
    END IF;
    
    -- Insert margin baru
    INSERT INTO margin_penjualan (persen, status, iduser, created_at, updated_at)
    VALUES (p_persen, p_status, p_iduser, NOW(), NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_margin` (IN `p_idmargin` INT, IN `p_persen` DOUBLE, IN `p_status` TINYINT)   BEGIN
    -- Jika status = 1, non-aktifkan margin lain
    IF p_status = 1 THEN
        UPDATE margin_penjualan 
        SET status = 0 
        WHERE status = 1 AND idmargin_penjualan != p_idmargin;
    END IF;
    
    -- Update margin
    UPDATE margin_penjualan
    SET persen = p_persen,
        status = p_status,
        updated_at = NOW()
    WHERE idmargin_penjualan = p_idmargin;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_hitung_stock` (`p_idbarang` INT) RETURNS INT DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_stock_terakhir INT DEFAULT 0;
    
    -- Ambil stock terakhir berdasarkan created_at dan idkartu_stok
    SELECT stock INTO v_stock_terakhir
    FROM kartu_stok
    WHERE idbarang = p_idbarang
    ORDER BY created_at DESC, idkartu_stok DESC
    LIMIT 1;
    
    -- Jika tidak ada record (barang baru), return 0
    IF v_stock_terakhir IS NULL THEN
        SET v_stock_terakhir = 0;
    END IF;
    
    RETURN v_stock_terakhir;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_hitung_total_pengadaan` (`p_idpengadaan` INT) RETURNS INT DETERMINISTIC BEGIN
    DECLARE v_subtotal_nilai INT DEFAULT 0;
    DECLARE v_ppn INT DEFAULT 0;
    DECLARE v_total_nilai INT DEFAULT 0;
    
    -- 1. Hitung akumulasi sub_total dari detail_pengadaan
    SELECT COALESCE(SUM(sub_total), 0) INTO v_subtotal_nilai
    FROM detail_pengadaan
    WHERE idpengadaan = p_idpengadaan;
    
    -- 2. Ambil ppn dari tabel pengadaan
    SELECT ppn INTO v_ppn
    FROM pengadaan
    WHERE idpengadaan = p_idpengadaan;
    
    -- 3. Hitung total_nilai = subtotal_nilai + (subtotal_nilai * (ppn / 100))
    SET v_total_nilai = v_subtotal_nilai + (v_subtotal_nilai * (v_ppn / 100));
    
    -- 4. UPDATE tabel pengadaan
    UPDATE pengadaan
    SET subtotal_nilai = v_subtotal_nilai,
        total_nilai = v_total_nilai
    WHERE idpengadaan = p_idpengadaan;
    
    RETURN v_total_nilai;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_hitung_total_penjualan` (`p_idpenjualan` INT) RETURNS INT DETERMINISTIC BEGIN
    DECLARE v_subtotal_nilai INT DEFAULT 0;
    DECLARE v_margin_persen DOUBLE DEFAULT 0;
    DECLARE v_ppn INT DEFAULT 0;
    DECLARE v_margin_nilai INT DEFAULT 0;
    DECLARE v_ppn_nilai INT DEFAULT 0;
    DECLARE v_total_nilai INT DEFAULT 0;
    
    -- 1. Hitung akumulasi subtotal dari detail_penjualan
    SELECT COALESCE(SUM(subtotal), 0) INTO v_subtotal_nilai
    FROM detail_penjualan
    WHERE idpenjualan = p_idpenjualan;
    
    -- 2. Ambil margin persen dari margin_penjualan via penjualan
    SELECT mp.persen INTO v_margin_persen
    FROM penjualan p
    JOIN margin_penjualan mp ON p.idmargin_penjualan = mp.idmargin_penjualan
    WHERE p.idpenjualan = p_idpenjualan;
    
    -- 3. Ambil ppn dari tabel penjualan
    SELECT ppn INTO v_ppn
    FROM penjualan
    WHERE idpenjualan = p_idpenjualan;
    
    -- 4. Hitung margin_nilai = subtotal_nilai × (margin_persen / 100)
    SET v_margin_nilai = v_subtotal_nilai * (v_margin_persen / 100);
    
    -- 5. Hitung ppn_nilai = subtotal_nilai × (ppn / 100)
    SET v_ppn_nilai = v_subtotal_nilai * (v_ppn / 100);
    
    -- 6. Hitung total_nilai = subtotal_nilai + margin_nilai + ppn_nilai
    SET v_total_nilai = v_subtotal_nilai + v_margin_nilai + v_ppn_nilai;
    
    -- 7. UPDATE tabel penjualan
    UPDATE penjualan
    SET subtotal_nilai = v_subtotal_nilai,
        total_nilai = v_total_nilai
    WHERE idpenjualan = p_idpenjualan;
    
    RETURN v_total_nilai;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `idbarang` int NOT NULL,
  `jenis` char(1) DEFAULT NULL,
  `nama` varchar(45) NOT NULL,
  `idsatuan` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `harga` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`idbarang`, `jenis`, `nama`, `idsatuan`, `status`, `harga`) VALUES
(1, 'F', 'Beras Premium', 1, 1, 100000),
(3, 'F', 'Gula Pasir', 1, 1, 16000),
(4, 'F', 'Air Mineral 600ml', 3, 1, 5000),
(5, 'F', 'Mie Instan', 4, 0, 3000),
(6, 'S', 'Alat Hack Wifi', 6, 1, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `detail_penerimaan`
--

CREATE TABLE `detail_penerimaan` (
  `iddetail_penerimaan` int NOT NULL,
  `idpenerimaan` int DEFAULT NULL,
  `idbarang` int DEFAULT NULL,
  `jumlah_terima` int DEFAULT NULL,
  `harga_satuan_terima` int DEFAULT NULL,
  `sub_total_terima` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `detail_penerimaan`
--
DELIMITER $$
CREATE TRIGGER `trg_detail_penerimaan_after_insert` AFTER INSERT ON `detail_penerimaan` FOR EACH ROW BEGIN
    DECLARE v_stock_sekarang INT;
    DECLARE v_stock_baru INT;
    
    -- 1. Hitung stock sekarang menggunakan function
    SET v_stock_sekarang = fn_hitung_stock(NEW.idbarang);
    
    -- 2. Hitung stock baru
    SET v_stock_baru = v_stock_sekarang + NEW.jumlah_terima;
    
    -- 3. INSERT INTO kartu_stok
    INSERT INTO kartu_stok (
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    ) VALUES (
        'P',                        -- Penerimaan
        NEW.jumlah_terima,          -- masuk
        0,                          -- keluar
        v_stock_baru,               -- stock baru
        NOW(),                      -- created_at
        NEW.iddetail_penerimaan,    -- idtransaksi
        NEW.idbarang                -- idbarang
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penerimaan_after_update` AFTER UPDATE ON `detail_penerimaan` FOR EACH ROW BEGIN
    DECLARE v_stock_sekarang INT;
    DECLARE v_stock_setelah_tarik INT;
    DECLARE v_stock_baru INT;
    
    -- 1. Hitung stock sekarang
    SET v_stock_sekarang = fn_hitung_stock(NEW.idbarang);
    
    -- 2. STEP 1 - Tarik stock lama (kembalikan ke kondisi awal)
    SET v_stock_setelah_tarik = v_stock_sekarang - OLD.jumlah_terima;
    
    INSERT INTO kartu_stok (
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    ) VALUES (
        'P',                        -- Penerimaan
        0,                          -- masuk
        OLD.jumlah_terima,          -- keluar (tarik stok lama)
        v_stock_setelah_tarik,      -- stock setelah ditarik
        NOW(),                      -- created_at
        NEW.iddetail_penerimaan,    -- idtransaksi
        NEW.idbarang                -- idbarang
    );
    
    -- 3. STEP 2 - Tambah stock baru
    SET v_stock_baru = v_stock_setelah_tarik + NEW.jumlah_terima;
    
    INSERT INTO kartu_stok (
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    ) VALUES (
        'P',                        -- Penerimaan
        NEW.jumlah_terima,          -- masuk (stok baru)
        0,                          -- keluar
        v_stock_baru,               -- stock baru
        NOW(),                      -- created_at
        NEW.iddetail_penerimaan,    -- idtransaksi
        NEW.idbarang                -- idbarang
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penerimaan_before_delete` BEFORE DELETE ON `detail_penerimaan` FOR EACH ROW BEGIN
    DECLARE v_stock_sekarang INT;
    DECLARE v_stock_baru INT;
    
    -- 1. Hitung stock sekarang
    SET v_stock_sekarang = fn_hitung_stock(OLD.idbarang);
    
    -- 2. Hitung stock setelah delete
    SET v_stock_baru = v_stock_sekarang - OLD.jumlah_terima;
    
    -- 3. INSERT INTO kartu_stok (keluar)
    INSERT INTO kartu_stok (
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    ) VALUES (
        'P',                        -- Penerimaan
        0,                          -- masuk
        OLD.jumlah_terima,          -- keluar
        v_stock_baru,               -- stock berkurang
        NOW(),                      -- created_at
        OLD.iddetail_penerimaan,    -- idtransaksi
        OLD.idbarang                -- idbarang
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penerimaan_before_insert` BEFORE INSERT ON `detail_penerimaan` FOR EACH ROW BEGIN
    DECLARE v_harga INT;
    DECLARE v_ppn INT;
    
    -- 1. Ambil harga dari tabel barang
    SELECT harga INTO v_harga
    FROM barang
    WHERE idbarang = NEW.idbarang;
    
    -- 2. Set harga_satuan_terima
    SET NEW.harga_satuan_terima = v_harga;
    
    -- 3. Ambil ppn dari tabel pengadaan via idpenerimaan
    SELECT p.ppn INTO v_ppn
    FROM penerimaan pen
    JOIN pengadaan p ON pen.idpengadaan = p.idpengadaan
    WHERE pen.idpenerimaan = NEW.idpenerimaan;
    
    -- 4. Hitung sub_total_terima = jumlah_terima × (harga_satuan_terima + (harga_satuan_terima × (ppn/100)))
    SET NEW.sub_total_terima = NEW.jumlah_terima * 
                               (NEW.harga_satuan_terima + 
                                (NEW.harga_satuan_terima * (v_ppn / 100)));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penerimaan_before_update` BEFORE UPDATE ON `detail_penerimaan` FOR EACH ROW BEGIN
    DECLARE v_harga INT;
    DECLARE v_ppn INT;
    
    -- 1. Ambil harga dari tabel barang
    SELECT harga INTO v_harga
    FROM barang
    WHERE idbarang = NEW.idbarang;
    
    -- 2. Set harga_satuan_terima
    SET NEW.harga_satuan_terima = v_harga;
    
    -- 3. Ambil ppn dari tabel pengadaan via idpenerimaan
    SELECT p.ppn INTO v_ppn
    FROM penerimaan pen
    JOIN pengadaan p ON pen.idpengadaan = p.idpengadaan
    WHERE pen.idpenerimaan = NEW.idpenerimaan;
    
    -- 4. Hitung sub_total_terima
    SET NEW.sub_total_terima = NEW.jumlah_terima * 
                               (NEW.harga_satuan_terima + 
                                (NEW.harga_satuan_terima * (v_ppn / 100)));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penerimaan_check_complete_delete` AFTER DELETE ON `detail_penerimaan` FOR EACH ROW BEGIN
    DECLARE v_idpengadaan INT;
    
    -- Ambil idpengadaan dari penerimaan
    SELECT idpengadaan INTO v_idpengadaan
    FROM penerimaan
    WHERE idpenerimaan = OLD.idpenerimaan;
    
    -- Cek apakah pengadaan masih lengkap atau tidak
    CALL sp_check_pengadaan_complete(v_idpengadaan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penerimaan_check_complete_insert` AFTER INSERT ON `detail_penerimaan` FOR EACH ROW BEGIN
    DECLARE v_idpengadaan INT;
    
    -- Ambil idpengadaan dari penerimaan
    SELECT idpengadaan INTO v_idpengadaan
    FROM penerimaan
    WHERE idpenerimaan = NEW.idpenerimaan;
    
    -- Cek apakah pengadaan sudah lengkap
    CALL sp_check_pengadaan_complete(v_idpengadaan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penerimaan_check_complete_update` AFTER UPDATE ON `detail_penerimaan` FOR EACH ROW BEGIN
    DECLARE v_idpengadaan INT;
    
    -- Ambil idpengadaan dari penerimaan
    SELECT idpengadaan INTO v_idpengadaan
    FROM penerimaan
    WHERE idpenerimaan = NEW.idpenerimaan;
    
    -- Cek apakah pengadaan sudah lengkap
    CALL sp_check_pengadaan_complete(v_idpengadaan);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pengadaan`
--

CREATE TABLE `detail_pengadaan` (
  `iddetail_pengadaan` int NOT NULL,
  `harga_satuan` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sub_total` int DEFAULT NULL,
  `idbarang` int DEFAULT NULL,
  `idpengadaan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `detail_pengadaan`
--
DELIMITER $$
CREATE TRIGGER `trg_detail_pengadaan_after_delete` AFTER DELETE ON `detail_pengadaan` FOR EACH ROW BEGIN
    DECLARE v_result INT;
    
    -- Panggil function untuk hitung total
    SET v_result = fn_hitung_total_pengadaan(OLD.idpengadaan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_pengadaan_after_insert` AFTER INSERT ON `detail_pengadaan` FOR EACH ROW BEGIN
    DECLARE v_result INT;
    
    -- Panggil function untuk hitung total
    SET v_result = fn_hitung_total_pengadaan(NEW.idpengadaan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_pengadaan_after_update` AFTER UPDATE ON `detail_pengadaan` FOR EACH ROW BEGIN
    DECLARE v_result INT;
    
    -- Panggil function untuk hitung total
    SET v_result = fn_hitung_total_pengadaan(NEW.idpengadaan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_pengadaan_before_insert` BEFORE INSERT ON `detail_pengadaan` FOR EACH ROW BEGIN
    DECLARE v_harga INT;
    
    -- 1. Ambil harga dari tabel barang
    SELECT harga INTO v_harga
    FROM barang
    WHERE idbarang = NEW.idbarang;
    
    -- 2. Set harga_satuan
    SET NEW.harga_satuan = v_harga;
    
    -- 3. Hitung sub_total
    SET NEW.sub_total = NEW.jumlah * NEW.harga_satuan;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_pengadaan_before_update` BEFORE UPDATE ON `detail_pengadaan` FOR EACH ROW BEGIN
    DECLARE v_harga INT;
    
    -- 1. Ambil harga dari tabel barang
    SELECT harga INTO v_harga
    FROM barang
    WHERE idbarang = NEW.idbarang;
    
    -- 2. Set harga_satuan
    SET NEW.harga_satuan = v_harga;
    
    -- 3. Hitung sub_total
    SET NEW.sub_total = NEW.jumlah * NEW.harga_satuan;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `iddetail_penjualan` int NOT NULL,
  `harga_satuan` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `subtotal` int DEFAULT NULL,
  `idpenjualan` int DEFAULT NULL,
  `idbarang` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `detail_penjualan`
--
DELIMITER $$
CREATE TRIGGER `trg_detail_penjualan_after_delete` AFTER DELETE ON `detail_penjualan` FOR EACH ROW BEGIN
    DECLARE v_stock_sekarang INT;
    DECLARE v_stock_baru INT;
    DECLARE v_result INT;
    
    -- 1. Hitung stock sekarang
    SET v_stock_sekarang = fn_hitung_stock(OLD.idbarang);
    
    -- 2. Kembalikan stok (masuk kembali)
    SET v_stock_baru = v_stock_sekarang + OLD.jumlah;
    
    -- 3. INSERT INTO kartu_stok
    INSERT INTO kartu_stok (
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    ) VALUES (
        'J',                        -- Jenis Penjualan
        OLD.jumlah,                 -- masuk (kembalikan)
        0,                          -- keluar
        v_stock_baru,               -- stock bertambah
        NOW(),                      -- created_at
        OLD.iddetail_penjualan,     -- idtransaksi
        OLD.idbarang                -- idbarang
    );
    
    -- 4. Update total penjualan
    SET v_result = fn_hitung_total_penjualan(OLD.idpenjualan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penjualan_after_insert` AFTER INSERT ON `detail_penjualan` FOR EACH ROW BEGIN
    DECLARE v_stock_sekarang INT;
    DECLARE v_stock_baru INT;
    DECLARE v_result INT;
    
    -- 1. Hitung stock sekarang
    SET v_stock_sekarang = fn_hitung_stock(NEW.idbarang);
    
    -- 2. Hitung stock baru (berkurang karena keluar)
    SET v_stock_baru = v_stock_sekarang - NEW.jumlah;
    
    -- 3. INSERT INTO kartu_stok
    INSERT INTO kartu_stok (
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    ) VALUES (
        'J',                        -- Jenis Penjualan
        0,                          -- masuk
        NEW.jumlah,                 -- keluar
        v_stock_baru,               -- stock berkurang
        NOW(),                      -- created_at
        NEW.iddetail_penjualan,     -- idtransaksi
        NEW.idbarang                -- idbarang
    );
    
    -- 4. Update total penjualan
    SET v_result = fn_hitung_total_penjualan(NEW.idpenjualan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penjualan_after_update` AFTER UPDATE ON `detail_penjualan` FOR EACH ROW BEGIN
    DECLARE v_stock_sekarang INT;
    DECLARE v_stock_setelah_kembalikan INT;
    DECLARE v_stock_baru INT;
    DECLARE v_result INT;
    
    -- 1. Hitung stock sekarang
    SET v_stock_sekarang = fn_hitung_stock(NEW.idbarang);
    
    -- 2. STEP 1 - Kembalikan stok lama (masuk kembali)
    SET v_stock_setelah_kembalikan = v_stock_sekarang + OLD.jumlah;
    
    INSERT INTO kartu_stok (
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    ) VALUES (
        'J',                        -- Jenis Penjualan
        OLD.jumlah,                 -- masuk (kembalikan)
        0,                          -- keluar
        v_stock_setelah_kembalikan, -- stock bertambah
        NOW(),                      -- created_at
        NEW.iddetail_penjualan,     -- idtransaksi
        NEW.idbarang                -- idbarang
    );
    
    -- 3. STEP 2 - Kurangi stok baru (keluar lagi)
    SET v_stock_baru = v_stock_setelah_kembalikan - NEW.jumlah;
    
    INSERT INTO kartu_stok (
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    ) VALUES (
        'J',                        -- Jenis Penjualan
        0,                          -- masuk
        NEW.jumlah,                 -- keluar (stok baru)
        v_stock_baru,               -- stock berkurang lagi
        NOW(),                      -- created_at
        NEW.iddetail_penjualan,     -- idtransaksi
        NEW.idbarang                -- idbarang
    );
    
    -- 4. Update total penjualan
    SET v_result = fn_hitung_total_penjualan(NEW.idpenjualan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penjualan_before_insert` BEFORE INSERT ON `detail_penjualan` FOR EACH ROW BEGIN
    DECLARE v_harga_barang INT;
    
    -- 1. Ambil harga dari tabel barang (tanpa margin)
    SELECT harga INTO v_harga_barang
    FROM barang
    WHERE idbarang = NEW.idbarang;
    
    -- 2. Set harga_satuan = harga barang saja
    SET NEW.harga_satuan = v_harga_barang;
    
    -- 3. Hitung subtotal = harga_satuan * jumlah
    SET NEW.subtotal = NEW.harga_satuan * NEW.jumlah;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_penjualan_before_update` BEFORE UPDATE ON `detail_penjualan` FOR EACH ROW BEGIN
    DECLARE v_harga_barang INT;
    
    -- 1. Ambil harga dari tabel barang
    SELECT harga INTO v_harga_barang
    FROM barang
    WHERE idbarang = NEW.idbarang;
    
    -- 2. Set harga_satuan = harga barang saja
    SET NEW.harga_satuan = v_harga_barang;
    
    -- 3. Hitung subtotal
    SET NEW.subtotal = NEW.harga_satuan * NEW.jumlah;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_retur`
--

CREATE TABLE `detail_retur` (
  `iddetail_retur` int NOT NULL,
  `jumlah` int DEFAULT NULL,
  `alasan` varchar(200) DEFAULT NULL,
  `idretur` int DEFAULT NULL,
  `iddetail_penerimaan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kartu_stok`
--

CREATE TABLE `kartu_stok` (
  `idkartu_stok` int NOT NULL,
  `jenis_transaksi` char(1) DEFAULT NULL,
  `masuk` int DEFAULT NULL,
  `keluar` int DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `idtransaksi` int DEFAULT NULL,
  `idbarang` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `margin_penjualan`
--

CREATE TABLE `margin_penjualan` (
  `idmargin_penjualan` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `persen` double DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `iduser` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `margin_penjualan`
--

INSERT INTO `margin_penjualan` (`idmargin_penjualan`, `created_at`, `persen`, `status`, `iduser`, `updated_at`) VALUES
(1, '2025-10-01 08:00:00', 15.5, 0, 1, '2025-10-01 08:00:00'),
(2, '2025-10-01 09:30:00', 20, 0, 2, '2025-12-01 11:34:24'),
(3, '2025-10-01 11:15:00', 12.75, 0, 3, '2025-10-01 11:15:00'),
(4, '2025-10-01 14:20:00', 25, 0, 4, '2025-10-01 14:20:00'),
(6, '2025-12-01 11:34:35', 11, 1, 1, '2025-12-01 11:37:19');

-- --------------------------------------------------------

--
-- Table structure for table `penerimaan`
--

CREATE TABLE `penerimaan` (
  `idpenerimaan` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` char(1) DEFAULT NULL,
  `idpengadaan` int DEFAULT NULL,
  `iduser` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengadaan`
--

CREATE TABLE `pengadaan` (
  `idpengadaan` int NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_iduser` int DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `vendor_idvendor` int DEFAULT NULL,
  `subtotal_nilai` int DEFAULT NULL,
  `ppn` int DEFAULT NULL,
  `total_nilai` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `pengadaan`
--
DELIMITER $$
CREATE TRIGGER `trg_pengadaan_delete_cascade` BEFORE DELETE ON `pengadaan` FOR EACH ROW BEGIN
    -- Hapus semua detail_pengadaan terkait
    DELETE FROM detail_pengadaan
    WHERE idpengadaan = OLD.idpengadaan;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_pengadaan_update_ppn` BEFORE UPDATE ON `pengadaan` FOR EACH ROW BEGIN
    -- Jika ppn berubah, hitung ulang total_nilai
    IF OLD.ppn != NEW.ppn THEN
        SET NEW.total_nilai = NEW.subtotal_nilai + (NEW.subtotal_nilai * (NEW.ppn / 100));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `idpenjualan` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `subtotal_nilai` int DEFAULT NULL,
  `ppn` int DEFAULT NULL,
  `total_nilai` int DEFAULT NULL,
  `iduser` int DEFAULT NULL,
  `idmargin_penjualan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `penjualan`
--
DELIMITER $$
CREATE TRIGGER `trg_penjualan_before_insert` BEFORE INSERT ON `penjualan` FOR EACH ROW BEGIN
    -- Set nilai awal = 0 (akan ter-update dari detail_penjualan)
    SET NEW.subtotal_nilai = 0;
    SET NEW.total_nilai = 0;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_penjualan_update_ppn` BEFORE UPDATE ON `penjualan` FOR EACH ROW BEGIN
    DECLARE v_margin_persen DOUBLE;
    DECLARE v_margin_nilai INT;
    DECLARE v_ppn_nilai INT;
    
    -- Jika ppn atau margin berubah, hitung ulang total_nilai
    IF OLD.ppn != NEW.ppn OR OLD.idmargin_penjualan != NEW.idmargin_penjualan THEN
        -- Ambil margin persen
        SELECT persen INTO v_margin_persen
        FROM margin_penjualan
        WHERE idmargin_penjualan = NEW.idmargin_penjualan;
        
        -- Hitung margin_nilai
        SET v_margin_nilai = NEW.subtotal_nilai * (v_margin_persen / 100);
        
        -- Hitung ppn_nilai
        SET v_ppn_nilai = NEW.subtotal_nilai * (NEW.ppn / 100);
        
        -- Hitung total_nilai
        SET NEW.total_nilai = NEW.subtotal_nilai + v_margin_nilai + v_ppn_nilai;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `retur`
--

CREATE TABLE `retur` (
  `idretur` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `idpenerimaan` int DEFAULT NULL,
  `iduser` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `idrole` int NOT NULL,
  `nama_role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`idrole`, `nama_role`) VALUES
(1, 'Admin'),
(2, 'Super Admin'),
(3, 'Kasir'),
(4, 'Manajer'),
(5, 'Direktur'),
(6, 'Cheater');

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `idsatuan` int NOT NULL,
  `nama_satuan` varchar(45) NOT NULL,
  `status` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`idsatuan`, `nama_satuan`, `status`) VALUES
(1, 'Kilogram', 1),
(2, 'Liter', 1),
(3, 'Pack', 1),
(4, 'Dus', 1),
(6, 'Sak', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `iduser` int NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(100) NOT NULL,
  `idrole` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`iduser`, `username`, `password`, `idrole`) VALUES
(1, 'admin1', 'pass123', 1),
(2, 'SuperAdmin1', 'pass123', 2),
(3, 'kasir1', 'pass123', 3),
(4, 'manager1', 'pass123', 4),
(5, 'direktur1', 'pass123', 5);

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `idvendor` int NOT NULL,
  `nama_vendor` varchar(100) NOT NULL,
  `badan_hukum` char(1) DEFAULT NULL,
  `status` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`idvendor`, `nama_vendor`, `badan_hukum`, `status`) VALUES
(1, 'PT Sumber Rejeki', 'Y', 'A'),
(2, 'CV Maju Jaya', 'N', 'A'),
(3, 'PT Agro Mandiri', 'Y', 'A'),
(4, 'UD Tani Makmur', 'N', 'A'),
(5, 'PT Global Food', 'Y', 'A');

--
-- Triggers `vendor`
--
DELIMITER $$
CREATE TRIGGER `trg_vendor_nonaktif` AFTER UPDATE ON `vendor` FOR EACH ROW BEGIN
    -- Jika vendor berubah dari aktif ke non-aktif
    IF OLD.status = 'A' AND NEW.status != 'A' THEN
        -- Non-aktifkan semua pengadaan dengan vendor ini
        UPDATE pengadaan
        SET status = 'N'
        WHERE vendor_idvendor = NEW.idvendor;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_barang`
-- (See below for the actual view)
--
CREATE TABLE `view_barang` (
`harga` int
,`idbarang` int
,`jenis` char(1)
,`nama` varchar(45)
,`nama_satuan` varchar(45)
,`status` tinyint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_barang_all`
-- (See below for the actual view)
--
CREATE TABLE `view_barang_all` (
`harga` int
,`idbarang` int
,`jenis` char(1)
,`nama` varchar(45)
,`nama_satuan` varchar(45)
,`status` tinyint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_detail_penerimaan`
-- (See below for the actual view)
--
CREATE TABLE `view_detail_penerimaan` (
`harga_satuan_terima` int
,`iddetail_penerimaan` int
,`idpenerimaan` int
,`jumlah_terima` int
,`nama_barang` varchar(45)
,`sub_total_terima` int
,`tanggal_penerimaan` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_detail_pengadaan`
-- (See below for the actual view)
--
CREATE TABLE `view_detail_pengadaan` (
`harga_satuan` int
,`iddetail_pengadaan` int
,`idpengadaan` int
,`jumlah` int
,`nama_barang` varchar(45)
,`nama_vendor` varchar(100)
,`sub_total` int
,`tanggal_pengadaan` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_detail_penjualan`
-- (See below for the actual view)
--
CREATE TABLE `view_detail_penjualan` (
`harga_satuan` int
,`iddetail_penjualan` int
,`idpenjualan` int
,`jumlah` int
,`kasir` varchar(45)
,`nama_barang` varchar(45)
,`subtotal` int
,`tanggal_penjualan` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_detail_retur`
-- (See below for the actual view)
--
CREATE TABLE `view_detail_retur` (
`alasan` varchar(200)
,`iddetail_retur` int
,`idretur` int
,`jumlah` int
,`nama_barang` varchar(45)
,`tanggal_penerimaan` timestamp
,`tanggal_retur` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_kartu_stok`
-- (See below for the actual view)
--
CREATE TABLE `view_kartu_stok` (
`created_at` timestamp
,`idkartu_stok` int
,`idtransaksi` int
,`jenis_transaksi` char(1)
,`keluar` int
,`masuk` int
,`nama_barang` varchar(45)
,`stock` int
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_margin_penjualan`
-- (See below for the actual view)
--
CREATE TABLE `view_margin_penjualan` (
`created_at` timestamp
,`idmargin_penjualan` int
,`persen` double
,`status` tinyint
,`updated_at` timestamp
,`username` varchar(45)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_margin_penjualan_all`
-- (See below for the actual view)
--
CREATE TABLE `view_margin_penjualan_all` (
`created_at` timestamp
,`idmargin_penjualan` int
,`persen` double
,`status` tinyint
,`updated_at` timestamp
,`username` varchar(45)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_penerimaan`
-- (See below for the actual view)
--
CREATE TABLE `view_penerimaan` (
`created_at` timestamp
,`idpenerimaan` int
,`idpengadaan` int
,`nama_vendor` varchar(100)
,`penerima` varchar(45)
,`status` char(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pengadaan`
-- (See below for the actual view)
--
CREATE TABLE `view_pengadaan` (
`idpengadaan` int
,`nama_vendor` varchar(100)
,`pembuat` varchar(45)
,`ppn` int
,`status` char(1)
,`subtotal_nilai` int
,`timestamp` timestamp
,`total_nilai` int
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pengadaan_all`
-- (See below for the actual view)
--
CREATE TABLE `view_pengadaan_all` (
`idpengadaan` int
,`nama_vendor` varchar(100)
,`pembuat` varchar(45)
,`ppn` int
,`status` char(1)
,`subtotal_nilai` int
,`timestamp` timestamp
,`total_nilai` int
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_penjualan`
-- (See below for the actual view)
--
CREATE TABLE `view_penjualan` (
`created_at` timestamp
,`idpenjualan` int
,`kasir` varchar(45)
,`margin_persen` double
,`ppn` int
,`subtotal_nilai` int
,`total_nilai` int
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_retur`
-- (See below for the actual view)
--
CREATE TABLE `view_retur` (
`idpenerimaan` int
,`idretur` int
,`pengembali` varchar(45)
,`tanggal_penerimaan` timestamp
,`tanggal_retur` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_role`
-- (See below for the actual view)
--
CREATE TABLE `view_role` (
`idrole` int
,`nama_role` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_satuan`
-- (See below for the actual view)
--
CREATE TABLE `view_satuan` (
`idsatuan` int
,`nama_satuan` varchar(45)
,`status` tinyint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_satuan_all`
-- (See below for the actual view)
--
CREATE TABLE `view_satuan_all` (
`idsatuan` int
,`nama_satuan` varchar(45)
,`status` tinyint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_sisa_penerimaan`
-- (See below for the actual view)
--
CREATE TABLE `view_sisa_penerimaan` (
`idbarang` int
,`idpengadaan` int
,`jumlah_pengadaan` int
,`jumlah_terima` decimal(32,0)
,`nama_barang` varchar(45)
,`sisa_penerimaan` decimal(33,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_status_penerimaan`
-- (See below for the actual view)
--
CREATE TABLE `view_status_penerimaan` (
`idbarang` int
,`idpengadaan` int
,`jumlah_pengadaan` int
,`jumlah_terima` decimal(32,0)
,`nama_barang` varchar(45)
,`sisa_penerimaan` decimal(33,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_user`
-- (See below for the actual view)
--
CREATE TABLE `view_user` (
`iduser` int
,`nama_role` varchar(100)
,`username` varchar(45)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_vendor`
-- (See below for the actual view)
--
CREATE TABLE `view_vendor` (
`badan_hukum` char(1)
,`idvendor` int
,`nama_vendor` varchar(100)
,`status` char(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_vendor_all`
-- (See below for the actual view)
--
CREATE TABLE `view_vendor_all` (
`badan_hukum` char(1)
,`idvendor` int
,`nama_vendor` varchar(100)
,`status` char(1)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`idbarang`),
  ADD KEY `idsatuan` (`idsatuan`);

--
-- Indexes for table `detail_penerimaan`
--
ALTER TABLE `detail_penerimaan`
  ADD PRIMARY KEY (`iddetail_penerimaan`),
  ADD KEY `idpenerimaan` (`idpenerimaan`),
  ADD KEY `idbarang` (`idbarang`);

--
-- Indexes for table `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  ADD PRIMARY KEY (`iddetail_pengadaan`),
  ADD KEY `idbarang` (`idbarang`),
  ADD KEY `idpengadaan` (`idpengadaan`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`iddetail_penjualan`),
  ADD KEY `idpenjualan` (`idpenjualan`),
  ADD KEY `idbarang` (`idbarang`);

--
-- Indexes for table `detail_retur`
--
ALTER TABLE `detail_retur`
  ADD PRIMARY KEY (`iddetail_retur`),
  ADD KEY `idretur` (`idretur`),
  ADD KEY `iddetail_penerimaan` (`iddetail_penerimaan`);

--
-- Indexes for table `kartu_stok`
--
ALTER TABLE `kartu_stok`
  ADD PRIMARY KEY (`idkartu_stok`),
  ADD KEY `idbarang` (`idbarang`);

--
-- Indexes for table `margin_penjualan`
--
ALTER TABLE `margin_penjualan`
  ADD PRIMARY KEY (`idmargin_penjualan`),
  ADD KEY `iduser` (`iduser`);

--
-- Indexes for table `penerimaan`
--
ALTER TABLE `penerimaan`
  ADD PRIMARY KEY (`idpenerimaan`),
  ADD KEY `idpengadaan` (`idpengadaan`),
  ADD KEY `iduser` (`iduser`);

--
-- Indexes for table `pengadaan`
--
ALTER TABLE `pengadaan`
  ADD PRIMARY KEY (`idpengadaan`),
  ADD KEY `user_iduser` (`user_iduser`),
  ADD KEY `vendor_idvendor` (`vendor_idvendor`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`idpenjualan`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idmargin_penjualan` (`idmargin_penjualan`);

--
-- Indexes for table `retur`
--
ALTER TABLE `retur`
  ADD PRIMARY KEY (`idretur`),
  ADD KEY `idpenerimaan` (`idpenerimaan`),
  ADD KEY `iduser` (`iduser`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`idrole`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`idsatuan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`),
  ADD KEY `idrole` (`idrole`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`idvendor`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `idbarang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_penerimaan`
--
ALTER TABLE `detail_penerimaan`
  MODIFY `iddetail_penerimaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  MODIFY `iddetail_pengadaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `iddetail_penjualan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detail_retur`
--
ALTER TABLE `detail_retur`
  MODIFY `iddetail_retur` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kartu_stok`
--
ALTER TABLE `kartu_stok`
  MODIFY `idkartu_stok` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `margin_penjualan`
--
ALTER TABLE `margin_penjualan`
  MODIFY `idmargin_penjualan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `penerimaan`
--
ALTER TABLE `penerimaan`
  MODIFY `idpenerimaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengadaan`
--
ALTER TABLE `pengadaan`
  MODIFY `idpengadaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `idpenjualan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `retur`
--
ALTER TABLE `retur`
  MODIFY `idretur` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `idrole` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `idsatuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `idvendor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- --------------------------------------------------------

--
-- Structure for view `view_barang`
--
DROP TABLE IF EXISTS `view_barang`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_barang`  AS SELECT `b`.`idbarang` AS `idbarang`, `b`.`jenis` AS `jenis`, `b`.`nama` AS `nama`, `s`.`nama_satuan` AS `nama_satuan`, `b`.`status` AS `status`, `b`.`harga` AS `harga` FROM (`barang` `b` join `satuan` `s` on((`b`.`idsatuan` = `s`.`idsatuan`))) WHERE (`b`.`status` = 1) ;

-- --------------------------------------------------------

--
-- Structure for view `view_barang_all`
--
DROP TABLE IF EXISTS `view_barang_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_barang_all`  AS SELECT `b`.`idbarang` AS `idbarang`, `b`.`jenis` AS `jenis`, `b`.`nama` AS `nama`, `s`.`nama_satuan` AS `nama_satuan`, `b`.`status` AS `status`, `b`.`harga` AS `harga` FROM (`barang` `b` join `satuan` `s` on((`b`.`idsatuan` = `s`.`idsatuan`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_detail_penerimaan`
--
DROP TABLE IF EXISTS `view_detail_penerimaan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_detail_penerimaan`  AS SELECT `dp`.`iddetail_penerimaan` AS `iddetail_penerimaan`, `dp`.`idpenerimaan` AS `idpenerimaan`, `b`.`nama` AS `nama_barang`, `dp`.`jumlah_terima` AS `jumlah_terima`, `dp`.`harga_satuan_terima` AS `harga_satuan_terima`, `dp`.`sub_total_terima` AS `sub_total_terima`, `p`.`created_at` AS `tanggal_penerimaan` FROM ((`detail_penerimaan` `dp` join `barang` `b` on((`dp`.`idbarang` = `b`.`idbarang`))) join `penerimaan` `p` on((`dp`.`idpenerimaan` = `p`.`idpenerimaan`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_detail_pengadaan`
--
DROP TABLE IF EXISTS `view_detail_pengadaan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_detail_pengadaan`  AS SELECT `dp`.`iddetail_pengadaan` AS `iddetail_pengadaan`, `dp`.`idpengadaan` AS `idpengadaan`, `b`.`nama` AS `nama_barang`, `dp`.`harga_satuan` AS `harga_satuan`, `dp`.`jumlah` AS `jumlah`, `dp`.`sub_total` AS `sub_total`, `v`.`nama_vendor` AS `nama_vendor`, `p`.`timestamp` AS `tanggal_pengadaan` FROM (((`detail_pengadaan` `dp` join `barang` `b` on((`dp`.`idbarang` = `b`.`idbarang`))) join `pengadaan` `p` on((`dp`.`idpengadaan` = `p`.`idpengadaan`))) join `vendor` `v` on((`p`.`vendor_idvendor` = `v`.`idvendor`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_detail_penjualan`
--
DROP TABLE IF EXISTS `view_detail_penjualan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_detail_penjualan`  AS SELECT `dp`.`iddetail_penjualan` AS `iddetail_penjualan`, `dp`.`idpenjualan` AS `idpenjualan`, `b`.`nama` AS `nama_barang`, `dp`.`harga_satuan` AS `harga_satuan`, `dp`.`jumlah` AS `jumlah`, `dp`.`subtotal` AS `subtotal`, `p`.`created_at` AS `tanggal_penjualan`, `u`.`username` AS `kasir` FROM (((`detail_penjualan` `dp` join `barang` `b` on((`dp`.`idbarang` = `b`.`idbarang`))) join `penjualan` `p` on((`dp`.`idpenjualan` = `p`.`idpenjualan`))) join `user` `u` on((`p`.`iduser` = `u`.`iduser`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_detail_retur`
--
DROP TABLE IF EXISTS `view_detail_retur`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_detail_retur`  AS SELECT `dr`.`iddetail_retur` AS `iddetail_retur`, `dr`.`idretur` AS `idretur`, `b`.`nama` AS `nama_barang`, `dr`.`jumlah` AS `jumlah`, `dr`.`alasan` AS `alasan`, `p`.`created_at` AS `tanggal_penerimaan`, `r`.`created_at` AS `tanggal_retur` FROM ((((`detail_retur` `dr` join `detail_penerimaan` `dp` on((`dr`.`iddetail_penerimaan` = `dp`.`iddetail_penerimaan`))) join `barang` `b` on((`dp`.`idbarang` = `b`.`idbarang`))) join `penerimaan` `p` on((`dp`.`idpenerimaan` = `p`.`idpenerimaan`))) join `retur` `r` on((`dr`.`idretur` = `r`.`idretur`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_kartu_stok`
--
DROP TABLE IF EXISTS `view_kartu_stok`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_kartu_stok`  AS SELECT `ks`.`idkartu_stok` AS `idkartu_stok`, `b`.`nama` AS `nama_barang`, `ks`.`jenis_transaksi` AS `jenis_transaksi`, `ks`.`masuk` AS `masuk`, `ks`.`keluar` AS `keluar`, `ks`.`stock` AS `stock`, `ks`.`created_at` AS `created_at`, `ks`.`idtransaksi` AS `idtransaksi` FROM (`kartu_stok` `ks` join `barang` `b` on((`ks`.`idbarang` = `b`.`idbarang`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_margin_penjualan`
--
DROP TABLE IF EXISTS `view_margin_penjualan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_margin_penjualan`  AS SELECT `mp`.`idmargin_penjualan` AS `idmargin_penjualan`, `mp`.`persen` AS `persen`, `mp`.`status` AS `status`, `mp`.`created_at` AS `created_at`, `mp`.`updated_at` AS `updated_at`, `u`.`username` AS `username` FROM (`margin_penjualan` `mp` join `user` `u` on((`mp`.`iduser` = `u`.`iduser`))) WHERE (`mp`.`status` = 1) ;

-- --------------------------------------------------------

--
-- Structure for view `view_margin_penjualan_all`
--
DROP TABLE IF EXISTS `view_margin_penjualan_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_margin_penjualan_all`  AS SELECT `mp`.`idmargin_penjualan` AS `idmargin_penjualan`, `mp`.`persen` AS `persen`, `mp`.`status` AS `status`, `mp`.`created_at` AS `created_at`, `mp`.`updated_at` AS `updated_at`, `u`.`username` AS `username` FROM (`margin_penjualan` `mp` join `user` `u` on((`mp`.`iduser` = `u`.`iduser`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_penerimaan`
--
DROP TABLE IF EXISTS `view_penerimaan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_penerimaan`  AS SELECT `p`.`idpenerimaan` AS `idpenerimaan`, `p`.`created_at` AS `created_at`, `p`.`status` AS `status`, `pg`.`idpengadaan` AS `idpengadaan`, `v`.`nama_vendor` AS `nama_vendor`, `u`.`username` AS `penerima` FROM (((`penerimaan` `p` join `pengadaan` `pg` on((`p`.`idpengadaan` = `pg`.`idpengadaan`))) join `vendor` `v` on((`pg`.`vendor_idvendor` = `v`.`idvendor`))) join `user` `u` on((`p`.`iduser` = `u`.`iduser`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_pengadaan`
--
DROP TABLE IF EXISTS `view_pengadaan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pengadaan`  AS SELECT `p`.`idpengadaan` AS `idpengadaan`, `p`.`timestamp` AS `timestamp`, `p`.`subtotal_nilai` AS `subtotal_nilai`, `p`.`ppn` AS `ppn`, `p`.`total_nilai` AS `total_nilai`, `p`.`status` AS `status`, `v`.`nama_vendor` AS `nama_vendor`, `u`.`username` AS `pembuat` FROM ((`pengadaan` `p` join `vendor` `v` on((`p`.`vendor_idvendor` = `v`.`idvendor`))) join `user` `u` on((`p`.`user_iduser` = `u`.`iduser`))) WHERE (`p`.`status` = 'A') ;

-- --------------------------------------------------------

--
-- Structure for view `view_pengadaan_all`
--
DROP TABLE IF EXISTS `view_pengadaan_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pengadaan_all`  AS SELECT `p`.`idpengadaan` AS `idpengadaan`, `p`.`timestamp` AS `timestamp`, `p`.`subtotal_nilai` AS `subtotal_nilai`, `p`.`ppn` AS `ppn`, `p`.`total_nilai` AS `total_nilai`, `p`.`status` AS `status`, `v`.`nama_vendor` AS `nama_vendor`, `u`.`username` AS `pembuat` FROM ((`pengadaan` `p` join `vendor` `v` on((`p`.`vendor_idvendor` = `v`.`idvendor`))) join `user` `u` on((`p`.`user_iduser` = `u`.`iduser`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_penjualan`
--
DROP TABLE IF EXISTS `view_penjualan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_penjualan`  AS SELECT `p`.`idpenjualan` AS `idpenjualan`, `p`.`created_at` AS `created_at`, `p`.`subtotal_nilai` AS `subtotal_nilai`, `p`.`ppn` AS `ppn`, `p`.`total_nilai` AS `total_nilai`, `u`.`username` AS `kasir`, `mp`.`persen` AS `margin_persen` FROM ((`penjualan` `p` join `user` `u` on((`p`.`iduser` = `u`.`iduser`))) left join `margin_penjualan` `mp` on((`p`.`idmargin_penjualan` = `mp`.`idmargin_penjualan`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_retur`
--
DROP TABLE IF EXISTS `view_retur`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_retur`  AS SELECT `r`.`idretur` AS `idretur`, `r`.`created_at` AS `tanggal_retur`, `p`.`idpenerimaan` AS `idpenerimaan`, `p`.`created_at` AS `tanggal_penerimaan`, `u`.`username` AS `pengembali` FROM ((`retur` `r` join `penerimaan` `p` on((`r`.`idpenerimaan` = `p`.`idpenerimaan`))) join `user` `u` on((`r`.`iduser` = `u`.`iduser`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_role`
--
DROP TABLE IF EXISTS `view_role`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_role`  AS SELECT `role`.`idrole` AS `idrole`, `role`.`nama_role` AS `nama_role` FROM `role` ;

-- --------------------------------------------------------

--
-- Structure for view `view_satuan`
--
DROP TABLE IF EXISTS `view_satuan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_satuan`  AS SELECT `satuan`.`idsatuan` AS `idsatuan`, `satuan`.`nama_satuan` AS `nama_satuan`, `satuan`.`status` AS `status` FROM `satuan` WHERE (`satuan`.`status` = 1) ;

-- --------------------------------------------------------

--
-- Structure for view `view_satuan_all`
--
DROP TABLE IF EXISTS `view_satuan_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_satuan_all`  AS SELECT `satuan`.`idsatuan` AS `idsatuan`, `satuan`.`nama_satuan` AS `nama_satuan`, `satuan`.`status` AS `status` FROM `satuan` ;

-- --------------------------------------------------------

--
-- Structure for view `view_sisa_penerimaan`
--
DROP TABLE IF EXISTS `view_sisa_penerimaan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_sisa_penerimaan`  AS SELECT `view_status_penerimaan`.`idpengadaan` AS `idpengadaan`, `view_status_penerimaan`.`idbarang` AS `idbarang`, `view_status_penerimaan`.`nama_barang` AS `nama_barang`, `view_status_penerimaan`.`jumlah_pengadaan` AS `jumlah_pengadaan`, `view_status_penerimaan`.`jumlah_terima` AS `jumlah_terima`, `view_status_penerimaan`.`sisa_penerimaan` AS `sisa_penerimaan` FROM `view_status_penerimaan` WHERE (`view_status_penerimaan`.`sisa_penerimaan` > 0) ;

-- --------------------------------------------------------

--
-- Structure for view `view_status_penerimaan`
--
DROP TABLE IF EXISTS `view_status_penerimaan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_status_penerimaan`  AS SELECT `dp`.`idpengadaan` AS `idpengadaan`, `dp`.`idbarang` AS `idbarang`, `b`.`nama` AS `nama_barang`, `dp`.`jumlah` AS `jumlah_pengadaan`, coalesce(`ps`.`total_terima`,0) AS `jumlah_terima`, (`dp`.`jumlah` - coalesce(`ps`.`total_terima`,0)) AS `sisa_penerimaan` FROM ((`detail_pengadaan` `dp` join `barang` `b` on((`dp`.`idbarang` = `b`.`idbarang`))) left join (select `pen`.`idpengadaan` AS `idpengadaan`,`dpen`.`idbarang` AS `idbarang`,sum(`dpen`.`jumlah_terima`) AS `total_terima` from (`detail_penerimaan` `dpen` join `penerimaan` `pen` on((`dpen`.`idpenerimaan` = `pen`.`idpenerimaan`))) group by `pen`.`idpengadaan`,`dpen`.`idbarang`) `ps` on(((`dp`.`idpengadaan` = `ps`.`idpengadaan`) and (`dp`.`idbarang` = `ps`.`idbarang`)))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_user`
--
DROP TABLE IF EXISTS `view_user`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_user`  AS SELECT `u`.`iduser` AS `iduser`, `u`.`username` AS `username`, `r`.`nama_role` AS `nama_role` FROM (`user` `u` join `role` `r` on((`u`.`idrole` = `r`.`idrole`))) ;

-- --------------------------------------------------------

--
-- Structure for view `view_vendor`
--
DROP TABLE IF EXISTS `view_vendor`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_vendor`  AS SELECT `vendor`.`idvendor` AS `idvendor`, `vendor`.`nama_vendor` AS `nama_vendor`, `vendor`.`badan_hukum` AS `badan_hukum`, `vendor`.`status` AS `status` FROM `vendor` WHERE (`vendor`.`status` = 'A') ;

-- --------------------------------------------------------

--
-- Structure for view `view_vendor_all`
--
DROP TABLE IF EXISTS `view_vendor_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_vendor_all`  AS SELECT `vendor`.`idvendor` AS `idvendor`, `vendor`.`nama_vendor` AS `nama_vendor`, `vendor`.`badan_hukum` AS `badan_hukum`, `vendor`.`status` AS `status` FROM `vendor` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`idsatuan`) REFERENCES `satuan` (`idsatuan`);

--
-- Constraints for table `detail_penerimaan`
--
ALTER TABLE `detail_penerimaan`
  ADD CONSTRAINT `detail_penerimaan_ibfk_1` FOREIGN KEY (`idpenerimaan`) REFERENCES `penerimaan` (`idpenerimaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_penerimaan_ibfk_2` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`);

--
-- Constraints for table `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  ADD CONSTRAINT `detail_pengadaan_ibfk_1` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`),
  ADD CONSTRAINT `detail_pengadaan_ibfk_2` FOREIGN KEY (`idpengadaan`) REFERENCES `pengadaan` (`idpengadaan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `detail_penjualan_ibfk_1` FOREIGN KEY (`idpenjualan`) REFERENCES `penjualan` (`idpenjualan`),
  ADD CONSTRAINT `detail_penjualan_ibfk_2` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`);

--
-- Constraints for table `detail_retur`
--
ALTER TABLE `detail_retur`
  ADD CONSTRAINT `detail_retur_ibfk_1` FOREIGN KEY (`idretur`) REFERENCES `retur` (`idretur`),
  ADD CONSTRAINT `detail_retur_ibfk_2` FOREIGN KEY (`iddetail_penerimaan`) REFERENCES `detail_penerimaan` (`iddetail_penerimaan`);

--
-- Constraints for table `kartu_stok`
--
ALTER TABLE `kartu_stok`
  ADD CONSTRAINT `kartu_stok_ibfk_1` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`);

--
-- Constraints for table `margin_penjualan`
--
ALTER TABLE `margin_penjualan`
  ADD CONSTRAINT `margin_penjualan_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`);

--
-- Constraints for table `penerimaan`
--
ALTER TABLE `penerimaan`
  ADD CONSTRAINT `penerimaan_ibfk_1` FOREIGN KEY (`idpengadaan`) REFERENCES `pengadaan` (`idpengadaan`),
  ADD CONSTRAINT `penerimaan_ibfk_2` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`);

--
-- Constraints for table `pengadaan`
--
ALTER TABLE `pengadaan`
  ADD CONSTRAINT `pengadaan_ibfk_1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`),
  ADD CONSTRAINT `pengadaan_ibfk_2` FOREIGN KEY (`vendor_idvendor`) REFERENCES `vendor` (`idvendor`);

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`),
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`idmargin_penjualan`) REFERENCES `margin_penjualan` (`idmargin_penjualan`);

--
-- Constraints for table `retur`
--
ALTER TABLE `retur`
  ADD CONSTRAINT `retur_ibfk_1` FOREIGN KEY (`idpenerimaan`) REFERENCES `penerimaan` (`idpenerimaan`),
  ADD CONSTRAINT `retur_ibfk_2` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`idrole`) REFERENCES `role` (`idrole`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
