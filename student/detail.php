<?php
session_start();
include '../header.php';
include '../db_connect.php';
// Kiểm tra đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit;
}


if (isset($_GET['MaSV'])) {
    $MaSV = $_GET['MaSV'];

    $sql = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, sv.MaNganh, nh.TenNganh 
            FROM SinhVien sv 
            JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh 
            WHERE sv.MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $MaSV);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        echo "Sinh viên không tồn tại.";
        exit;
    }

    $stmt->close();
} else {
    echo "Không có mã sinh viên được cung cấp.";
    exit;
}
?>

<h2>THÔNG TIN CHI TIẾT</h2>
<div class="card">
    <div class="card-body">
        <p><strong>Mã SV:</strong> <?php echo $student['MaSV']; ?></p>
        <p><strong>Họ tên:</strong> <?php echo $student['HoTen']; ?></p>
        <p><strong>Giới tính:</strong> <?php echo $student['GioiTinh']; ?></p>
        <p><strong>Ngày sinh:</strong> <?php echo $student['NgaySinh']; ?></p>
        <p><strong>Hình:</strong> <img src="<?php echo $student['Hinh']; ?>" alt="Hinh" class="img-fluid" style="max-width: 200px;"></p>
        <p><strong>Mã ngành:</strong> <?php echo $student['MaNganh']; ?></p>
        <p><strong>Tên ngành:</strong> <?php echo $student['TenNganh']; ?></p>
        <a href="../index.php" class="btn btn-secondary">Quay lại</a>
    </div>
</div>

</div>
</body>
</html>

<?php
$conn->close();
?>