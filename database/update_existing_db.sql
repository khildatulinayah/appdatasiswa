-- Update script untuk database yang sudah ada
-- Jalankan script ini jika Anda sudah memiliki data siswa/kelas

-- 1. Buat tabel user jika belum ada
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `level` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insert user admin jika belum ada
INSERT IGNORE INTO `user` (`username`, `password`, `nama_lengkap`, `email`, `level`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@app-siswa.com', 'admin');

-- 3. Insert user contoh jika belum ada
INSERT IGNORE INTO `user` (`username`, `password`, `nama_lengkap`, `email`, `level`) 
VALUES ('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Contoh', 'user@app-siswa.com', 'user');

-- 4. Cek tabel yang sudah ada
SELECT 'Tabel siswa: ' + IF(COUNT(*) > 0, 'EXISTS', 'NOT EXISTS') as status FROM information_schema.tables WHERE table_name = 'siswa' AND table_schema = DATABASE();
SELECT 'Tabel kelas: ' + IF(COUNT(*) > 0, 'EXISTS', 'NOT EXISTS') as status FROM information_schema.tables WHERE table_name = 'kelas' AND table_schema = DATABASE();

-- 5. Opsional: Tambahkan created_by dan updated_by ke tabel yang sudah ada
-- Uncomment jika Anda ingin tracking siapa yang membuat/mengubah data

-- ALTER TABLE `siswa` ADD COLUMN `created_by` int(11) NULL AFTER `id_kelas`;
-- ALTER TABLE `siswa` ADD COLUMN `updated_by` int(11) NULL AFTER `created_by`;
-- ALTER TABLE `siswa` ADD CONSTRAINT `fk_siswa_created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;
-- ALTER TABLE `siswa` ADD CONSTRAINT `fk_siswa_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;

-- ALTER TABLE `kelas` ADD COLUMN `created_by` int(11) NULL AFTER `nama_kelas`;
-- ALTER TABLE `kelas` ADD COLUMN `updated_by` int(11) NULL AFTER `created_by`;
-- ALTER TABLE `kelas` ADD CONSTRAINT `fk_kelas_created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;
-- ALTER TABLE `kelas` ADD CONSTRAINT `fk_kelas_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;

-- 6. Update existing records dengan admin user (jika menggunakan foreign key di atas)
-- Uncomment jika Anda menambahkan foreign key

-- UPDATE `siswa` SET `created_by` = 1 WHERE `created_by` IS NULL;
-- UPDATE `siswa` SET `updated_by` = 1 WHERE `updated_by` IS NULL;
-- UPDATE `kelas` SET `created_by` = 1 WHERE `created_by` IS NULL;
-- UPDATE `kelas` SET `updated_by` = 1 WHERE `updated_by` IS NULL;

SELECT '✅ Database update completed!' as message;
