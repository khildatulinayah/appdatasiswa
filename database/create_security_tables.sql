-- Database tables for Brute Force Protection System
-- Jalankan query ini untuk membuat tabel keamanan

-- 1. Tabel untuk tracking login attempts
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `user_agent` text DEFAULT NULL,
  `attempt_type` enum('local','google','forgot_password') NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`),
  KEY `username` (`username`),
  KEY `attempt_time` (`attempt_time`),
  KEY `success` (`success`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel untuk blocked IP addresses
CREATE TABLE IF NOT EXISTS `blocked_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `blocked_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `blocked_until` timestamp NULL DEFAULT NULL,
  `is_permanent` tinyint(1) NOT NULL DEFAULT '0',
  `attempts_count` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`),
  KEY `blocked_until` (`blocked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel untuk locked accounts
CREATE TABLE IF NOT EXISTS `locked_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `lock_reason` varchar(255) NOT NULL,
  `locked_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `locked_until` timestamp NULL DEFAULT NULL,
  `is_permanent` tinyint(1) NOT NULL DEFAULT '0',
  `failed_attempts` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `username` (`username`),
  KEY `locked_until` (`locked_until`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel untuk security events log
CREATE TABLE IF NOT EXISTS `security_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_type` (`event_type`),
  KEY `severity` (`severity`),
  KEY `ip_address` (`ip_address`),
  KEY `event_time` (`event_time`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel untuk rate limiting
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `request_count` int(11) NOT NULL DEFAULT '1',
  `window_start` timestamp NOT NULL DEFAULT current_timestamp(),
  `window_duration` int(11) NOT NULL DEFAULT '300', -- 5 minutes in seconds
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_endpoint_window` (`ip_address`, `endpoint`, `window_start`),
  KEY `window_start` (`window_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
