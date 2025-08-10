<!DOCTYPE html>
<html>
<head>
    <title>Question Paper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: 21cm 30cm; 
            margin: 1cm; 
        }
        body { 
            width: 21cm; 
            min-height: 30cm; 
            margin: 0 auto; 
            padding: 1cm; 
            box-sizing: border-box; 
        }
        .header-section { 
            margin-bottom: 20px; 
        }
        .course-details { 
            margin-bottom: 20px; 
        }
        .section { 
            margin-top: 20px; 
        }
        .section-title {
            background-color: rgb(249, 249, 249);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }
        .question { 
            margin-bottom: 20px; 
            page-break-inside: avoid; 
        }
        .sub-question { 
            margin-left: 20px; 
            margin-top: 5px; 
        }
        .question-image { 
            max-width: 50%; 
            height: auto; 
            margin-top: 10px; 
            margin-bottom: 10px; 
            margin-left: 30px; 
        }
        .or-divider {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            page-break-inside: avoid;
        }
        @media print { 
            body { padding: 0; margin: 0; } 
            .container { width: 100%; max-width: 21cm; border: none !important; } 
            #printButton { display: none; }
            #homeButton { display: none; }
            #warning { display: none; } 
        }
    </style>
</head>
<body>
    <div class="container border border-warning">
        <button id="printButton" class="btn btn-secondary m-2 mb-3">Print Page</button>
        <button id="homeButton" class="btn btn-info m-2 mb-3" onclick="window.location.href='../homepage.php'">Go Back To Home</button>

        <?php
        $conn = new mysqli("localhost", "root", "", "qgen1");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $pattern_id = $_POST['pattern_id'] ?? null;
        $chapter_pattern_id = $_POST['chapter_pattern_id'] ?? null;
        $qpool_id = $_POST['qpool_id'] ?? $_COOKIE['qpool_id'];
        if (!$pattern_id || !$chapter_pattern_id) {
            die("Error: Pattern or chapter pattern not selected.");
        }

        $metadata_query = "SELECT * FROM metadata WHERE qpool = $qpool_id LIMIT 1";
        $metadata_result = $conn->query($metadata_query);
        $metadata = $metadata_result->fetch_assoc();
        ?>

        <div class="header-section">
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        Roll No : _______________________<br>
                        Name    : ______________________
                    </div>
                </div>
            </div>
            <div class="text-center mb-4 mt-4">
                <h6 class="fw-bold" style="margin-right:10%;"><?php echo $metadata['college']; ?></h6>
                <h6 class="fw-bold"><?php echo $metadata['exName']; ?></h6>
            </div>
            <div class="course-details">
                <div class="mb-2">
                    <p><strong>Course Code:</strong> <?php echo $metadata['subcode']; ?></p>
                    <p><strong>Course Name:</strong> <?php echo $metadata['subname']; ?></p>
                </div>
                <div class="row">
                    <div class="col-6">
                        <p><strong>Duration:</strong> <?php echo $metadata['duration']; ?> Hours</p>
                    </div>
                    <div class="col-6 text-end">
                        <p><strong>Marks:</strong> <?php echo $metadata['mark']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $section_details_query = "SELECT section, easy, med, hard, qNum FROM section_details WHERE p_id = $pattern_id";
        $section_details_result = $conn->query($section_details_query);

        $chapter_details_query = "SELECT section, or_allowed FROM chapter_details WHERE p_id = $chapter_pattern_id";
        $chapter_details_result = $conn->query($chapter_details_query);
        $or_allowed_sections = [];
        while ($chapter_row = $chapter_details_result->fetch_assoc()) {
            $or_allowed_sections[$chapter_row['section']] = $chapter_row['or_allowed'];
        }

        $chapter_allowed_query = "SELECT section, GROUP_CONCAT(chap1 + chap2 + chap3 + chap4 + chap5 + chap6 + chap7 + chap8) as chapters 
                                 FROM chapter_details 
                                 WHERE p_id = $chapter_pattern_id 
                                 GROUP BY section";
        $chapter_allowed_result = $conn->query($chapter_allowed_query);
        $chapter_allowed = [];
        while ($row = $chapter_allowed_result->fetch_assoc()) {
            $section = $row['section'];
            $chapter_allowed[$section] = [];
            for ($i = 1; $i <= 8; $i++) {
                $chap_field = "chap$i";
                $chap_value = $conn->query("SELECT $chap_field FROM chapter_details WHERE p_id = $chapter_pattern_id AND section = '$section'")->fetch_assoc()[$chap_field];
                if ($chap_value) {
                    $chapter_allowed[$section][] = $i;
                }
            }
        }

        $count = 1;

        while ($section_detail = $section_details_result->fetch_assoc()) {
            $section = $section_detail['section'];
            echo "<div class='section'>";

            $easy_count = $section_detail['easy'];
            $med_count = $section_detail['med'];
            $hard_count = $section_detail['hard'];
            $total_qNum = $section_detail['qNum'];

            echo "<!-- Debug: Section $section, Total qNum = $total_qNum, Easy = $easy_count, Med = $med_count, Hard = $hard_count -->";

            $allowed_chapters = $chapter_allowed[$section] ?? [];
            if (empty($allowed_chapters)) {
                echo "<p class='text-danger'>Error: No chapters specified for Part $section.</p>";
                echo "</div>";
                continue;
            }

            $chapter_count = count($allowed_chapters);
            $base_q_per_chapter = floor($total_qNum / $chapter_count);
            $remainder = $total_qNum % $chapter_count;
            $questions_per_chapter = array_fill(0, $chapter_count, $base_q_per_chapter);
            for ($i = 0; $i < $remainder; $i++) {
                $questions_per_chapter[$i]++;
            }

            $chapter_list = implode(',', $allowed_chapters);
            $available_query = "
                SELECT difficulty, chapter, COUNT(*) as count 
                FROM question 
                WHERE qpool_id = $qpool_id AND section = '$section' AND chapter IN ($chapter_list)
                GROUP BY difficulty, chapter";
            $available_result = $conn->query($available_query);
            $available = ['easy' => [], 'medium' => [], 'hard' => []];
            $total_available = 0;
            while ($row = $available_result->fetch_assoc()) {
                $available[strtolower($row['difficulty'])][$row['chapter']] = $row['count'];
                $total_available += $row['count'];
            }

            echo "<!-- Debug: Total available questions = $total_available -->";

            if ($total_available < $total_qNum) {
                echo "<p class='text-danger'>Error: Only $total_available questions available for Part $section in allowed chapters, but $total_qNum are required.</p>";
                echo "</div>";
                continue;
            }

            $warnings = [];
            $chapter_queries = [];
            $remaining_easy = $easy_count;
            $remaining_med = $med_count;
            $remaining_hard = $hard_count;

            foreach ($allowed_chapters as $idx => $chapter) {
                $q_needed = $questions_per_chapter[$idx];
                $easy_available = $available['easy'][$chapter] ?? 0;
                $med_available = $available['medium'][$chapter] ?? 0;
                $hard_available = $available['hard'][$chapter] ?? 0;

                $easy_per_chapter = min(floor($easy_count / $chapter_count), $easy_available);
                if ($idx < $easy_count % $chapter_count) $easy_per_chapter++;
                $easy_per_chapter = min($easy_per_chapter, $remaining_easy, $q_needed);
                $remaining_easy -= $easy_per_chapter;

                $med_per_chapter = min(floor($med_count / $chapter_count), $med_available);
                if ($idx < $med_count % $chapter_count) $med_per_chapter++;
                $med_per_chapter = min($med_per_chapter, $remaining_med, $q_needed - $easy_per_chapter);
                $remaining_med -= $med_per_chapter;

                $hard_per_chapter = min(floor($hard_count / $chapter_count), $hard_available);
                if ($idx < $hard_count % $chapter_count) $hard_per_chapter++;
                $hard_per_chapter = min($hard_per_chapter, $remaining_hard, $q_needed - $easy_per_chapter - $med_per_chapter);
                $remaining_hard -= $hard_per_chapter;

                $total_per_chapter = $easy_per_chapter + $med_per_chapter + $hard_per_chapter;

                if ($total_per_chapter < $q_needed) {
                    $extra_needed = $q_needed - $total_per_chapter;
                    $extra_hard = min($extra_needed, max(0, $hard_available - $hard_per_chapter), $remaining_hard);
                    $hard_per_chapter += $extra_hard;
                    $remaining_hard -= $extra_hard;
                    $extra_needed -= $extra_hard;

                    if ($extra_needed > 0) {
                        $extra_med = min($extra_needed, max(0, $med_available - $med_per_chapter), $remaining_med);
                        $med_per_chapter += $extra_med;
                        $remaining_med -= $extra_med;
                        $extra_needed -= $extra_med;
                    }

                    if ($extra_needed > 0) {
                        $extra_easy = min($extra_needed, max(0, $easy_available - $easy_per_chapter), $remaining_easy);
                        $easy_per_chapter += $extra_easy;
                        $remaining_easy -= $extra_easy;
                        $extra_needed -= $extra_easy;
                    }

                    if ($extra_needed > 0) {
                        $warnings[] = "Could not meet full $q_needed questions for Chapter $chapter in Part $section; shortfall: $extra_needed";
                    }
                } elseif ($total_per_chapter > $q_needed) {
                    $excess = $total_per_chapter - $q_needed;
                    if ($hard_per_chapter > 0) {
                        $reduce_hard = min($excess, $hard_per_chapter);
                        $hard_per_chapter -= $reduce_hard;
                        $remaining_hard += $reduce_hard;
                        $excess -= $reduce_hard;
                    }
                    if ($excess > 0 && $med_per_chapter > 0) {
                        $reduce_med = min($excess, $med_per_chapter);
                        $med_per_chapter -= $reduce_med;
                        $remaining_med += $reduce_med;
                        $excess -= $reduce_med;
                    }
                    if ($excess > 0 && $easy_per_chapter > 0) {
                        $reduce_easy = min($excess, $easy_per_chapter);
                        $easy_per_chapter -= $reduce_easy;
                        $remaining_easy += $reduce_easy;
                    }
                }

                echo "<!-- Debug: Chapter $chapter, Needed = $q_needed, Easy = $easy_per_chapter, Med = $med_per_chapter, Hard = $hard_per_chapter -->";

                $chapter_queries[] = "
                    (SELECT * FROM question 
                     WHERE qpool_id = $qpool_id AND section = '$section' AND chapter = $chapter AND difficulty = 'easy' 
                     ORDER BY RAND() LIMIT $easy_per_chapter)
                    UNION
                    (SELECT * FROM question 
                     WHERE qpool_id = $qpool_id AND section = '$section' AND chapter = $chapter AND difficulty = 'medium' 
                     ORDER BY RAND() LIMIT $med_per_chapter)
                    UNION
                    (SELECT * FROM question 
                     WHERE qpool_id = $qpool_id AND section = '$section' AND chapter = $chapter AND difficulty = 'hard' 
                     ORDER BY RAND() LIMIT $hard_per_chapter)";
            }

            $question_query = implode(' UNION ', $chapter_queries) . " ORDER BY chapter";
            $question_result = $conn->query($question_query) or die($conn->error);

            if ($question_result->num_rows > 0) {
                if (!empty($warnings)) {
                    echo "<p id='warning' class='text-warning'>Warning for Part $section: " . implode("; ", $warnings) . "</p>";
                }

                echo "<!-- Debug: Fetched " . $question_result->num_rows . " questions -->";

                $first_question = true;
                $current_chapter = null;
                $chapter_question_count = []; // Track questions per chapter
                $or_allowed = $or_allowed_sections[$section] ?? 0;

                // Pre-calculate question counts per chapter for "OR" logic
                $question_result->data_seek(0);
                while ($row = $question_result->fetch_assoc()) {
                    $chapter = $row['chapter'];
                    $chapter_question_count[$chapter] = ($chapter_question_count[$chapter] ?? 0) + 1;
                }
                $question_result->data_seek(0);

                echo "<div>";
                while ($question = $question_result->fetch_assoc()) {
                    $chapter = $question['chapter'];
                    if ($current_chapter !== $chapter) {
                        $current_chapter = $chapter;
                        $questions_displayed_in_chapter = 0;
                    }

                    if (!$first_question && $or_allowed && $questions_displayed_in_chapter > 0 && $questions_displayed_in_chapter < $chapter_question_count[$chapter]) {
                        echo "<div class='or-divider'>OR</div>";
                    }

                    if ($first_question) {
                        echo "<div class='title-first-question'>";
                        echo "<h5 class='section-title'>PART $section</h5>";
                        echo "<div class='list-group-item question'>";
                        echo "$count. " . $question['content'];
                        echo " <span class='float-end'>[" . $question['mark'] . " marks]</span>";
                        if ($question['QType'] == 'sub') {
                            $sub_query = "SELECT content FROM squestion WHERE qpool_id = $qpool_id AND Q_id = " . $question['Q_id'];
                            $sub_result = $conn->query($sub_query);
                            if ($sub_result->num_rows > 0) {
                                $sub_count = 'a';
                                while ($sub_question = $sub_result->fetch_assoc()) {
                                    echo "<div class='sub-question'>$sub_count) " . $sub_question['content'] . "</div>";
                                    $sub_count++;
                                }
                            }
                        } elseif ($question['QType'] == 'img') {
                            $img_query = "SELECT picture FROM pquestion WHERE qpool_id = $qpool_id AND Q_id = " . $question['Q_id'] . " LIMIT 1";
                            $img_result = $conn->query($img_query);
                            if ($img_result->num_rows > 0) {
                                $img = $img_result->fetch_assoc();
                                $img_data = base64_encode($img['picture']);
                                echo "<br><img src='data:image/jpeg;base64,$img_data' class='question-image' alt='Question Image'>";
                            }
                        }
                        echo "</div>";
                        echo "</div>";
                        $first_question = false;
                    } else {
                        echo "<div class='list-group-item question'>";
                        echo "$count. " . $question['content'];
                        echo " <span class='float-end'>[" . $question['mark'] . " marks]</span>";
                        if ($question['QType'] == 'sub') {
                            $sub_query = "SELECT content FROM squestion WHERE qpool_id = $qpool_id AND Q_id = " . $question['Q_id'];
                            $sub_result = $conn->query($sub_query);
                            if ($sub_result->num_rows > 0) {
                                $sub_count = 'a';
                                while ($sub_question = $sub_result->fetch_assoc()) {
                                    echo "<div class='sub-question'>$sub_count) " . $sub_question['content'] . "</div>";
                                    $sub_count++;
                                }
                            }
                        } elseif ($question['QType'] == 'img') {
                            $img_query = "SELECT picture FROM pquestion WHERE qpool_id = $qpool_id AND Q_id = " . $question['Q_id'] . " LIMIT 1";
                            $img_result = $conn->query($img_query);
                            if ($img_result->num_rows > 0) {
                                $img = $img_result->fetch_assoc();
                                $img_data = base64_encode($img['picture']);
                                echo "<br><img src='data:image/jpeg;base64,$img_data' class='question-image' alt='Question Image'>";
                            }
                        }
                        echo "</div>";
                    }
                    $questions_displayed_in_chapter++;
                    $count++;
                }
                echo "</div>";
            } else {
                echo "<p class='text-danger'>Error: No questions fetched for Part $section in allowed chapters.</p>";
            }
            echo "</div>";
        }
        $conn->close();
        ?>
    </div>

    <script>
        document.querySelector("#printButton").addEventListener("click", () => { window.print(); });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>