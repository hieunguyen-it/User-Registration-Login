<?php
include_once("dbconnection.php");
include "PHPMailer-master/src/PHPMailer.php";
include "PHPMailer-master/src/Exception.php";
include "PHPMailer-master/src/OAuth.php";
include "PHPMailer-master/src/POP3.php";
include "PHPMailer-master/src/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>

<?php session_start();
require_once('dbconnection.php');


//Code chức năng đăng ký tài khoản
if (isset($_POST['signup'])) {
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$contact = $_POST['contact'];
	$enc_password = $password;
	$sql = mysqli_query($con, "select id from users where email='$email'"); // id của bảng users từ cột email
	$row = mysqli_num_rows($sql); // mysqli_num_rows là kết quả tập hợp của các hàng mysqli_query
	if ($row > 0) {
		echo "<script>alert('Email id đã tồn tại với một tài khoản khác. Vui lòng thử với id email khác');</script>";
	} else {
		$msg = mysqli_query($con, "insert into users(fname,lname,email,password,contactno) 
	values('$fname','$lname','$email','$enc_password','$contact')");

		if ($msg) {
			echo "<script>alert('Đăng ký thành công');</script>";
		}
	}
}

// Code chức năng đăng nhập
if (isset($_POST['login'])) {
	$password = $_POST['password'];
	$dec_password = $password;
	$useremail = $_POST['uemail'];
	$ret = mysqli_query($con, "SELECT * FROM users WHERE email='$useremail' and password='$dec_password'");
	$num = mysqli_fetch_array($ret);
	if ($num > 0) {
		$extra = "welcome.php";
		$_SESSION['login'] = $_POST['uemail'];
		$_SESSION['id'] = $num['id'];
		$_SESSION['name'] = $num['fname'];
		$host = $_SERVER['HTTP_HOST']; // Trả về yêu cầu từ yêu cầu hiện tạis
		$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		// rtrim loại bỏ khoảng trắng dirname trả về đường dẫn đến thư mục gốc
		// sau đó $_Server sẽ thông qua thành phần PHP_SELF để trả về tên file của file đang được chạy
		header("location:http://$host$uri/$extra");
		exit();
	} else {
		echo "<script>alert('Sai tên đăng nhập hoặc mật khẩu');</script>";
		$extra = "index.php";
		$host  = $_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		// header("location:http://$host$uri/$extra");
		exit();
	}
}

//Code for Forgot Password

// if (isset($_POST['send'])) {
// 	$femail = $_POST['femail'];

// 	$row1 = mysqli_query($con, "select email,password from users where email='$femail'");
// 	$row2 = mysqli_fetch_array($row1);
// 	if ($row2 > 0) {
// 		$email = $row2['email'];
// 		$subject = "Information about your password";
// 		$password = $row2['password'];
// 		$message = "Your password is " . $password;
// 		mail($email, $subject, $message, "From: $email");
// 		echo  "<script>alert('Your Password has been sent Successfully');</script>";
// 	} else {
// 		echo "<script>alert('Email not register with us');</script>";
// 	}
// }

if (isset($_POST["umail"])) {
	$email = $_POST["umail"];
	// tạo câu truy vấn 
	$sql = "SELECT * FROM users WHERE email = '$email'";
	$query = mysqli_query($con, $sql);
	$row = mysqli_fetch_array($query);
	// $email = $row["email"] ;
	if (mysqli_num_rows($query) > 0) {
		// function randomString($length){
		//     $arrCharacter = array_merge(range('A' , 'Z'), range('a' , 'z') , range(0 , 9)) ;
		//     $arrCharacter = implode('' , $arrCharacter) ;
		//     $arrCharacter = str_shuffle($arrCharacter) ;
		//     $result = substr($arrCharacter , 0 , $length) ;
		//     return $result ;
		// }
		// hàm tạo mật khẩu kiểu chuỗi ngẫu nhiên
		function randomString($length)
		{
			$arrCharacter = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9)); // hàm array_merge dùng để gộp các mảng con thành mảng cha, hàm range dùng để tạo một mảng ví dụ từ a đến z , A đến Z, 0 đến 9 .....
			$arrCharacter = implode('', $arrCharacter); // hàm dùng để chuyển mảng thành chuỗi
			$arrCharacter = str_shuffle($arrCharacter); // hàm dùng để sắp sếp ngẫu nhiên các kí tự bất kì trong chuỗi
			$result = substr($arrCharacter, 0, $length); // hàm dùng để lấy một chuỗi con trong chuỗi cha
			return $result;
		}
		// gọi hàm 
		$str_random = randomString(10);
		$str_body = 'Email của bạn là: ' . $email . '<br> Mật khẩu mới của bạn là: ' . $str_random;
		// viết câu truy vấn UPDATE pass
		$sql = "UPDATE users SET password = '$str_random' WHERE email = '$email'  ";
		// thực hiện câu truy vấn
		mysqli_query($con, $sql);
		///////////////////////////////////thực hiện quá trình gửi mật khẩu mới  vào mail khách hàng////////////////////////////////////////////

		$mail = new PHPMailer(true);                              // Passing 'true' enables exceptions
		try {
			//Server settings (0 là không hiển thi lỗi , 2 là hiển thị lỗi)
			$mail->SMTPDebug = 0;                                 // Enable verbose debug output
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'anhnhatdev2504@gmail.com';                 // SMTP username
			// $mail->Password = 'vietpr0sh0p';                           // SMTP password
			$mail->Password = 'aooetapcleuuisun';                           // SMTP password
			$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, 'ssl' also accepted
			$mail->Port = 465;                                    // TCP port to connect to

			//Recipients
			$mail->CharSet = 'UTF-8';
			$mail->setFrom('lamthieu0@gmail.com', 'Vietpro Mobile Shop');				// Gửi mail tới Mail Server
			$mail->addAddress($email);               // Gửi mail tới mail người nhận
			//$mail->addReplyTo('ceo.vietpro@gmail.com', 'Information');
			$mail->addCC('lamthieu0@gmail.com');
			//$mail->addBCC('bcc@example.com');

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Xác nhận mật khẩu mới từ Vietpro Mobile Shop';
			$mail->Body    = $str_body;
			$mail->AltBody = 'Quên mật khẩu';

			$mail->send();
			//header('location:index.php?page_layout=success');
			header("location:success.php");
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}
	} else {
		echo "Email không hợp lệ";
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<title>Login System</title>
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Elegent Tab Forms,Login Forms,Sign up Forms,Registration Forms,News latter Forms,Elements" . />
	<script type="application/x-javascript">
		addEventListener("load", function() {
			setTimeout(hideURLbar, 0);
		}, false);

		function hideURLbar() {
			window.scrollTo(0, 1);
		}
	</script>
	</script>
	<script src="js/jquery.min.js"></script>
	<script src="js/easyResponsiveTabs.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#horizontalTab').easyResponsiveTabs({
				type: 'default',
				width: 'auto',
				fit: true
			});
		});
	</script>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,400,600,700,200italic,300italic,400italic,600italic|Lora:400,700,400italic,700italic|Raleway:400,500,300,600,700,200,100' rel='stylesheet' type='text/css'>
