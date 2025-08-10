<?php
session_start();
header('Content-Type: application/json');

ob_start(); // Start output buffering

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$connect = new mysqli("localhost", "root", "", "qgen1");
if ($connect->connect_error) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$email = $_POST['email'] ?? '';
if (empty($email)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

$stmt = $connect->prepare("SELECT email FROM account WHERE email = ?");
if (!$stmt) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database query preparation failed']);
    $connect->close();
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $otp = rand(100000, 999999);
    $_SESSION['email'] = $email;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username   = 'sachinsuresh9562@gmail.com';                     //SMTP username
        $mail->Password   = 'xbzuiemwaajzjwob';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('sachinsuresh9562@gmail.com', 'QGen Team');
        $mail->addAddress($email, 'Joe User');

        $mail->isHTML(true);
        $mail->Subject = 'OTP Verification';
        $body = 'Hi ' . $email . ',
        <br><br>We received your request for a single-use code to use with your account.<br><br>
        Your single-use code is: <b>' . $otp . '</b><br>
        Only enter this code on our official website. Don’t share it with anyone. We will never ask for it outside an official platform.<br><br>
        Thanks,<br>
        The QGen team';
        $mail->Body = $body;

        $mail->send();
        
        ob_end_clean();
        echo json_encode(['success' => true, 'otp' => (string)$otp]);
    } catch (Exception $e) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Email not found']);
}

$stmt->close();
$connect->close();
?>