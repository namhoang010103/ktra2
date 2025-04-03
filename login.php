<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $MaSV = $_POST['MaSV'];
    $Password = $_POST['Password'];

    // Truy vấn bảng SinhVien
    $sql = "SELECT * FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $MaSV);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Kiểm tra xem MaSV có tồn tại và Password khớp với MaSV không
    if ($user && $Password === $user['MaSV']) {
        $_SESSION['MaSV'] = $user['MaSV'];
        header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('Mã sinh viên hoặc mật khẩu không đúng!');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center">Đăng nhập</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="MaSV" placeholder="Mã sinh viên" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="Password" placeholder="Mật khẩu" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>