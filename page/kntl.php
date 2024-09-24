<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link rel="stylesheet" href="./bootstrap/css/popupimg.css">
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
            /* Pastikan toast berada di atas elemen lain */
        }
    </style>
</head>

<body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="hamburger" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
        </symbol>
        <symbol id="like" viewBox="0 0 16 16">
            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" />
        </symbol>
        <symbol id="send" viewBox="0 0 16 16">
            <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z" />
        </symbol>
        <symbol id="like-fill" viewBox="0 0 16 16">
            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1z" />
        </symbol>
        <symbol id="bookmark" viewBox="0 0 16 16">
            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1z" />
        </symbol>
        <symbol id="trash" viewBox="0 0 16 16">
            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
        </symbol>
    </svg>
    <div class="container mt-3">
        <div class="row">
            <?php
            include './config/config.php'; // Include database connection


            // Check if user is logged in
            if (isset($_SESSION['userID'])) {
                $userID = $_SESSION['userID'];

                // Check if like button is clicked
                if (isset($_POST['like'])) {
                    $postID = $_POST['postID'];

                    // Check if the user has already liked this post
                    $check_like_sql = "SELECT * FROM likes WHERE userID = ? AND postID = ?";
                    $stmt = $conn->prepare($check_like_sql);
                    $stmt->bind_param("ii", $userID, $postID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 0) {
                        // User has not liked the post yet, so add a like
                        $conn->begin_transaction(); // Start transaction

                        try {
                            // Insert like into likes table
                            $insert_like_sql = "INSERT INTO likes (userID, postID) VALUES (?, ?)";
                            $stmt = $conn->prepare($insert_like_sql);
                            $stmt->bind_param("ii", $userID, $postID);
                            $stmt->execute();

                            // Update the likes count for the post
                            $update_likes_sql = "UPDATE post SET likes = likes + 1 WHERE postID = ?";
                            $stmt = $conn->prepare($update_likes_sql);
                            $stmt->bind_param("i", $postID);
                            $stmt->execute();

                            $conn->commit(); // Commit transaction
                        } catch (Exception $e) {
                            $conn->rollback(); // Rollback if any error occurs
                            echo "Error: " . $e->getMessage();
                        }
                    } else {
                        // User has already liked the post, so remove the like
                        $conn->begin_transaction(); // Start transaction

                        try {
                            // Delete like from likes table
                            $delete_like_sql = "DELETE FROM likes WHERE userID = ? AND postID = ?";
                            $stmt = $conn->prepare($delete_like_sql);
                            $stmt->bind_param("ii", $userID, $postID);
                            $stmt->execute();

                            // Update the likes count for the post
                            $update_likes_sql = "UPDATE post SET likes = likes - 1 WHERE postID = ?";
                            $stmt = $conn->prepare($update_likes_sql);
                            $stmt->bind_param("i", $postID);
                            $stmt->execute();

                            $conn->commit(); // Commit transaction
                        } catch (Exception $e) {
                            $conn->rollback(); // Rollback if any error occurs
                            echo "Error: " . $e->getMessage();
                        }
                    }

                    $stmt->close();
                }
            } else {
                // If user is not logged in and tries to like
                if (isset($_POST['like'])) {
            ?>
                    <div class="toast-container">
                        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <strong class="me-auto">Error</strong>
                                <small>1 mins ago</small>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                You must be logged in to like posts.
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            // Handle Bookmark
            if (isset($_POST['bookmark'])) {
                $postID = $_POST['postID'];

                // Check if the post is already bookmarked by the user
                $check_bookmark_sql = "SELECT * FROM collection WHERE userID = ? AND postID = ?";
                $stmt = $conn->prepare($check_bookmark_sql);
                $stmt->bind_param("ii", $userID, $postID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    // User has not bookmarked the post yet, so add to collection
                    $insert_bookmark_sql = "INSERT INTO collection (userID, postID) VALUES (?, ?)";
                    $stmt = $conn->prepare($insert_bookmark_sql);
                    $stmt->bind_param("ii", $userID, $postID);
                    if ($stmt->execute()) {
                        // Bookmark added successfully
                        echo "<div class='toast-container'>
                    <div class='toast' role='alert'>
                      <div class='toast-header'>
                        <strong class='me-auto'>Success</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='toast'></button>
                      </div>
                      <div class='toast-body'>Post has been bookmarked.</div>
                    </div>
                  </div>";
                    }
                } else {
                    // User has already bookmarked the post, so remove from collection
                    $delete_bookmark_sql = "DELETE FROM collection WHERE userID = ? AND postID = ?";
                    $stmt = $conn->prepare($delete_bookmark_sql);
                    $stmt->bind_param("ii", $userID, $postID);
                    if ($stmt->execute()) {
                        // Bookmark removed successfully
                        echo "<div class='toast-container'>
                    <div class='toast' role='alert'>
                      <div class='toast-header'>
                        <strong class='me-auto'>Success</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='toast'></button>
                      </div>
                      <div class='toast-body'>Post has been removed from bookmarks.</div>
                    </div>
                  </div>";
                    }
                }

                $stmt->close();
            }
            // Fetch posts from the database
            $sql = "SELECT p.postID, p.created, p.img, p.caption, p.description, p.likes, u.email 
                FROM post p
                JOIN user u ON p.userID = u.userID";
            $result = $conn->query($sql);
            ?>

            <div class="container mt-3">
                <?php
                if (isset($error_message)) {
                    echo "<div class='alert alert-warning'>$error_message</div>";
                }
                ?>
                <div class="row">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {

                            $userID = htmlspecialchars($row['email']);
                            $postID = htmlspecialchars($row['postID']);
                            $img = htmlspecialchars($row['img']);
                            $created = htmlspecialchars($row['created']);
                            $caption = htmlspecialchars($row['caption']);
                            $description = htmlspecialchars($row['description']);
                            $likes = htmlspecialchars($row['likes']);
                    ?>
                            <?php
                            // Start the session if it hasn't been started yet
                            if (session_status() == PHP_SESSION_NONE) {
                                session_start();
                            }

                            // Check if there's a flash message to display
                            if (isset($_SESSION['flash_message'])) {
                                $message = $_SESSION['flash_message'];
                                $type = $_SESSION['flash_type'];

                                // Clear the flash message after displaying it
                                unset($_SESSION['flash_message']);
                                unset($_SESSION['flash_type']);
                            ?>
                                <div class="toast-container">
                                    <div class="toast align-items-center text-white bg-<?php echo $type === 'success' ? 'success' : 'danger'; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="d-flex">
                                            <div class="toast-body">
                                                <?php echo htmlspecialchars($message); ?>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>

                            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-end">
                                        <div class="dropdown">
                                            <!-- name -->
                                            <label id="email"><?php echo $userID; ?></label>
                                            <button class="btn align-items-center text-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                <svg class="bi me-2 opacity-50" width="1rem" height="1rem">
                                                    <use href="#hamburger"></use>
                                                </svg>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li><a class="dropdown-item" href="?page=delete_post&postID=<?php echo $postID; ?>">Delete Post</a></li>
                                                <li><a class="dropdown-item" href="?page=editPost&postID=<?php echo $postID; ?>">Edit Post</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- HTML / PHP untuk menampilkan gambar -->
                                    <a href="javascript:void(0);" onclick="showPopup(<?php echo $postID; ?>);">
                                        <img src="./uploads/<?php echo htmlspecialchars($img); ?>" class="card-img-top img-popup" alt="Post Image">
                                    </a>

                                    <!-- <img src="./uploads/<?php echo $img; ?>" class="card-img-top img-popup" alt="Post Image" onclick="document.getElementById('popup-overlay').style.display='block'; return false;"> -->
                                    <div class="card-body">
                                        <!-- Like Section -->
                                        <form method="post" action="" class="d-flex justify-content-start">
                                            <input type="hidden" name="postID" id="postID" value="<?php echo $postID; ?>">
                                            <button type="submit" name="like" class="btn align-items-center d-flex text-center">
                                                <svg class="bi" width="1.5rem" height="1.5rem">
                                                    <use href="#like"></use>
                                                </svg>
                                                <label class="bi text-decoration-none ms-2"><?php echo $likes; ?> Likes</label>
                                            </button>
                                            <button type="submit" name="bookmark" class="btn align-items-center d-flex text-center">
                                                <svg class="bi" width="1.5rem" height="1.5rem">
                                                    <use href="#bookmark"></use>
                                                </svg>
                                                <label class="bi text-decoration-none ms-2">Bookmark</label>
                                            </button>
                                        </form>

                                        <div class="hero-section" style="max-height: 100px; height:100px; overflow-y: auto;">
                                            <p class="card-text bold-text h6"><?php echo $caption; ?></p>
                                            <label id="created"><?php echo $created; ?></label>
                                            <p class="card-text"><?php echo $description; ?></p>
                                        </div>

                                        <!-- Display Comments -->
                                        <h6>Comments:</h6>
                                        <div class="comments-container p-2 rounded" style="max-height: 100px; height:100px; overflow-y: auto; background-color:rgb(0,0,0,0.11);">
                                            <?php
                                            // Fetch comments for the current post
                                            $comment_sql = "
                                                SELECT c.commentID, c.comment_text, c.userID as commentUserID, u.email
                                                FROM comment c
                                                JOIN user u ON c.userID = u.userID
                                                WHERE c.postID = '$postID'
                                            ";
                                            $comment_result = $conn->query($comment_sql);

                                            if ($comment_result->num_rows > 0) {
                                                while ($comment_row = $comment_result->fetch_assoc()) {
                                                    $commentID = $comment_row['commentID'];
                                                    $commentUserID = $comment_row['commentUserID']; // Get the userID of the comment
                                                    $email = htmlspecialchars($comment_row['email']);
                                                    $comment_text = htmlspecialchars($comment_row['comment_text']);
                                            ?>
                                                    <div class="comment mb-2 d-flex justify-content-between align-items-center">
                                                        <p><strong><?php echo $email; ?></strong>: <?php echo $comment_text; ?></p>
                                                        <?php if (isset($_SESSION['userID']) && $_SESSION['userID'] == $commentUserID) { ?>
                                                            <!-- Delete Comment Form -->
                                                            <form action="?page=delete_comment" method="post" class="mb-0">
                                                                <input type="hidden" name="commentID" value="<?php echo $commentID; ?>">
                                                                <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Are you sure you want to delete this comment?');">
                                                                    <svg class="bi" width="1.2rem" height="1.2rem">
                                                                        <use href="#trash"></use>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        <?php } ?>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo "<p>No comments yet.</p>";
                                            }
                                            ?>
                                        </div>


                                        <!-- Add Comment Form -->
                                        <form action="?page=add_comment" method="post" class="mt-3">
                                            <div class="form-group d-flex">
                                                <input type="hidden" name="postID" value="<?php echo $postID; ?>"> <!-- Add hidden postID field -->
                                                <textarea class="form-control m-1" id="comment_text" name="comment_text" rows="1"></textarea>
                                                <button type="submit" class="btn d-flex align-items-center">
                                                    <svg class="bi opacity-50 " width="1.5rem" height="1.5rem">
                                                        <use href="#send"></use>
                                                    </svg>
                                                </button>
                                            </div>
                                        </form>
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
        </div>
    </div>
    <!-- Popup overlay -->
    <div class="popup-overlay" id="detailPopup">
        <div class="popup-content">
            <iframe id="popupIframe" style="width: 900px; height: 400px; border: none;"></iframe>
            <button id="closePopup">Close</button>
        </div>
    </div>
    <script>
        function showPopup(postID) {
            const iframe = document.getElementById('popupIframe');
            iframe.src = `?page=detail_post&postID=${postID}`;
            document.getElementById('detailPopup').style.display = 'block';
        }

        document.getElementById('closePopup').addEventListener('click', function() {
            document.getElementById('detailPopup').style.display = 'none';
            document.getElementById('popupIframe').src = ''; // Hapus src iframe saat popup ditutup
        });
    </script>


    <!-- jQuery -->
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
        });
    </script>
</body>

</html>