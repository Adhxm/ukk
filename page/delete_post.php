<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
    <style>
        .btn:hover {
            background-color: rgba(99, 99, 99, 0.9);
            color: black;
        }

        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
            /* Ensure toast is above other elements */
        }
    </style>
</head>

<body>
    <?php
    include './config/config.php'; // Include database connection

    // Handle delete request
    if (isset($_GET['page']) && $_GET['page'] == 'delete_post' && isset($_GET['postID'])) {
        $postID = intval($_GET['postID']);
        $userID = $_SESSION['userID']; // Get the logged-in user's ID

        // Verify that the post belongs to the logged-in user
        $sql = "SELECT userID FROM post WHERE postID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $postID);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();

        if ($post && $post['userID'] == $userID) {
            // Prepare and execute delete query
            $sql = "DELETE FROM post WHERE postID = ? AND userID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $postID, $userID);

            if ($stmt->execute()) {
                $message = 'Successfully deleted post!';
                $messageType = 'success';
                $redirect = '?page=home';
            } else {
                $message = 'Something went wrong!';
                $messageType = 'error';
                $redirect = '?page=home';
            }
        } else {
            $message = 'You do not have permission to delete this post!';
            $messageType = 'error';
            $redirect = '?page=home';
        }

        $stmt->close();
    }

    // Fetch posts from the database
    $sql = "SELECT postID, img, caption, description, userID FROM post";
    $result = $conn->query($sql);
    ?>

    <div class="container mt-3">
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    $id = htmlspecialchars($row['postID']);
                    $img = htmlspecialchars($row['img']);
                    $cap = htmlspecialchars($row['caption']);
                    $desc = htmlspecialchars($row['description']);
                    $postUserID = htmlspecialchars($row['userID']);
            ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-end">
                                <?php if ($postUserID == $_SESSION['userID']) { ?>
                                    <div class="dropdown">
                                        <button class="btn align-items-center text-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <svg class="bi me-2 opacity-50" width="1rem" height="1rem">
                                                <use href="#hamburger"></use>
                                            </svg>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" href="?page=delete_post&postID=<?php echo $id; ?>">Delete Post</a></li>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                            <img src="./uploads/<?php echo $img; ?>" class="card-img-top" alt="...">
                            <div class="card-body">
                                <p class="card-text bold-text h6"><?php echo $cap; ?></p>
                                <p class="card-text"><?php echo $desc; ?></p>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <p>No posts found</p>
            <?php
            }
            $conn->close();
            ?>
        </div>
    </div>

    <!-- Toast Container -->
    <?php if (isset($message)): ?>
        <div class="toast-container">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto"><?php echo ucfirst($messageType); ?></strong>
                    <small>1 min ago</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <?php echo $message; ?>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var toastContainer = document.querySelector('.toast-container');
                var toastElList = [].slice.call(document.querySelectorAll('.toast'));

                toastElList.forEach(function(toastEl) {
                    // Pindahkan toast ke dalam kontainer
                    toastContainer.appendChild(toastEl);
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });

                // Redirect after showing toast
                if (<?php echo json_encode($redirect); ?>) {
                    setTimeout(function() {
                        window.location.href = <?php echo json_encode($redirect); ?>;
                    }, 2000); // Wait for 2 seconds before redirecting
                }
            });
        </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>

</html>