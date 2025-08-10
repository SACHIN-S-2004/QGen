<?php
    $connect = new mysqli("localhost", "root", "", "qgen1");
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['name']);
        $pass = trim($_POST['pass']);

        try{
            $stmt = $connect->prepare("SELECT user_id, username, password FROM account WHERE username = ? OR email = ?");
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1146) {
                echo "Invalid Username!";  // Send error message
            } else {
                echo "Unexpected error occured! Try Later";  // Send error message
            }
        }
        
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $stmt->bind_result($user_id,$usrname, $db_pass);

        if ($stmt->fetch()) {
            if (password_verify($pass, $db_pass)) {
                //session_start();
                //$_SESSION['username'] = $usrname;
                setcookie('username', $usrname, time() + (86400 * 30), "/");
                // Set a cookie
                setcookie('user_id', $user_id, time() + (86400 * 30), "/"); // Cookie valid for 30 days
                echo "success";  // Send 'success' response to JavaScript
            } else {
                echo "Incorrect Password!";  // Send error message
            }
        } else {
            echo "Invalid Username!";  // Send error message
        }

        $stmt->close();
        $connect->close();
    }
?>
