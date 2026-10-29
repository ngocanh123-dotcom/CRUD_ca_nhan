# TEST CASE – CRUD QUẢN LÝ TÀI KHOẢN STUDENTHUB

## 1. Mục đích kiểm thử

Kiểm tra các chức năng thêm, sửa, xóa và tìm kiếm tài khoản trong bảng `nguoi_dung` của hệ thống StudentHub.

## 2. Danh sách kiểm thử

### TC01 – Thêm tài khoản thành công
- **Dữ liệu đầu vào:** Nhập đầy đủ mã đăng nhập, mật khẩu, họ tên, email, số điện thoại và vai trò hợp lệ.
- **Thao tác:** Nhấn nút Thêm tài khoản.
- **Kết quả mong đợi:** Tài khoản được lưu vào cơ sở dữ liệu và hiển thị trong danh sách.
- **Kết quả dự kiến:** Thêm tài khoản thành công, thông tin tài khoản mới xuất hiện trong danh sách.
- **Đánh giá:** Đạt nếu kết quả thực tế đúng như mong đợi.

### TC02 – Thêm tài khoản khi bỏ trống họ tên
- **Dữ liệu đầu vào:** Để trống trường họ tên, các trường còn lại nhập hợp lệ.
- **Thao tác:** Nhấn nút Thêm tài khoản.
- **Kết quả mong đợi:** Hệ thống thông báo lỗi và không thêm tài khoản.
- **Kết quả dự kiến:** Hệ thống thông báo họ tên không được để trống và không lưu tài khoản mới.
- **Đánh giá:** Đạt nếu hệ thống từ chối dữ liệu không hợp lệ.

### TC03 – Thêm tài khoản bị trùng tên đăng nhập
- **Dữ liệu đầu vào:** Nhập tên đăng nhập đã tồn tại trong cơ sở dữ liệu.
- **Thao tác:** Nhấn nút Thêm tài khoản.
- **Kết quả mong đợi:** Hệ thống thông báo tên đăng nhập đã tồn tại và không tạo tài khoản trùng lặp.
- **Kết quả dự kiến:** Hệ thống thông báo tên đăng nhập bị trùng và không thêm tài khoản.
- **Đánh giá:** Đạt nếu tài khoản trùng không được tạo.

### TC04 – Sửa thông tin tài khoản
- **Dữ liệu đầu vào:** Chọn một tài khoản đang tồn tại và thay đổi họ tên hoặc email bằng dữ liệu hợp lệ.
- **Thao tác:** Nhấn nút Sửa hoặc Lưu thay đổi.
- **Kết quả mong đợi:** Thông tin được cập nhật trong cơ sở dữ liệu và hiển thị đúng trên danh sách.
- **Kết quả dự kiến:** Hệ thống thông báo cập nhật thành công và danh sách hiển thị thông tin mới.
- **Đánh giá:** Đạt nếu thông tin sau khi sửa được lưu chính xác.

### TC05 – Tìm kiếm tài khoản
- **Dữ liệu đầu vào:** Nhập mã đăng nhập hoặc họ tên của một tài khoản đã tồn tại.
- **Thao tác:** Nhập từ khóa vào ô tìm kiếm.
- **Kết quả mong đợi:** Hệ thống hiển thị các tài khoản phù hợp với từ khóa tìm kiếm.
- **Kết quả dự kiến:** Danh sách được lọc và hiển thị tài khoản phù hợp với từ khóa.
- **Đánh giá:** Đạt nếu kết quả tìm kiếm chính xác.

## 3. Tổng kết kiểm thử

- TC01 – Thêm tài khoản: Dự kiến đạt.
- TC02 – Kiểm tra họ tên để trống: Dự kiến đạt.
- TC03 – Kiểm tra tên đăng nhập trùng: Dự kiến đạt.
- TC04 – Sửa thông tin tài khoản: Dự kiến đạt.
- TC05 – Tìm kiếm tài khoản: Dự kiến đạt.

