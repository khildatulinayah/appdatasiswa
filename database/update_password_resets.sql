-- Update password_resets table for secure email verification system
-- Jalankan query ini untuk upgrade forgot password system

-- Drop existing table if it exists
DROP TABLE IF EXISTS `password_resets`;

-- Create new password_resets table with verification code system
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `verification_code` varchar(6) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `attempts_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `email` (`email`),
  KEY `verification_code` (`verification_code`),
  KEY `expires_at` (`expires_at`),
  KEY `ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add password reset tracking to security_events if not exists
ALTER TABLE `security_events` 
ADD COLUMN IF NOT EXISTS `reset_token` varchar(255) NULL AFTER `user_id`,
ADD INDEX IF NOT EXISTS `reset_token` (`reset_token`);

-- Create password_reset_attempts table for rate limiting
CREATE TABLE IF NOT EXISTS `password_reset_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `attempt_type` enum('request','verify','reset') NOT NULL DEFAULT 'request',
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `ip_address` (`ip_address`),
  KEY `attempt_time` (`attempt_time`),
  KEY `success` (`success`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
