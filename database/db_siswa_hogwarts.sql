-- phpMyAdmin SQL Dump
-- version 5.2.0-dev+20220102.e4dfea9a45
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 24, 2023 at 11:18 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_siswa_hogwarts`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_asrama`
--

CREATE TABLE `tbl_asrama` (
  `id_asrama` int NOT NULL,
  `nama_asrama` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `tbl_asrama`
--

INSERT INTO `tbl_asrama` (`id_asrama`, `nama_asrama`, `deskripsi`) VALUES
(1, 'Gryffindor', 'Asrama yang dikenal dengan keberanian dan kejujuran. Warna merah dan emas.'),
(2, 'Hufflepuff', 'Asrama yang menghargai kesetiaan, kerja keras, dan persahabatan. Warna kuning dan hitam.'),
(3, 'Ravenclaw', 'Asrama untuk yang cerdas dan kreatif. Warna biru dan perak.'),
(4, 'Slytherin', 'Asrama yang ambisius dan cerdik. Warna hijau dan perak.');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_siswa`
--

CREATE TABLE `tbl_siswa` (
  `id_siswa` varchar(8) NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `asrama` int NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `whatsapp` varchar(13) NOT NULL,
  `foto_profil` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `tbl_siswa`
--

INSERT INTO `tbl_siswa` (`id_siswa`, `tanggal_daftar`, `asrama`, `nama_lengkap`, `jenis_kelamin`, `alamat`, `email`, `whatsapp`, `foto_profil`) VALUES
('ID-00001', '2023-01-03', 2, 'Indra Styawantoro', 'Laki-laki', 'Tanjung Karang, Kota Bandar Lampung, Lampung', 'indra.styawantoro@gmail.com', '081377783334', 'ecdf17a5b23ce3ede07cb4d34a12a8aa110f9c03.jpg'),
('ID-00002', '2023-01-03', 1, 'Alice Doe', 'Perempuan', 'Tanjung Karang, Kota Bandar Lampung, Lampung', 'alice.doe@gmail.com', '082377883344', '2c2844eb49cb9871c84a2621e0bf28e4a11f4120.png'),
('ID-00003', '2023-01-04', 1, 'Jonathan Smart', 'Laki-laki', 'Kedaton, Kota Bandar Lampung, Lampung', 'jonathan.smart@gmail.com', '082373378448', 'dc9d4273e3e1c581959d31f8619bddaa3cabff3e.png'),
('ID-00004', '2023-01-05', 3, 'Mike Brown', 'Laki-laki', 'Rajabasa, Kota Bandar Lampung, Lampung', 'mike.brown@gmail.com', '082188669988', 'd6a1b86bfb06de5443a48ad26326e2b9cc7688ed.png'),
('ID-00005', '2023-01-05', 1, 'Pauline Smith', 'Perempuan', 'Teluk Betung, Kota Bandar Lampung, Lampung', 'pauline.smith@gmail.com', '085669919779', '135ccbd9b716c92d45b6d20e49efa397784364f4.png'),
('ID-00006', '2023-01-07', 4, 'Ronnie Blake', 'Laki-laki', 'Tanjung Karang, Kota Bandar Lampung, Lampung', 'ronnie.blake@gmail.com', '082173775544', '9738eeba999eb102aac3cd7a189a995f49dbca92.png'),
('ID-00007', '2023-01-07', 1, 'Marsha Singer', 'Perempuan', 'Teluk Betung, Kota Bandar Lampung, Lampung', 'marsha.singer@gmail.com', '085758857778', 'a3afbc61edb3786e26f10a6ef55aed2303f89545.png'),
('ID-00008', '2023-01-09', 2, 'Manver Jacobson', 'Laki-laki', 'Rajabasa, Kota Bandar Lampung, Lampung', 'manver.jacobson@gmail.com', '082189897676', '18def517962c38e1573480704185450412243968.jpg'),
('ID-00009', '2023-01-09', 1, 'Lindsay Spice', 'Perempuan', 'Kedaton, Kota Bandar Lampung, Lampung', 'lindsay.spice@gmail.com', '0895881122441', '73a7e8ac5e8a8f8e90e0976fa5e8683da5a6de42.png'),
('ID-00010', '2023-01-09', 2, 'Lynda Marquez', 'Perempuan', 'Tanjung Karang, Kota Bandar Lampung, Lampung', 'lynda.marquez@gmail.com', '0898557766889', 'c035c0d4bf502d79ea529255b32317d9ae73a81d.png'),
('ID-00011', '2023-01-10', 1, 'James Doe', 'Laki-laki', 'Rajabasa, Kota Bandar Lampung, Lampung', 'james.doe@gmail.com', '082380905643', 'ed090dd94092aa2d8e84ac9107a9b3c051c4bb58.png'),
('ID-00012', '2023-01-11', 2, 'Mark Parker', 'Laki-laki', 'Kedaton, Kota Bandar Lampung, Lampung', 'mark.parker@gmail.com', '082123459876', 'c2321cc7e7f4a2ad53f5dd60a20e11ac0353dc82.png'),
('ID-00013', '2023-01-11', 2, 'Frank Gibson', 'Laki-laki', 'Kemiling, Kota Bandar Lampung, Lampung', 'frank.gibson@gmail.com', '081379793535', 'f242257b1856c9e60db1d472abc60ed256e28448.png'),
('ID-00014', '2023-01-13', 2, 'Ashlyn Jordan', 'Perempuan', 'Langkapura, Kota Bandar Lampung, Lampung', 'ashlyn.jordan@gmail.com', '081381195335', '9f2e492ced301b587b9ea1fd13d3cac7bde55937.jpg'),
('ID-00015', '2023-01-17', 2, 'Patric Green', 'Laki-laki', 'Way Halim, Kota Bandar Lampung, Lampung', 'patric.green@gmail.com', '081366782234', '929c37dc1fcec95ffb025e0df0c969b925033c26.png'),
('ID-00016', '2023-01-25', 3, 'Jeffery Riley', 'Laki-laki', 'Labuhan Ratu, Kota Bandar Lampung, Lampung', 'jeffery.riley@gmail.com', '081376891324', 'cc18551bb2dae2040bc1d085a220bf0c7086b526.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_asrama`
--
ALTER TABLE `tbl_asrama`
  ADD PRIMARY KEY (`id_asrama`);

--
-- Indexes for table `tbl_siswa`
--
ALTER TABLE `tbl_siswa`
  ADD PRIMARY KEY (`id_siswa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_asrama`
--
ALTER TABLE `tbl_asrama`
  MODIFY `id_asrama` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
