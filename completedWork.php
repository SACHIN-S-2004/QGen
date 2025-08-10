<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .view-btn {
            transition: transform 0.5s ease;
        }
        .view-btn:hover {
            transform: scale(1.1);
        }
        div.hover-bounce {
            transition: transform 0.3s ease;
        }
        div.hover-bounce:hover {
            transform: translateY(-10px);
        }
        .d-flex.align-items-center.mb-4 {
            width: 100%; /* Ensure full width for centering */
        }
        .text-3xl {
            font-size: 1.875rem;
            line-height: 2.25rem;
        }
        .work-item {
            transition: opacity 0.3s ease; /* Optional: Smooth transition */
        }
        .work-item.d-none {
            display: none !important; /* Ensure hiding works */
        }
        .input-group-text {
            display: flex;
            align-items: center;
        }
        #searchInput {
            border-radius: 5px;
        }
        #searchInput:focus + .input-group-text .bi-search,
        .input-group-text .bi-search {
            fill: #6c757d; /* Gray icon */
        }
        #searchInput:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        #noResults {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div id="main-content">
        <?php include 'navBar.php'; ?>
        <div class="max-w-7xl px-4 w-75 py-10 mt-[100px] ml-[200px] me-4 bg-light rounded-1">
            <div id="create-work">
            <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-3xl font-bold text-gray-900 ml-[400px]">Completed Works</h2>
                        <div class="input-group" style="max-width: 300px;">
                            <span class="input-group-text bg-transparent border-0 pe-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search me-2" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name...">
                        </div>
                    </div>
                <div id="noResults" class="alert alert-warning d-none" role="alert">
                    No works found matching your search.
                </div>
                <div id="questionPoolContainer" class="d-flex flex-column justify-content-between p-2 ml-4">
                    <?php
                        $connect = new mysqli("localhost", "root", "", "qgen1");
                        if ($connect->connect_error) {
                            die("Connection failed: " . $connect->connect_error);
                        }

                        $query = "SELECT qpool_id, section, name, completed FROM qpool WHERE user_id = ? AND `show` = 1 AND `completed` = 1 ORDER BY `time` DESC";
                        $stmt = $connect->prepare($query);
                        $stmt->bind_param("s", $_COOKIE['user_id']);
                        $stmt->execute();
                        $Result = $stmt->get_result();
                        
                        // Track rendered IDs to avoid duplicates
                        $rendered_ids = [];
                        
                        if ($Result->num_rows > 0): 
                    ?>
                    <?php while ($row = $Result->fetch_assoc()): ?>
                        <?php
                            // Skip duplicates
                            if (in_array($row['qpool_id'], $rendered_ids)) {
                                echo "<!-- Warning: Duplicate qpool_id {$row['qpool_id']} detected -->";
                                continue;
                            }
                            $rendered_ids[] = $row['qpool_id'];
                        ?>
                        <div class="work-item d-flex justify-content-between align-items-center" data-qpool-id="<?php echo $row['qpool_id']; ?>" style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">
                            <p class="work-name"><?php echo htmlspecialchars($row['name']); ?></p>
                            <div class="d-flex float-end"> 
                                <a href="createP/select_pattern.php?section=<?php echo $row['section']; ?>&qpool_id=<?php echo $row['qpool_id']; ?>" class="btn btn-sm btn-info text-white view-btn">View</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <p>No finished works available.</p>
                    <?php 
                        endif;
                        $stmt->close();
                        $connect->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const workItems = document.querySelectorAll('.work-item');
        const noResultsDiv = document.getElementById('noResults');

        console.log('Found work items:', workItems.length);
        workItems.forEach(item => {
            console.log('Work item ID:', item.getAttribute('data-qpool-id'), 'Name:', item.querySelector('.work-name').textContent.trim());
        });

        if (!searchInput) {
            console.error('Search input not found!');
            return;
        }

        searchInput.addEventListener('input', function() {
            const searchQuery = this.value.trim().toLowerCase();
            let hasResults = false;

            console.log('Search query:', searchQuery);

            workItems.forEach(item => {
                const workName = item.querySelector('.work-name').textContent.trim().toLowerCase();
                const qpoolId = item.getAttribute('data-qpool-id');
                console.log(`Checking work: ${workName} (ID: ${qpoolId})`);
                
                if (searchQuery === '' || workName.startsWith(searchQuery)) {
                    item.classList.remove('d-none');
                    hasResults = true;
                    console.log(`Showing: ${workName} (ID: ${qpoolId})`);
                } else {
                    item.classList.add('d-none');
                    console.log(`Hiding: ${workName} (ID: ${qpoolId})`);
                }
                
                console.log(`Current display class: ${item.className}`);
            });

            noResultsDiv.classList.toggle('d-none', hasResults || searchQuery === '');
            if (!hasResults && searchQuery !== '') {
                console.log('No results found');
            }
        });
    });
</script>
</html>