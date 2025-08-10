<?php

    $connect = new mysqli("localhost", "root", "", "qgen1");
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    
    if (isset($_GET['data'])) {
        $qID=$_COOKIE['qID'];

        $query = "SELECT qpool_id, QType FROM question WHERE Q_id = $qID";
        $result = $connect->query($query);

        if ($result->num_rows > 0) {
            $questionData = $result->fetch_assoc();

            $query = "DELETE FROM question WHERE Q_id = $qID";
            $result = $connect->query($query);
            
            if ($questionData['QType'] === 'sub') {
                $query = "DELETE FROM SQuestion WHERE Q_id = $qID";
                $result = $connect->query($query);
            } elseif ($questionData['QType'] === 'img') {
                $query = "DELETE FROM PQuestion WHERE Q_id = $qID";
                $result = $connect->query($query);
            }
            
            echo "success";
        }
        else{
            
            echo "failed";
        }
    }

    if (isset($_GET['qpool'])) {
        $qpoolID=$_GET['qpool'];

        $query = "DELETE FROM qpool WHERE qpool_id = $qpoolID";
        if ($connect->query($query)) {
            echo "success";
        }
        else{
            echo "failed";
        }
    }

    if (isset($_GET['visible'])) {
        $qpoolID=$_GET['visible'];

        $query = "UPDATE qpool SET `show` = 0, deletion_date = NOW() WHERE qpool_id= $qpoolID";
        //$result = $connect->query($query);

        if ($connect->query($query)) {
            echo "success";
        }
        else{
            echo "failed";
        }
    }

    if (isset($_GET['rename'])) {
        $qpoolID=$_GET['rename'];
        $qpoolName=$_COOKIE['poolName'];
        $query = "UPDATE qpool SET `name` = '$qpoolName' WHERE qpool_id= $qpoolID";
        //$result = $connect->query($query);

        if ($connect->query($query)) {
            echo "success";
        }
        else{
            echo "failed";
        }
    }
    $connect->close();
?>
