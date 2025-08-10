    <?php
    session_start();
    $connect = new mysqli("localhost", "root", "", "qgen1");

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    if (isset($_GET['qpool_id'])) {
        $qpool_id = $_GET['qpool_id']; // Assign the actual value, not the isset() result.
        //error_log($qpool_id);
        //echo "<script>console.log('" . $qpool_id . "');</script>";
        // Fixing the single quotes issue in the SQL query
        $query = "SELECT section FROM qpool WHERE qpool_id = '" . $qpool_id . "'";
        $result = $connect->query($query);

        if ($result) {
            $row = $result->fetch_assoc();
            if (is_null($row['section'])) {
                echo "failure";
            } else {
                echo "success";
                setcookie('section', $row['section'], time() + 3600, '/');
            }
        } else {
            // Optional: handle query failure
            echo "query failed";
        }
    }

    if (isset($_GET['data'])) {
        $data = $_GET['data']; // Assign the actual value, not the isset() result.
        $tchapter = $_GET['chapters'];
        // Fixing the single quotes issue in the SQL query
        $query = "UPDATE qpool SET section = '$data', tchapter  = '$tchapter'  WHERE qpool_id = '" . $_COOKIE['qpool_id'] . "'";
        $result = $connect->query($query);
    }

    if (isset($_GET['section'])) {
        $section = isset($_GET['section']) ? (int)$_GET['section'] : 0;
        /*if (isset($_GET['data'])) {
            echo "<script>console.log('yes');</script>";
            error_log("Hello, this is a log message!1");
        }else{
            echo "<script>console.log('no');</script>";
            error_log("Hello, this is a log message!2");
        }
        $data = isset($_GET['data']) ;
        echo "<script>console.log(" .$data. ");</script>";*/

        if($section==1){
            $section="A";
        }
        else if($section==2){
            $section="B";
        }
        else if($section==3){
            $section="C";
        }
        else if($section==4){
            $section="D";
        }
        else if($section==5){
            $section="E";
        }

        $query = "SELECT Q_id,content FROM question WHERE section = '$section' and qpool_id = '" . $_COOKIE['qpool_id'] . "'";
        $result = $connect->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $content = $row['content'];
                $qID = $row['Q_id'];
                echo "<div class='view-btn1' style='border:1px solid #ddd; padding:10px; margin:10px 0; display:flex; justify-content:space-between; align-items:center;'>
                        <p style='margin:0; width:90%; max-height:3em; line-height:1.5em; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;'>$content</p>
                        <button class='btn btn-sm btn-info text-white view-btn' onclick=\"viewContent($qID)\">View</button>
                    </div>";
            }
        } else {
            echo "<p>No previous works available.</p>";
        }
    }
    $connect->close();
?>