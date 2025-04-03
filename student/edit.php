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

    $sql = "SELECT * FROM SinhVien WHERE MaSV = ?";
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
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $MaSV = $_POST['MaSV'];
    $HoTen = $_POST['HoTen'];
    $GioiTinh = $_POST['GioiTinh'];
    $NgaySinh = $_POST['NgaySinh'];
    $Hinh = $_POST['Hinh'];
    $MaNganh = $_POST['MaNganh'];

    $sql = "UPDATE SinhVien SET HoTen = ?, GioiTinh = ?, NgaySinh = ?, Hinh = ?, MaNganh = ? WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $HoTen, $GioiTinh, $NgaySinh, $Hinh, $MaNganh, $MaSV);

    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật sinh viên thành công!'); window.location='../index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}
?>

<h2>SỬA SINH VIÊN</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label for="MaSV" class="form-label">Mã SV:</label>
        <input type="text" class="form-control" id="MaSV" name="MaSV" value="<?php echo $student['MaSV']; ?>" readonly>
    </div>
    <div class="mb-3">
        <label for="HoTen" class="form-label">Họ tên:</label>
        <input type="text" class="form-control" id="HoTen" name="HoTen" value="<?php echo $student['HoTen']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="GioiTinh" class="form-label">Giới tính:</label>
        <select class="form-select" id="GioiTinh" name="GioiTinh" required>
            <option value="Nam" <?php if ($student['GioiTinh'] == 'Nam') echo 'selected'; ?>>Nam</option>
            <option value="Nữ" <?php if ($student['GioiTinh'] == 'Nữ') echo 'selected'; ?>>Nữ</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="NgaySinh" class="form-label">Ngày sinh:</label>
        <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" value="<?php echo $student['NgaySinh']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="Hinh" class="form-label">Hinh hiện tại</label><br>
        <img src="<?php echo $sinhvien['Hinh']; ?>" class="student-img" alt="Hinh"><br><br>
        <label for="Hinh" class="form-label">Chọn hình mới</label>
        <input type="file" class="form-control" id="Hinh" name="Hinh" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="MaNganh" class="form-label">Mã ngành:</label>
        <select class="form-select" id="MaNganh" name="MaNganh" required>
            <?php
            $sql = "SELECT MaNganh, TenNganh FROM NganhHoc";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $selected = ($row['MaNganh'] == $student['MaNganh']) ? 'selected' : '';
                echo "<option value='" . $row['MaNganh'] . "' $selected>" . $row['MaNganh'] . " - " . $row['TenNganh'] . "</option>";
            }
            ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Cập nhật</button>
    <a href="../index.php" class="btn btn-secondary">Hủy</a>
</form>

</div>
</body>
</html>

<?php
$conn->close();
?>