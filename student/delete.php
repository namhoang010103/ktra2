<?php
session_start();
include '../header.php';
include '../db_connect.php';
// Kiểm tra đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['MaSV'])) {
    $MaSV = $_POST['MaSV'];

    // Xóa sinh viên
    $sql = "DELETE FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $MaSV);

    if ($stmt->execute()) {
        echo "<script>alert('Xóa sinh viên thành công!'); window.location='../index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Không có mã sinh viên được cung cấp.";
    exit;
}

$conn->close();
?>