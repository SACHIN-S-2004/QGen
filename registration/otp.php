<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../login/PHPMailer/src/Exception.php';
require '../login/PHPMailer/src/PHPMailer.php';
require '../login/PHPMailer/src/SMTP.php';

require __DIR__ . '/../importQ/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();


$dbUsername = $_ENV['_USERNAME'];
$dbPassword = $_ENV['_PASSWORD'];
//console_log($dbUsername);
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $email = trim($_POST['email']);
}

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $dbUsername; //SMTP username
    $mail->Password   = $dbPassword; //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //ENCRYPTION_SMTPS Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('sachinsuresh9562@gmail.com', 'QGen Team');
    $mail->addAddress($email, 'Joe User');     //Add a recipient

    $mail->isHTML(true);  
    $otp=rand(100000,999999);                                //Set email format to HTML
    $mail->Subject = 'OTP Verfication';
    $body='Hi '.$email.',
    <br><br>We received your request for a single-use code to use with your account.<br><br>
    Your single-use code is: <b>'.$otp.'</b><br>
    Only enter this code on our official website. Dont share it with anyone. We will never ask for it outside an official platform.<br><br>
    Thanks,<br>
    The QGen team';

    $mail->Body    = $body;
    $mail->send();
    
    //header("Location: Registration.php?otp=".$otp);
    //echo 'Message has been sent';
} catch (Exception $e) {
    $response = ["status" => "error", "message" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    echo json_encode($response);
    exit;
}

$response = ["status" => "success", "message" => "$otp"];
echo json_encode($response);
?>