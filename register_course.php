<?php
session_start();
include 'header.php';
include 'db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit;
}

$MaSV = $_SESSION['MaSV'];

// Xử lý đăng ký học phần
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['MaHP'])) {
    $MaHP = $_POST['MaHP'];

    $sql_check = "SELECT * FROM ChiTietDangKy WHERE MaSV = ? AND MaHP = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $MaSV, $MaHP);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>alert('Bạn đã đăng ký học phần này rồi!');</script>";
    } else {
        $sql_dangky = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (CURDATE(), ?)";
        $stmt_dangky = $conn->prepare($sql_dangky);
        $stmt_dangky->bind_param("s", $MaSV);
        $stmt_dangky->execute();
        $MaDK = $conn->insert_id;

        $sql_chitiet = "INSERT INTO ChiTietDangKy (MaDK, MaHP, MaSV) VALUES (?, ?, ?)";
        $stmt_chitiet = $conn->prepare($sql_chitiet);
        $stmt_chitiet->bind_param("iss", $MaDK, $MaHP, $MaSV);

        if ($stmt_chitiet->execute()) {
            echo "<script>alert('Đăng ký học phần thành công!');</script>";
        } else {
            echo "<script>alert('Đăng ký thất bại: " . $conn->error . "');</script>";
        }

        $stmt_dangky->close();
        $stmt_chitiet->close();
    }

    $stmt_check->close();
}

$sql = "SELECT MaHP, TenHP, SoTinChi FROM HocPhan";
$result = $conn->query($sql);
?>

<h2>ĐĂNG KÝ HỌC PHẦN</h2>
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Mã HP</th>
            <th>Tên HP</th>
            <th>Số tín chỉ</th>
            <th>Chức năng</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['MaHP'] . "</td>";
                echo "<td>" . $row['TenHP'] . "</td>";
                echo "<td>" . $row['SoTinChi'] . "</td>";
                echo "<td>
                        <form method='POST' action='' style='display:inline;'>
                            <input type='hidden' name='MaHP' value='" . $row['MaHP'] . "'>
                            <button type='submit' class='btn btn-primary btn-sm'>Đăng ký</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Không có học phần nào.</td></tr>";
        }
        ?>
    </tbody>
</table>
<a href="index.php" class="btn btn-secondary">Quay lại</a>

</div>
</body>
</html>

<?php
$conn->close();
?>