-- Insert user admin default
-- Password: admin123 (hash dengan password_hash)
INSERT INTO `user` (`username`, `password`, `nama_lengkap`, `email`, `level`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@app-siswa.com', 'admin');

-- Insert user contoh
-- Password: user123 (hash dengan password_hash)
INSERT INTO `user` (`username`, `password`, `nama_lengkap`, `email`, `level`) 
VALUES ('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Contoh', 'user@app-siswa.com', 'user');
