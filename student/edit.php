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
    $MaNganh = $_POST['MaNganh'];

    // Xử lý upload file hình ảnh
    $Hinh = $student['Hinh']; // Giữ ảnh cũ nếu không có ảnh mới
    if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] == 0) {
        $targetDir = "../Content/images/"; // Thư mục lưu ảnh
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['Hinh']['name']); // Đổi tên file để tránh trùng
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Kiểm tra định dạng file hợp lệ
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['Hinh']['tmp_name'], $targetFile)) {
                $Hinh = "/ktra1/Content/images/" . $fileName; // Lưu đường dẫn đúng vào DB
            } else {
                echo "Lỗi khi tải file lên.";
                exit;
            }
        } else {
            echo "Chỉ chấp nhận file JPG, JPEG, PNG, GIF.";
            exit;
        }
    }

    // Cập nhật vào database
    $sql = "UPDATE SinhVien SET HoTen = ?, GioiTinh = ?, NgaySinh = ?, Hinh = ?, MaNganh = ? WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $HoTen, $GioiTinh, $NgaySinh, $Hinh, $MaNganh, $MaSV);

    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật sinh viên thành công!'); window.location='../index.php';</script>";
    } else {
        echo "Lỗi khi cập nhật: " . $conn->error;
    }

    $stmt->close();
}
?>

<h2>SỬA SINH VIÊN</h2>
<form method="POST" action="" enctype="multipart/form-data"> <!-- Thêm enctype -->
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
        <label>Hình hiện tại:</label><br>
        <img src="<?php echo $student['Hinh']; ?>" class="student-img" alt="Hình" width="150"><br><br>
        <label for="Hinh" class="form-label">Chọn hình mới:</label>
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
