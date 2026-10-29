
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Hàm an toàn khi hiển thị dữ liệu HTML
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Token bảo vệ form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$thongBao = $_SESSION['flash'] ?? '';
$loaiThongBao = $_SESSION['flash_type'] ?? 'success';
unset($_SESSION['flash'], $_SESSION['flash_type']);

$duLieuSua = null;

// Xử lý thêm, sửa, xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (
            !isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            throw new RuntimeException('Phiên làm việc không hợp lệ. Hãy tải lại trang.');
        }

        $action = $_POST['action'] ?? '';

        // XÓA TÀI KHOẢN
        if ($action === 'delete') {
            $id = filter_var(
                $_POST['id'] ?? '',
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if (!$id) {
                throw new RuntimeException('ID tài khoản không hợp lệ.');
            }

            $stmt = $conn->prepare(
                'DELETE FROM nguoi_dung WHERE id = :id'
            );
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = $stmt->rowCount()
                ? 'Đã xóa tài khoản thành công.'
                : 'Không tìm thấy tài khoản cần xóa.';
        }

        // THÊM HOẶC SỬA TÀI KHOẢN
        elseif ($action === 'save') {
            $id = filter_var(
                $_POST['id'] ?? '',
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            $tenDangNhap = trim($_POST['ten_dang_nhap'] ?? '');
            $matKhau = $_POST['mat_khau'] ?? '';
            $hoTen = trim($_POST['ho_ten'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
            $vaiTro = $_POST['vai_tro'] ?? '';

            // Kiểm tra dữ liệu đầu vào
            if (
                $tenDangNhap === '' ||
                $hoTen === '' ||
                $email === '' ||
                $vaiTro === ''
            ) {
                throw new RuntimeException('Vui lòng nhập đầy đủ thông tin bắt buộc.');
            }

            if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $tenDangNhap)) {
                throw new RuntimeException(
                    'Tên đăng nhập phải có 3–50 ký tự, chỉ gồm chữ, số và dấu _.'
                );
            }

            if (mb_strlen($hoTen) > 100) {
                throw new RuntimeException('Họ tên không được vượt quá 100 ký tự.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Email không đúng định dạng.');
            }

            if (strlen($email) > 100) {
                throw new RuntimeException('Email không được vượt quá 100 ký tự.');
            }

            if (strlen($soDienThoai) > 15) {
                throw new RuntimeException('Số điện thoại không được vượt quá 15 ký tự.');
            }

            if (
                $soDienThoai !== '' &&
                !preg_match('/^[0-9+\s().-]+$/', $soDienThoai)
            ) {
                throw new RuntimeException('Số điện thoại không hợp lệ.');
            }

            $cacVaiTro = ['sinh_vien', 'giang_vien', 'admin'];

            if (!in_array($vaiTro, $cacVaiTro, true)) {
                throw new RuntimeException('Vai trò không hợp lệ.');
            }

            // THÊM MỚI
            if ($id === false || $id === null) {
                if (strlen($matKhau) < 6) {
                    throw new RuntimeException(
                        'Mật khẩu khi tạo mới phải có ít nhất 6 ký tự.'
                    );
                }

                $matKhauHash = password_hash(
                    $matKhau,
                    PASSWORD_DEFAULT
                );

                $sql = 'INSERT INTO nguoi_dung
                        (ten_dang_nhap, mat_khau_hash, ho_ten,
                         email, so_dien_thoai, vai_tro)
                        VALUES
                        (:ten_dang_nhap, :mat_khau_hash, :ho_ten,
                         :email, :so_dien_thoai, :vai_tro)';

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'ten_dang_nhap' => $tenDangNhap,
                    'mat_khau_hash' => $matKhauHash,
                    'ho_ten' => $hoTen,
                    'email' => $email,
                    'so_dien_thoai' => $soDienThoai ?: null,
                    'vai_tro' => $vaiTro
                ]);

                $_SESSION['flash'] = 'Thêm tài khoản thành công.';
            }

            // CẬP NHẬT
            else {
                $kiemTra = $conn->prepare(
                    'SELECT id FROM nguoi_dung WHERE id = :id'
                );
                $kiemTra->execute(['id' => $id]);

                if (!$kiemTra->fetch()) {
                    throw new RuntimeException(
                        'Tài khoản cần sửa không tồn tại.'
                    );
                }

                if ($matKhau !== '' && strlen($matKhau) < 6) {
                    throw new RuntimeException(
                        'Mật khẩu mới phải có ít nhất 6 ký tự.'
                    );
                }

                if ($matKhau !== '') {
                    $matKhauHash = password_hash(
                        $matKhau,
                        PASSWORD_DEFAULT
                    );

                    $sql = 'UPDATE nguoi_dung SET
                            ten_dang_nhap = :ten_dang_nhap,
                            mat_khau_hash = :mat_khau_hash,
                            ho_ten = :ho_ten,
                            email = :email,
                            so_dien_thoai = :so_dien_thoai,
                            vai_tro = :vai_tro
                            WHERE id = :id';
                } else {
                    $sql = 'UPDATE nguoi_dung SET
                            ten_dang_nhap = :ten_dang_nhap,
                            ho_ten = :ho_ten,
                            email = :email,
                            so_dien_thoai = :so_dien_thoai,
                            vai_tro = :vai_tro
                            WHERE id = :id';
                }

                $stmt = $conn->prepare($sql);

                $params = [
                    'ten_dang_nhap' => $tenDangNhap,
                    'ho_ten' => $hoTen,
                    'email' => $email,
                    'so_dien_thoai' => $soDienThoai ?: null,
                    'vai_tro' => $vaiTro,
                    'id' => $id
                ];

                if ($matKhau !== '') {
                    $params['mat_khau_hash'] = $matKhauHash;
                }

                $stmt->execute($params);

                $_SESSION['flash'] = 'Cập nhật tài khoản thành công.';
            }
        } else {
            throw new RuntimeException('Thao tác không hợp lệ.');
        }

        $_SESSION['flash_type'] = 'success';
    } catch (RuntimeException $ex) {
        $_SESSION['flash'] = $ex->getMessage();
        $_SESSION['flash_type'] = 'error';
    } catch (PDOException $ex) {
        error_log($ex->getMessage());

        if ($ex->getCode() === '23000') {
            $_SESSION['flash'] =
                'Tên đăng nhập hoặc email đã tồn tại, hoặc dữ liệu vi phạm ràng buộc.';
        } else {
            $_SESSION['flash'] =
                'Có lỗi khi xử lý dữ liệu. Vui lòng kiểm tra CSDL.';
        }

        $_SESSION['flash_type'] = 'error';
    }

    header('Location: index.php');
    exit;
}

