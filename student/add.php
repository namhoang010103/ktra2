<?php
session_start();
include '../header.php';
include '../db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $MaSV = $_POST['MaSV'];
    $HoTen = $_POST['HoTen'];
    $GioiTinh = $_POST['GioiTinh'];
    $NgaySinh = $_POST['NgaySinh'];
    $MaNganh = $_POST['MaNganh'];

    // Xử lý upload file hình ảnh
    $Hinh = "";
    if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] == 0) {
        $targetDir = "../Content/images/"; // Thư mục chứa ảnh
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['Hinh']['name']); // Đổi tên file tránh trùng
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Kiểm tra định dạng file
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['Hinh']['tmp_name'], $targetFile)) {
                $Hinh = "/ktra1/Content/images/" . $fileName; // Lưu đường dẫn đúng vào CSDL
            } else {
                echo "Lỗi khi tải file lên.";
                exit;
            }
        } else {
            echo "Chỉ chấp nhận file JPG, JPEG, PNG, GIF.";
            exit;
        }
    }

    // Thêm dữ liệu vào database
    $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $MaSV, $HoTen, $GioiTinh, $NgaySinh, $Hinh, $MaNganh);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm sinh viên thành công!'); window.location='../index.php';</script>";
    } else {
        echo "Lỗi khi thêm: " . $conn->error;
    }

    $stmt->close();
}
?>

<h2>THÊM SINH VIÊN</h2>
<form method="POST" action="" enctype="multipart/form-data"> <!-- Thêm enctype -->
    <div class="mb-3">
        <label for="MaSV" class="form-label">Mã SV:</label>
        <input type="text" class="form-control" id="MaSV" name="MaSV" required>
    </div>
    <div class="mb-3">
        <label for="HoTen" class="form-label">Họ tên:</label>
        <input type="text" class="form-control" id="HoTen" name="HoTen" required>
    </div>
    <div class="mb-3">
        <label for="GioiTinh" class="form-label">Giới tính:</label>
        <select class="form-select" id="GioiTinh" name="GioiTinh" required>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="NgaySinh" class="form-label">Ngày sinh:</label>
        <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" required>
    </div>
    <div class="mb-3">
        <label for="Hinh" class="form-label">Hình:</label>
        <input type="file" class="form-control" id="Hinh" name="Hinh" accept="image/*">
        <small class="form-text text-muted">Chỉ chấp nhận file JPG, JPEG, PNG, GIF.</small>
    </div>
    <div class="mb-3">
        <label for="MaNganh" class="form-label">Mã ngành:</label>
        <select class="form-select" id="MaNganh" name="MaNganh" required>
            <?php
            $sql = "SELECT MaNganh, TenNganh FROM NganhHoc";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['MaNganh'] . "'>" . $row['MaNganh'] . " - " . $row['TenNganh'] . "</option>";
            }
            ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Thêm</button>
    <a href="../index.php" class="btn btn-secondary">Hủy</a>
</form>

</div>
</body>
</html>

<?php
$conn->close();
?>
