# StudentHub – Quản lý tài khoản người dùng

## 1. Giới thiệu

StudentHub là hệ thống quản lý khóa học và đăng ký học phần. Trong phạm vi bài tập cá nhân, dự án tập trung xây dựng chức năng quản lý tài khoản người dùng bằng PHP và MySQL.

Chức năng được thực hiện trên bảng `nguoi_dung`, lưu trữ thông tin sinh viên, giảng viên và quản trị viên.

## 2. Công nghệ sử dụng

- **Ngôn ngữ lập trình:** PHP 8.x
- **Cơ sở dữ liệu:** MySQL
- **Kết nối cơ sở dữ liệu:** PDO
- **Máy chủ web:** Apache thông qua XAMPP
- **Giao diện:** HTML, CSS
- **Môi trường phát triển:** Visual Studio Code

## 3. Chức năng chính

- **Thêm tài khoản:** Tạo tài khoản mới với thông tin cần thiết.
- **Hiển thị danh sách:** Hiển thị các tài khoản được lưu trong cơ sở dữ liệu.
- **Sửa tài khoản:** Cập nhật thông tin tài khoản đã tồn tại.
- **Xóa tài khoản:** Xóa tài khoản được lựa chọn.
- **Tìm kiếm tài khoản:** Tìm kiếm theo thông tin như tên đăng nhập hoặc họ tên.
- **Lọc theo vai trò:** Lọc tài khoản theo sinh viên, giảng viên hoặc quản trị viên.
- **Kiểm tra dữ liệu:** Kiểm tra thông tin nhập vào và hạn chế dữ liệu không hợp lệ.
- **Bảo mật mật khẩu:** Sử dụng hàm `password_hash()` để băm mật khẩu trước khi lưu vào cơ sở dữ liệu.
- **Bảo vệ biểu mẫu:** Sử dụng mã CSRF để hỗ trợ ngăn chặn yêu cầu giả mạo từ trang khác.

## 4. Cấu trúc thư mục

studenthub_ca_nhan/
├── config/
│   ├── database.php
│   └── test_db.php
├── index.php
├── schema.sql
├── seed.sql
├── README.md
└── TEST_CASE.md


## 5. Cấu trúc bảng dữ liệu

Bảng chính được sử dụng là `nguoi_dung`, gồm các trường:

- `id`: Mã định danh tài khoản.
- `ten_dang_nhap`: Tên đăng nhập, không được trùng.
- `mat_khau_hash`: Mật khẩu đã được băm.
- `ho_ten`: Họ và tên người dùng.
- `email`: Địa chỉ email.
- `so_dien_thoai`: Số điện thoại.
- `vai_tro`: Vai trò của tài khoản, gồm sinh viên, giảng viên hoặc quản trị viên.

## 6. Kiểm thử

Các trường hợp kiểm thử được ghi trong file `TEST_CASE.md`, bao gồm:

1. Thêm tài khoản hợp lệ.
2. Thêm tài khoản khi bỏ trống họ tên.
3. Thêm tài khoản có tên đăng nhập bị trùng.
4. Sửa thông tin tài khoản.
5. Tìm kiếm tài khoản.

Kết quả kiểm thử cần được xác nhận sau khi chạy chương trình thực tế.

