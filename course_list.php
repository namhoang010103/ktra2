<?php
session_start();
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit;
}

$MaSV = $_SESSION['MaSV'];

// Xử lý xóa đăng ký học phần
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['MaDK']) && isset($_GET['MaHP'])) {
    $MaDK = $_GET['MaDK'];
    $MaHP = $_GET['MaHP'];

    // Xóa bản ghi trong ChiTietDangKy
    $sql_delete_chitiet = "DELETE FROM ChiTietDangKy WHERE MaDK = ? AND MaHP = ?";
    $stmt_delete_chitiet = $conn->prepare($sql_delete_chitiet);
    $stmt_delete_chitiet->bind_param("is", $MaDK, $MaHP);

    if ($stmt_delete_chitiet->execute()) {
        // Kiểm tra xem MaDK còn liên kết với học phần nào khác không
        $sql_check_dangky = "SELECT * FROM ChiTietDangKy WHERE MaDK = ?";
        $stmt_check_dangky = $conn->prepare($sql_check_dangky);
        $stmt_check_dangky->bind_param("i", $MaDK);
        $stmt_check_dangky->execute();
        $result_check_dangky = $stmt_check_dangky->get_result();

        if ($result_check_dangky->num_rows == 0) {
            // Nếu không còn học phần nào liên kết với MaDK, xóa bản ghi trong DangKy
            $sql_delete_dangky = "DELETE FROM DangKy WHERE MaDK = ?";
            $stmt_delete_dangky = $conn->prepare($sql_delete_dangky);
            $stmt_delete_dangky->bind_param("i", $MaDK);
            $stmt_delete_dangky->execute();
            $stmt_delete_dangky->close();
        }

        echo "<script>alert('Xóa đăng ký học phần thành công!'); window.location='course_list.php';</script>";
    } else {
        echo "<script>alert('Xóa đăng ký thất bại: " . $conn->error . "');</script>";
    }

    $stmt_delete_chitiet->close();
    $stmt_check_dangky->close();
}

// Hiển thị danh sách học phần đã đăng ký
$sql_dangky = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi, dk.MaDK 
               FROM HocPhan hp 
               JOIN ChiTietDangKy ctdk ON hp.MaHP = ctdk.MaHP 
               JOIN DangKy dk ON ctdk.MaDK = dk.MaDK 
               WHERE dk.MaSV = ?";
$stmt_dangky = $conn->prepare($sql_dangky);
$stmt_dangky->bind_param("s", $MaSV);
$stmt_dangky->execute();
$result_dangky = $stmt_dangky->get_result();

// Tính tổng số học phần và tổng số tín chỉ
$total_courses = $result_dangky->num_rows; // Tổng số học phần
$total_credits = 0;

$result_dangky->data_seek(0); // Đặt lại con trỏ kết quả về đầu để tính tổng

while ($row = $result_dangky->fetch_assoc()) {
    $total_credits += $row['SoTinChi']; // Cộng dồn số tín chỉ
}
?>

<h2>ĐĂNG KÝ HỌC PHẦN</h2>
<div class="summary">
    <p><strong>Tổng số học phần:</strong> <?php echo $total_courses; ?></p>
    <p><strong>Tổng số tín chỉ:</strong> <?php echo $total_credits; ?></p>
</div>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Mã HP</th>
            <th>Tên học phần</th>
            <th>Số tín chỉ</th>
            <th>Chức năng</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result_dangky->data_seek(0); 
        if ($total_courses > 0) {
            while ($row = $result_dangky->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['MaHP']) . "</td>";
                echo "<td>" . htmlspecialchars($row['TenHP']) . "</td>";
                echo "<td>" . htmlspecialchars($row['SoTinChi']) . "</td>";
                echo "<td>
                        <a href='course_list.php?action=delete&MaDK=" . htmlspecialchars($row['MaDK']) . "&MaHP=" . htmlspecialchars($row['MaHP']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Bạn có chắc muốn xóa học phần này?');\">Xóa</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Bạn chưa đăng ký học phần nào.</td></tr>";
        }
        ?>
    </tbody>
</table>
<a href="index.php" class="btn btn-secondary">Quay lại</a>

</div>
</body>
</html>

<?php
$stmt_dangky->close();
$conn->close();
?>