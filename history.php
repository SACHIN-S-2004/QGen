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
        .dropdown {
            position: relative;
            /*z-index: 1000; /* Ensures dropdown stays above other list items */
        }
        .dropdown-menu .form-check {
            padding: 0.5rem 1rem; /* Matches Bootstrap dropdown-item padding */
        }
        .work-item {
            transition: opacity 0.3s ease; /* Optional: Smooth transition for visibility */
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
            fill: #6c757d; /* Gray color for icon, matches Bootstrap placeholder */
        }
        #searchInput:focus {
            border-color: #86b7fe; /* Bootstrap focus color */
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); /* Bootstrap focus shadow */
        }
        #noResults {
            margin: 10px 0;
        }
        .work-item.d-none {
            display: none !important;
        }
    </style>
    </head>
    <body>
        <div id="main-content">
            <?php include 'navBar.php'; ?>
            <div class="max-w-7xl px-4 w-75 py-10 mt-[100px] ml-[200px] me-4 bg-light rounded-1">
                <div id="create-work">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-3xl font-bold text-gray-900 ml-[400px]">Previous Works</h2>
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
                            $connect = new mysqli("localhost","root","","qgen1");
                            if ($connect->connect_error) {
                                die("Connection failed: " . $connect->connect_error);
                            }

                            $query = "SELECT qpool_id, section, name, completed FROM qpool WHERE user_id = '" . $_COOKIE['user_id'] . "' AND `show` = 1 ORDER BY `time` DESC";
                            $Result = $connect->query($query);
                            
                            // Track rendered qpool_ids to detect duplicates
                            $rendered_ids = [];
                            
                            if($Result->num_rows > 0): 
                        ?>
                        <?php while($row = $Result->fetch_assoc()): ?>
                            <?php
                                // Check for duplicate qpool_id
                                if (in_array($row['qpool_id'], $rendered_ids)) {
                                    echo "<!-- Warning: Duplicate qpool_id {$row['qpool_id']} detected -->";
                                    continue; // Skip rendering duplicates
                                }
                                $rendered_ids[] = $row['qpool_id'];
                            ?>
                            <div class="work-item d-flex justify-content-between align-items-center" data-qpool-id="<?php echo $row['qpool_id']; ?>" style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">
                                <p class="work-name">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </p>
                                <div class="d-flex float-end"> 
                                    <button class="btn btn-sm btn-info text-white view-btn" onclick="viewContent(<?php echo $row['section']; ?>, <?php echo $row['qpool_id']; ?>)">
                                        View
                                    </button>
                                    <button class="btn btn-info text-white ml-1 view-btn" onclick="deleteQpool(<?php echo $row['qpool_id']; ?>)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                        </svg>
                                    </button>
                                    <div class="dropdown show">
                                        <button class="btn btn-info text-white ml-1 view-btn" data-bs-toggle="dropdown" aria-expanded="false" style="z-index: 1;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                                <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                            </svg>
                                        </button>
                                        <ul class="dropdown-menu" style="position: absolute; z-index: 1000;">
                                            <li><a class="dropdown-item" onclick="openPage(<?php echo $row['qpool_id']; ?>)">Rename</a></li>
                                            <li>                                          
                                            <div class="form-check d-flex justify-content-between align-items-center">
                                                <label class="form-check-label" for="complete-<?php echo $row['qpool_id']; ?>">Mark as Complete</label>
                                                <input class="form-check-input ms-2" type="checkbox" id="complete-<?php echo $row['qpool_id']; ?>" 
                                                    <?php echo $row['completed'] ? 'checked' : ''; ?> 
                                                    onchange="markAsComplete(<?php echo $row['qpool_id']; ?>, this.checked)">
                                            </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <p>No previous works available.</p>
                        <?php 
                            endif;
                            $connect->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content d-flex flex-column">
                <button type="button" class="btn-close align-self-end m-2" data-bs-dismiss="modal" aria-label="Close" onclick="closePage()"></button>
                <form>
                    <div id="questionPoolContainer" class="d-flex align-items-center justify-content-end p-3">
                        <input type="text" id="poolName" class="form-control me-2" placeholder="Enter New Question Pool Name" required>
                        <button class="btn btn-primary" id="show-tabs-button" onclick="renamePool()">Rename</button>
                    </div>
                </form>
            </div>

            </div>
        </div>
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
                        
                        // Debug: Log current display state
                        console.log(`Current display class: ${item.className}`);
                    });

                    // Toggle no results message
                    noResultsDiv.classList.toggle('d-none', hasResults || searchQuery === '');
                    if (!hasResults && searchQuery !== '') {
                        console.log('No results found');
                    }
                });
            });
            
            function openPage(qpoolID) {
                console.log(`View button clicked for content: ${qpoolID}`);
                document.cookie = `qpool_id=${qpoolID}; path=/; max-age=86400`;
                document.getElementById('main-content').classList.add('blur');
                event.preventDefault();
                var myModal = new bootstrap.Modal(document.getElementById('renameModal'));
                myModal.show();
            }
            function closePage() {
                document.getElementById('main-content').classList.remove('blur');
                var myModalElement = document.getElementById('renameModal');
                var myModal = bootstrap.Modal.getInstance(myModalElement);
                if (!myModal) {
                    myModal = new bootstrap.Modal(myModalElement); // Create instance if it doesn’t exist
                }
                myModal.hide();
            }
            function renamePool() {
                var qpoolID = getCookie("qpool_id");
                var poolName = document.getElementById("poolName").value;
                console.log(`Renaming pool ID: ${qpoolID} to ${poolName}`);
                document.cookie = `poolName=${poolName}; path=/; max-age=86400`;
                fetch(`Qc/deleteQ.php?rename=${qpoolID}`, {
                    method: "POST"
                })
                .then(response => response.text())  
                .then(data => {
                    console.log("Response:", data);                                                          
                    if (data.trim() === "success") {
                        Swal.fire({
                            title: "Renamed!",
                            text: "Your file has been renamed.",
                            icon: "success"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        console.log(`${data}`);
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                });
            }
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            }
            function viewContent(section, qpoolID) {
                console.log(`View button clicked for content: ${qpoolID}`);
                document.cookie = `section=${section}; path=/; max-age=86400`;
                document.cookie = `qpool_id=${qpoolID}; path=/; max-age=86400`;
                console.log(qpoolID);
                console.log(section);
                window.location.href="Qc/questionC.php";
            }

            function deleteQpool(qpoolID) {
                console.log(`Delete button clicked for content: ${qpoolID}`);
                Swal.fire({
                    title: "Proceed to delete?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: "Yes, delete it!",
                    denyButtonText: "Move to Trash",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "You won't be able to revert this!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, delete it!"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`Qc/deleteQ.php?qpool=${qpoolID}`, {
                                    method: "POST"
                                })
                                .then(response => response.text())
                                .then(data => {
                                    console.log("Response:", data);                                                          
                                    if (data.trim() === "success") {
                                        Swal.fire({
                                            title: "Deleted!",
                                            text: "Your file has been deleted.",
                                            icon: "success"
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        console.log(`${data}`);
                                    }
                                })
                                .catch(error => {
                                    console.error("Fetch Error:", error);
                                });
                            }
                        });
                    } else if (result.isDenied) {
                        fetch(`Qc/deleteQ.php?visible=${qpoolID}`, {
                            method: "POST"
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log("Response:", data);                                                          
                            if (data.trim() === "success") {
                                window.location.reload();
                            } else {
                                console.log(`${data}`);
                            }
                        })
                        .catch(error => {
                            console.error("Fetch Error:", error);
                        });
                    }
                });
            }

            function markAsComplete(qpoolID, isChecked) {
                console.log(`Mark as Complete toggled for qpoolID: ${qpoolID}, Checked: ${isChecked}`);
                fetch(`createP/markComplete.php?qpool=${qpoolID}&complete=${isChecked ? 1 : 0}`, {
                    method: "POST"
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "success") {
                        console.log("Marked as complete status updated successfully");
                    } else {
                        console.log("Error updating status:", data);
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                });
            }
        </script>
    </body>
</html>