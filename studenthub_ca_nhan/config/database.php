
<?php
/**
 * Cấu hình kết nối cơ sở dữ liệu StudentHub cá nhân.
 */

$host = 'localhost';
$port = '3306';
$dbname = 'studenthub_ca_nhan';
$username = 'root';

// Thay giá trị bên dưới bằng mật khẩu MySQL trên máy bạn.
$password = '03112006';

try {
    $conn = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log('Lỗi kết nối StudentHub cá nhân: ' . $e->getMessage());

    http_response_code(500);
    exit('Không thể kết nối cơ sở dữ liệu. Vui lòng kiểm tra cấu hình MySQL.');
}