</head>

<body>
	<div class="main">
		<h1>Registration and Login System</h1>
		<div class="sap_tabs">
			<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
				<ul class="resp-tabs-list">
					<li class="resp-tab-item" aria-controls="tab_item-0" role="tab">
						<div class="top-img"><img src="images/top-note.png" alt="" /></div><span>Register</span>

					</li>
					<li class="resp-tab-item" aria-controls="tab_item-1" role="tab">
						<div class="top-img"><img src="images/top-lock.png" alt="" /></div><span>Login</span>
					</li>
					<li class="resp-tab-item lost" aria-controls="tab_item-2" role="tab">
						<div class="top-img"><img src="images/top-key.png" alt="" /></div><span>Forgot Password</span>
					</li>
					<div class="clear"></div>
				</ul>

				<div class="resp-tabs-container">
					<div class="tab-1 resp-tab-content" aria-labelledby="tab_item-0">
						<div class="facts">
							<div class="register">
								<form name="registration" method="post" action="" enctype="multipart/form-data">
									<p>First Name </p>
									<input type="text" class="text" value="" name="fname" required>
									<p>Last Name </p>
									<input type="text" class="text" value="" name="lname" required>
									<p>Email Address </p>
									<input type="text" class="text" value="" name="email">
									<p>Password </p>
									<input type="password" value="" name="password" required>
									<p>Contact No. </p>
									<input type="text" value="" name="contact" required>
									<div class="sign-up">
										<input type="reset" value="Reset">
										<input type="submit" name="signup" value="Sign Up">
										<div class="clear"> </div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="tab-2 resp-tab-content" aria-labelledby="tab_item-1">
						<div class="facts">
							<div class="login">
								<div class="buttons">
								</div>
								<form name="login" action="" method="post">
									<input type="text" class="text" name="uemail" value="" placeholder="Enter your registered email">
									<a href="#" class=" icon email"></a>

									<input type="password" value="" name="password" placeholder="Enter valid password">
									<a href="#" class=" icon lock"></a>

									<div class="p-container">

										<div class="submit two">
											<input type="submit" name="login" value="LOG IN">
										</div>
										<div class="clear"> </div>
									</div>

								</form>
							</div>
						</div>
					</div>
					<div class="tab-2 resp-tab-content" aria-labelledby="tab_item-1">
						<div class="facts">
							<div class="login">
								<div class="buttons">
									<form name="login" method="post">
										<input class="text" placeholder="E-mail" name="umail" type="text">
										<a href="#" class=" icon email"></a>
										<button type="submit" name="sbm" class="text" style="color:red; padding:10px; font-size:18px;"> Xác nhận lấy lại mật khẩu </button>
										<a href="#" class=" icon email"></a>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</body>

</html>