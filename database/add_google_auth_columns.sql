-- Menambahkan kolom untuk Google OAuth2 authentication
-- Jalankan query ini untuk menambahkan support Google login ke tabel user yang sudah ada

ALTER TABLE `user` 
ADD COLUMN `google_id` VARCHAR(50) NULL AFTER `email`,
ADD COLUMN `google_picture` TEXT NULL AFTER `google_id`,
ADD COLUMN `auth_method` ENUM('local', 'google', 'both') NOT NULL DEFAULT 'local' AFTER `google_picture`,
ADD INDEX `google_id` (`google_id`);

-- Update existing users to have 'local' auth method
UPDATE `user` SET `auth_method` = 'local' WHERE `google_id` IS NULL;
