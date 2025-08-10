<!-- recycle_bin.html remains largely the same, just updating the PHP include -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recycle Bin - Question Pools</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead {
            background-color: #0d6efd;
            color: white;
        }
        .btn-action {
            margin: 0 5px;
        }
        .remaining-time {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mt-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h2 class="mb-0">Recycle Bin - Question Pools</h2>
                <button type="button" class="btn-close btn-close-white float-end mt-2 color-white" aria-label="Close" onclick="window.location.href='../homepage.php'"></button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Created On</th>
                                <th scope="col">Deletion Date</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="trashTable">
                            <?php include 'fetch_trash.php'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        async function restoreQuestionPool(qpool_id) {
            const result = await Swal.fire({
                title: 'Restore Question Pool?',
                text: "Do you want to restore this question pool?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, restore it!'
            });

            if (result.isConfirmed) {
                fetch('process_trash.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=restore&qpool_id=${qpool_id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("haiii");
                        Swal.fire({
                            icon: 'success',
                            title: 'Restored!',
                            text: 'The question pool has been restored.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error!', data.error || 'Something went wrong', 'error');
                    }
                });
            }
        }

        async function deleteQuestionPool(qpool_id) {
            const result = await Swal.fire({
                title: 'Permanently Delete?',
                text: "This will permanently delete the question pool and all related data!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#0d6efd',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                fetch('process_trash.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&qpool_id=${qpool_id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'The question pool has been permanently deleted.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error!', data.error || 'Something went wrong', 'error');
                    }
                });
            }
        }
    </script>
</body>
</html>