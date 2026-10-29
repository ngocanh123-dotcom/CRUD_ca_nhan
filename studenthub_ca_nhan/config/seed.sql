
USE studenthub_ca_nhan;

-- Thêm dữ liệu mẫu cho bảng nguoi_dung
-- Không thêm lại tài khoản nếu tên đăng nhập hoặc email đã tồn tại.

INSERT INTO nguoi_dung
    (ten_dang_nhap, mat_khau_hash, ho_ten, email, so_dien_thoai, vai_tro)
VALUES
(
    'SV001',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Nguyen Van An',
    'sv001@example.com',
    '0900000001',
    'sinh_vien'
),
(
    'GV001',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Tran Thi Binh',
    'gv001@example.com',
    '0900000002',
    'giang_vien'
),
(
    'admin01',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Quan Tri Vien',
    'admin01@example.com',
    '0900000003',
    'admin'
)
ON DUPLICATE KEY UPDATE
    ten_dang_nhap = VALUES(ten_dang_nhap);
