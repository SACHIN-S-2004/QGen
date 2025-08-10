<?php
        $connect = new mysqli("localhost","root","","qgen1");
        if ($connect->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        /*$query = "create database if not exists qgen1";
        if(!($Result = $connect->query($query))){
            echo "Error,Database is not Created";
            exit;
        }

        $db = mysqli_select_db($connect,"qgen1");
        if(!$db){
            echo "Error,Couldn't Select the database";
            exit;
        }

        $query = "create table if not exists account(user_id INT AUTO_INCREMENT PRIMARY KEY,profile_pic LONGBLOB,fname varchar(50),lname varchar(50),username VARCHAR(50) UNIQUE,email varchar(50),password VARCHAR(225))";
        if(!($Result = $connect->query($query))){
            echo "Error,Table is not Created";
            exit;
        }*/

        if ($_SERVER['REQUEST_METHOD']=='POST') {

            /*$username = trim($_POST['username']);
            $email = trim($_POST['email']);

            $query = "select username,email from account where username like'$username' or email like '$email'";
            $result = $connect->query($query);

            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                if($row['username'] == $username){
                    echo "Username already exists!";
                }else if($row['email'] == $email){
                    echo "Email already exists!";
                }
                //echo "Username or Email already exists!";
                exit;
            }*/
            /*$query = "insert into account(username) values('$username')";
            try{
                $result = $connect->query($query);
            }
            catch(mysqli_sql_exception $e) {
                if ($e->getCode() ==1062) {
                    /*$_SESSION['form_data'] = $_POST;
                    header("Location: Registration.php?status=1");
                    exit();
                    echo "Username already exists!";
                    exit;
                }                
            } */
            //include 'otp.php';
           //$email = $_POST['email'];
           //$_SESSION['email'] = $email;

            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if (isset($_FILES['profile_pic']) && is_uploaded_file($_FILES['profile_pic']['tmp_name'])) {
                if (!($image = addslashes(file_get_contents($_FILES['profile_pic']['tmp_name'])))) {
                    echo "Missing Profile Pic";
                }
                $query="INSERT INTO account(profile_pic,fname,lname,username,email,password) VALUES('$image','$fname','$lname','$username','$email','$pass')";
            } else{
                $query="INSERT INTO account(fname,lname,username,email,password) VALUES('$fname','$lname','$username','$email','$pass')";
            }    

                /*$query = "UPDATE account SET profile_pic = '$image', fname = '$fname', lname = '$lname', email = '$email', password = '$pass' WHERE username = '$username'";
            } else{
                $query = "UPDATE account SET fname = '$fname', lname = '$lname', email = '$email', password = '$pass' WHERE username = '$username'";
           */

            //header("Location: otp.php");

            if(!($Result = $connect->query($query))){
                //header("Location: Registration.php?status=0");
                //exit();
                echo "Account Registration Failed! Try Again Later";
            }else{
                //header("Location: Registration.php?status=2");
                //exit();
                echo "success";
            }
            $connect->close();
        }
    ?>