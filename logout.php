<?php
session_start(); // Bắt đầu thực hiện các phiên hoặc tiếp tục các phiên hiện có dựa trên phương thức POST VÀ GET
$_SESSION['login']==""; // Biến $_SESSION cho phép lưu trữ các phiên đã nhập vào trường "login" .

session_unset();  // Hàm session_unset () giải phóng tất cả các biến phiên hiện được đăng ký.
$_SESSION['action1']="You have logged out successfully..!"; // Khi người dùng thực hiện submit action1 từ form logout thành công sẽ 
// thoát chương trình và in ra thông báo  "You have logged out successfully..!"
?>
<script language="javascript">
document.location="index.php";
</script>
