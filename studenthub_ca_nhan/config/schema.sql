
CREATE DATABASE IF NOT EXISTS studenthub_ca_nhan
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE studenthub_ca_nhan;

CREATE TABLE IF NOT EXISTS nguoi_dung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap VARCHAR(50) NOT NULL UNIQUE,
    mat_khau_hash VARCHAR(255) NOT NULL,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    so_dien_thoai VARCHAR(15),
    vai_tro ENUM('sinh_vien', 'giang_vien', 'admin')
        NOT NULL DEFAULT 'sinh_vien'
);
