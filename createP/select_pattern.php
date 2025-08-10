<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Pattern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .view-btn {
            transition: transform 0.5s ease;
        }
        .view-btn:hover {
            transform: scale(1.1);
        }
        .container {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
        }
        #chapterIframe, #patternIframe {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 400px;
            border: none;
            z-index: 1060;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        #iframeBackdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1055;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Patterns for Question Paper</h2>
        <?php
        $conn = new mysqli("localhost", "root", "", "qgen1");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $qpool_id = isset($_GET['qpool_id']) ? $_GET['qpool_id'] : 31;
        $section_count_query = "SELECT COUNT(DISTINCT section) as sec_count FROM question WHERE qpool_id = $qpool_id";
        $sec_count_result = $conn->query($section_count_query);
        $sec_count = $sec_count_result->fetch_assoc()['sec_count'];
        ?>

        <form method="POST" action="generate_paper.php" id="patternForm">
            <!-- Chapter Selection -->
            <div class="mb-4">
                <h4>Chapter Pattern</h4>
                <div class="mb-3">
                    <label for="chapter_pattern" class="form-label">Choose a Chapter Pattern:</label>
                    <select name="chapter_pattern_id" id="chapter_pattern" class="form-select" required>
                        <option value="" hidden>-- Select a Chapter Pattern --</option>
                        <?php
                        $chapter_query = "SELECT * FROM chapter_patterns WHERE sec_num = $sec_count";
                        $chapter_result = $conn->query($chapter_query);
                        while ($chapter = $chapter_result->fetch_assoc()) {
                            echo "<option value='" . $chapter['p_id'] . "'>" . $chapter['p_name'] . " (" . $chapter['sec_num'] . " sections)</option>";
                        }
                        ?>
                    </select>
                </div>
                <div id="chapterPreview" class="mb-3" style="display: none;">
                    <p><strong>Selected Chapter Pattern:</strong> <span id="chapterName"></span></p>
                    <button type="button" class="btn btn-info view-btn" id="viewChapterBtn">View Chapter Pattern</button>
                </div>
            </div>

            <!-- Difficulty Selection -->
            <div class="mb-4">
                <h4>Difficulty Pattern</h4>
                <div class="mb-3">
                    <label for="pattern" class="form-label">Choose a Difficulty Pattern:</label>
                    <select name="pattern_id" id="pattern" class="form-select" required>
                        <option value="" hidden>-- Select a Difficulty Pattern --</option>
                        <?php
                        $pattern_query = "SELECT * FROM patterns WHERE sec_num = $sec_count";
                        $pattern_result = $conn->query($pattern_query);
                        while ($pattern = $pattern_result->fetch_assoc()) {
                            echo "<option value='" . $pattern['p_id'] . "'>" . $pattern['p_name'] . " (" . $pattern['sec_num'] . " sections)</option>";
                        }
                        ?>
                    </select>
                </div>
                <div id="patternPreview" class="mb-3" style="display: none;">
                    <p><strong>Selected Difficulty Pattern:</strong> <span id="patternName"></span></p>
                    <button type="button" class="btn btn-info view-btn" id="viewPatternBtn">View Difficulty Pattern</button>
                </div>
            </div>

            <input type="hidden" name="qpool_id" value="<?php echo $qpool_id; ?>">
            <button type="submit" class="btn btn-primary">Generate Question Paper</button>
        </form>

        <?php $conn->close(); ?>
    </div>

    <!-- Iframe for Previews -->
    <div id="iframeBackdrop"></div>
    <iframe id="chapterIframe" style="width:1000px; height:300px;"></iframe>
    <iframe id="patternIframe" style="height:300px;"></iframe>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chapterSelect = document.getElementById('chapter_pattern');
            const chapterPreview = document.getElementById('chapterPreview');
            const chapterNameSpan = document.getElementById('chapterName');
            const viewChapterBtn = document.getElementById('viewChapterBtn');

            const patternSelect = document.getElementById('pattern');
            const patternPreview = document.getElementById('patternPreview');
            const patternNameSpan = document.getElementById('patternName');
            const viewPatternBtn = document.getElementById('viewPatternBtn');

            const chapterIframe = document.getElementById('chapterIframe');
            const patternIframe = document.getElementById('patternIframe');
            const iframeBackdrop = document.getElementById('iframeBackdrop');

            // Chapter Pattern Selection
            chapterSelect.addEventListener('change', function() {
                if (this.value) {
                    chapterPreview.style.display = 'block';
                    chapterNameSpan.textContent = this.options[this.selectedIndex].text;
                } else {
                    chapterPreview.style.display = 'none';
                }
            });

            viewChapterBtn.addEventListener('click', function() {
                const chapterId = chapterSelect.value;
                if (chapterId) {
                    chapterIframe.src = `get_chapter_details.php?chapter_pattern_id=${chapterId}&qpool_id=<?php echo $qpool_id; ?>`;
                    chapterIframe.style.display = 'block';
                    iframeBackdrop.style.display = 'block';
                }
            });

            // Difficulty Pattern Selection
            patternSelect.addEventListener('change', function() {
                if (this.value) {
                    patternPreview.style.display = 'block';
                    patternNameSpan.textContent = this.options[this.selectedIndex].text;
                } else {
                    patternPreview.style.display = 'none';
                }
            });

            viewPatternBtn.addEventListener('click', function() {
                const patternId = patternSelect.value;
                if (patternId) {
                    patternIframe.src = `get_pattern_details.php?pattern_id=${patternId}&qpool_id=<?php echo $qpool_id; ?>`;
                    patternIframe.style.display = 'block';
                    iframeBackdrop.style.display = 'block';
                }
            });

            // Close iframe when clicking backdrop
            iframeBackdrop.addEventListener('click', function() {
                chapterIframe.style.display = 'none';
                patternIframe.style.display = 'none';
                iframeBackdrop.style.display = 'none';
                chapterIframe.src = '';
                patternIframe.src = '';
            });

            // Listen for close message from iframe
            window.addEventListener('message', function(event) {
                if (event.data === 'closeIframe') {
                    chapterIframe.style.display = 'none';
                    patternIframe.style.display = 'none';
                    iframeBackdrop.style.display = 'none';
                    chapterIframe.src = '';
                    patternIframe.src = '';
                }
            });
        });
    </script>
</body>
</html>