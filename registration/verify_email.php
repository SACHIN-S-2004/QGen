<?php
    header('Content-Type: application/json');

    $connect = new mysqli("localhost","root","");
    if ($connect->connect_error) {
        $response = ["status" => "error", "message" => "Unexpected error occured , Try Later!"];
        echo json_encode($response);
        exit;
    }

    $query = "create database if not exists qgen1";
    if(!($Result = $connect->query($query))){
        $response = ["status" => "error", "message" => "Unexpected error occured , Try Later!"];
        echo json_encode($response);
        exit;
    }

    $db = mysqli_select_db($connect,"qgen1");
    if(!$db){
        $response = ["status" => "error", "message" => "Unexpected error occured , Try Later!"];
        echo json_encode($response);
        exit;
    }

    $query = "create table if not exists account(user_id INT AUTO_INCREMENT PRIMARY KEY,profile_pic LONGBLOB,fname varchar(50),lname varchar(50),username VARCHAR(50) UNIQUE,email varchar(50),password VARCHAR(225))";
    if(!($Result = $connect->query($query))){
        $response = ["status" => "error", "message" => "Unexpected error occured , Try Later!"];
        echo json_encode($response);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD']=='POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        $query = "select username,email from account where username like'$username' or email like '$email'";
        $result = $connect->query($query);

        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            if($row['username'] == $username){
                $response = ["status" => "error", "message" => "Username already exists!"];
                echo json_encode($response);
            }else if($row['email'] == $email){
                $response = ["status" => "error", "message" => "Email already exists!"];
                echo json_encode($response);
            }
            //echo "Username or Email already exists!";
            exit;
        }
        $response = ["status" => "success", "message" => "Username and Email are available!"];
        echo json_encode($response);

        $connect->close();
    }
?>