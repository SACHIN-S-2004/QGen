<?php
    session_start();
    if ($_SERVER['REQUEST_METHOD']=='POST') {

        $connect = new mysqli("localhost","root","","qgen1");
        if ($connect->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $query = "create table if not exists qpool(user_id INT,qpool_id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),time DATETIME,section CHAR(3),q_amount INT)";
        if(!($Result = $connect->query($query))){
            echo "Error,Table is not Created";
        }
        //$username=$_SESSION['user_id'];
        /*$_SESSION['poolName'] = $_COOKIE[$cookie_name];*/
        $username=$_COOKIE['user_id'];
        $poolName=$_COOKIE['poolName'];
        $show=1;
        $query = "insert into qpool(user_id,name,time,`show`) values('$username','$poolName',NOW(),'$show')";
        $connect->query($query);
        //$query="select qpool_id from qpool where user_id like '$username'";
        //$connect->query($query);
        $qpool_id=$connect->insert_id;
        setcookie('qpool_id', $qpool_id, time() + 86400, '/');
        $_SESSION['qpool_id']=$qpool_id;
        // Output to browser's console
        //echo "<script>console.log('" . $qpool_id . "');</script>";
        //echo "<script>console.log('" . $_COOKIE['qpool_id'] . "');</script>";
        /*$qpool_id=$_SESSION['qpool_id'];*/
        $college = $_POST['college'];
        $exam = $_POST['exam'];
        $course = $_POST['course'];
        $ccode = $_POST['ccode'];
        $duration = $_POST['duration'];
        $marks = $_POST['marks'];

        $query = "CREATE table if not exists metadata(qpool INT,college VARCHAR(255),exName VARCHAR(255) NOT NULL,subname VARCHAR(255) NOT NULL,subcode VARCHAR(255),duration VARCHAR(25) NOT NULL,mark INT NOT NULL)";
        if(!($Result = $connect->query($query))){
            echo "Error,Table is not Created";
        }

        $query = "insert into metadata values('$qpool_id','$college','$exam','$course','$ccode','$duration','$marks')";
        if(!($Result = $connect->query($query))){
            echo "Error";
        }else{
            /*echo "<script>alert('Added Successfully!');</script>";
            echo '<script>window.location.href="question.php";</script>';*/
            echo "success";
        }
    }
    ?>