// LẤY DỮ LIỆU ĐỂ SỬA
if (isset($_GET['edit'])) {
    $idSua = filter_var(
        $_GET['edit'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($idSua) {
        $stmt = $conn->prepare(
            'SELECT id, ten_dang_nhap, ho_ten, email,
                    so_dien_thoai, vai_tro
             FROM nguoi_dung WHERE id = :id'
        );
        $stmt->execute(['id' => $idSua]);
        $duLieuSua = $stmt->fetch() ?: null;
    }
}

// TÌM KIẾM VÀ LỌC VAI TRÒ
$tuKhoa = trim($_GET['q'] ?? '');
$locVaiTro = $_GET['vai_tro'] ?? '';

$sql = 'SELECT id, ten_dang_nhap, ho_ten, email,
               so_dien_thoai, vai_tro
        FROM nguoi_dung
        WHERE (
            ten_dang_nhap LIKE :q1
            OR ho_ten LIKE :q2
            OR email LIKE :q3
        )';

$params = [
    'q1' => '%' . $tuKhoa . '%',
    'q2' => '%' . $tuKhoa . '%',
    'q3' => '%' . $tuKhoa . '%'
];

if (in_array($locVaiTro, ['sinh_vien', 'giang_vien', 'admin'], true)) {
    $sql .= ' AND vai_tro = :vai_tro';
    $params['vai_tro'] = $locVaiTro;
}

$sql .= ' ORDER BY id DESC';

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$danhSach = $stmt->fetchAll();

$tongTaiKhoan = (int)$conn
    ->query('SELECT COUNT(*) FROM nguoi_dung')
    ->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD quản lý tài khoản | StudentHub</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #eff6ff;
            color: #1e293b;
        }

        .container {
            max-width: 1250px;
            margin: 35px auto;
            padding: 0 20px;
        }

        header {
            background: linear-gradient(120deg, #0f172a, #1e3a8a);
            color: white;
            padding: 25px;
            border-radius: 14px;
            margin-bottom: 22px;
        }

        header h1 { margin: 0 0 8px; }
        header p { margin: 0; color: #dbeafe; }

        .stats, .layout {
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 20px;
            align-items: start;
        }

        .stats { margin-bottom: 20px; }

        .card {
            background: white;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .04);
        }

        .stat-number {
            font-size: 30px;
            color: #2563eb;
            font-weight: bold;
            margin-top: 8px;
        }

        h2 { font-size: 20px; margin-top: 0; }

        label {
            display: block;
            font-weight: bold;
            font-size: 14px;
            margin: 14px 0 7px;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
            background: white;
        }

        input:focus, select:focus {
            outline: 2px solid #bfdbfe;
            border-color: #2563eb;
        }

        .btn {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 10px 13px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
        }

        .primary { background: #2563eb; color: white; }
        .secondary { background: #e2e8f0; color: #1e293b; }
        .warning { background: #fef3c7; color: #92400e; }
        .danger { background: #fee2e2; color: #b91c1c; }

        .form-actions {
            display: flex;
            gap: 8px;
            margin-top: 18px;
        }

        .notice {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }

        .search-form {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .search-form input { flex: 1; min-width: 100px; }
        .search-form select { width: 170px; }

        .table-wrap { overflow-x: auto; }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 13px;
        }

        th, td {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        th {
            background: #eff6ff;
            color: #1e40af;
            white-space: nowrap;
        }

        .role {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 20px;
            background: #dbeafe;
            color: #1d4ed8;
            white-space: nowrap;
        }

        .actions {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .actions form { margin: 0; }

        .hint {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }

        @media (max-width: 850px) {
            .layout, .stats { grid-template-columns: 1fr; }
            .search-form { flex-wrap: wrap; }
            .search-form select { width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">

    <header>
        <h1>STUDENTHUB</h1>
        <p>Hệ thống CRUD quản lý tài khoản người dùng</p>
    </header>

    <?php if ($thongBao !== ''): ?>
        <div class="notice <?= e($loaiThongBao) ?>">
            <?= e($thongBao) ?>
        </div>
    <?php endif; ?>

    <div class="stats">
        <div class="card">
            <div>Tổng số tài khoản trong hệ thống</div>
            <div class="stat-number"><?= $tongTaiKhoan ?></div>
        </div>

        <div class="card">
            <h2>Chức năng quản lý tài khoản</h2>
            <p>Thêm, xem, sửa, xóa, tìm kiếm và lọc tài khoản theo vai trò.</p>
        </div>
    </div>

    <div class="layout">

        <section class="card">
            <h2>
                <?= $duLieuSua ? 'Cập nhật tài khoản' : 'Thêm tài khoản mới' ?>
            </h2>

            <form method="post" action="index.php">
                <input type="hidden" name="csrf_token"
                       value="<?= e($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="save">

                <input type="hidden" name="id"
                       value="<?= e($duLieuSua['id'] ?? '') ?>">

                <label for="ten_dang_nhap">Tên đăng nhập *</label>
                <input id="ten_dang_nhap" name="ten_dang_nhap"
                       maxlength="50" required
                       pattern="[A-Za-z0-9_]{3,50}"
                       title="3–50 ký tự, chỉ gồm chữ, số và dấu gạch dưới"
                       value="<?= e($duLieuSua['ten_dang_nhap'] ?? '') ?>">

                <label for="mat_khau">
                    Mật khẩu <?= $duLieuSua ? '(để trống nếu không đổi)' : '*' ?>
                </label>
                <input id="mat_khau" name="mat_khau" type="password"
                       minlength="6" autocomplete="new-password"
                       <?= $duLieuSua ? '' : 'required' ?>>

                <label for="ho_ten">Họ và tên *</label>
                <input id="ho_ten" name="ho_ten" maxlength="100" required
                       value="<?= e($duLieuSua['ho_ten'] ?? '') ?>">

                <label for="email">Email *</label>
                <input id="email" name="email" type="email"
                       maxlength="100" required
                       value="<?= e($duLieuSua['email'] ?? '') ?>">

                <label for="so_dien_thoai">Số điện thoại</label>
                <input id="so_dien_thoai" name="so_dien_thoai"
                       maxlength="15"
                       value="<?= e($duLieuSua['so_dien_thoai'] ?? '') ?>">

                <label for="vai_tro">Vai trò *</label>
                <select id="vai_tro" name="vai_tro" required>
                    <option value="">-- Chọn vai trò --</option>
                    <option value="sinh_vien"
                        <?= ($duLieuSua['vai_tro'] ?? '') === 'sinh_vien' ? 'selected' : '' ?>>
                        Sinh viên
                    </option>
                    <option value="giang_vien"
                        <?= ($duLieuSua['vai_tro'] ?? '') === 'giang_vien' ? 'selected' : '' ?>>
                        Giảng viên
                    </option>
                    <option value="admin"
                        <?= ($duLieuSua['vai_tro'] ?? '') === 'admin' ? 'selected' : '' ?>>
                        Quản trị viên
                    </option>
                </select>

                <p class="hint">
                    * Trường bắt buộc. Mật khẩu được lưu dưới dạng băm,
                    không hiển thị trong danh sách.
                </p>

                <div class="form-actions">
                    <button type="submit" class="btn primary">
                        <?= $duLieuSua ? 'Lưu thay đổi' : 'Thêm tài khoản' ?>
                    </button>

                    <?php if ($duLieuSua): ?>
                        <a href="index.php" class="btn secondary">Hủy sửa</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Danh sách tài khoản</h2>

            <form method="get" action="index.php" class="search-form">
                <input type="search" name="q"
                       placeholder="Tìm mã đăng nhập, họ tên, email..."
                       value="<?= e($tuKhoa) ?>">

                <select name="vai_tro">
                    <option value="">Tất cả vai trò</option>
                    <option value="sinh_vien"
                        <?= $locVaiTro === 'sinh_vien' ? 'selected' : '' ?>>
                        Sinh viên
                    </option>
                    <option value="giang_vien"
                        <?= $locVaiTro === 'giang_vien' ? 'selected' : '' ?>>
                        Giảng viên
                    </option>
                    <option value="admin"
                        <?= $locVaiTro === 'admin' ? 'selected' : '' ?>>
                        Quản trị viên
                    </option>
                </select>

                <button class="btn primary" type="submit">Tìm kiếm</button>
                <a href="index.php" class="btn secondary">Đặt lại</a>
            </form>

            <p class="hint">
                Tìm thấy <?= count($danhSach) ?> tài khoản phù hợp.
            </p>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Vai trò</th>
                        <th>Thao tác</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if (!$danhSach): ?>
                        <tr>
                            <td colspan="7">Chưa có tài khoản phù hợp.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSach as $tk): ?>
                            <tr>
                                <td><?= e($tk['id']) ?></td>
                                <td><?= e($tk['ten_dang_nhap']) ?></td>
                                <td><?= e($tk['ho_ten']) ?></td>
                                <td><?= e($tk['email']) ?></td>
                                <td><?= e($tk['so_dien_thoai'] ?? '') ?></td>
                                <td>
                                    <span class="role">
                                        <?php
                                        $tenVaiTro = [
                                            'sinh_vien' => 'Sinh viên',
                                            'giang_vien' => 'Giảng viên',
                                            'admin' => 'Quản trị viên'
                                        ];
                                        echo e($tenVaiTro[$tk['vai_tro']] ?? $tk['vai_tro']);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a class="btn warning"
                                           href="index.php?edit=<?= (int)$tk['id'] ?>">
                                            Sửa
                                        </a>

                                        <form method="post" action="index.php"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?');">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= e($_SESSION['csrf_token']) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id"
                                                   value="<?= (int)$tk['id'] ?>">
                                            <button type="submit" class="btn danger">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>
</body>
</html>
