<?php
session_start();
include 'header.php';
include 'db_connect.php';
// Kiểm tra đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit;
}

// Phân trang
$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Đếm tổng số sinh viên
$sql_count = "SELECT COUNT(*) as total FROM SinhVien";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_records = $row_count['total'];
$total_pages = ceil($total_records / $limit);

// Truy vấn lấy danh sách sinh viên với phân trang
$sql = "SELECT MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh FROM SinhVien LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<h2>TRANG SINH VIÊN</h2>
<a href="student/add.php" class="btn btn-primary mb-3">Thêm sinh viên</a>
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Mã SV</th>
            <th>Họ tên</th>
            <th>Giới tính</th>
            <th>Ngày sinh</th>
            <th>Hình</th>
            <th>Mã ngành</th>
            <th>Chức năng</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['MaSV'] . "</td>";
                echo "<td>" . $row['HoTen'] . "</td>";
                echo "<td>" . $row['GioiTinh'] . "</td>";
                echo "<td>" . $row['NgaySinh'] . "</td>";
                echo "<td><img src='" . $row['Hinh'] . "' alt='Hinh' style='width: 100px; height: auto;'></td>";
                echo "<td>" . $row['MaNganh'] . "</td>";
                echo "<td>
                        <a href='student/detail.php?MaSV=" . $row['MaSV'] . "' class='btn btn-info btn-sm'>Chi tiết</a>
                        <a href='student/edit.php?MaSV=" . $row['MaSV'] . "' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='student/confirm_delete.php?MaSV=" . $row['MaSV'] . "' class='btn btn-danger btn-sm'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Không có sinh viên nào.</td></tr>";
        }
        ?>
    </tbody>
</table>

<div class="pagination">
    <?php
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo "<a href='index.php?page=$i' class='btn btn-outline-primary $active'>$i</a>";
    }
    ?>
</div>

</div>
</body>
</html>

<?php
$conn->close();
?>