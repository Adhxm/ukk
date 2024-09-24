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
} else {
    // If user is not logged in and tries to like or bookmark
    if (isset($_POST['like']) || isset($_POST['bookmark'])) {
        echo "<div class='toast-container'>
                <div class='toast' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='toast-header'>
                        <strong class='me-auto'>Error</strong>
                        <small>1 mins ago</small>
                        <button type='button' class='btn-close' data-bs-dismiss='toast' aria-label='Close'></button>
                    </div>
                    <div class='toast-body'>
                        You must be logged in to like or bookmark posts.
                    </div>
                </div>
            </div>";
    }
}

// Fetch post data from the database
$post_id = $_GET['postID'];
$query = "SELECT * FROM post WHERE postID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "Post tidak ditemukan!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Post</title>
    <link rel="stylesheet" href="./bootstrap/css/popupimg2.css">
    <style>
        /* Custom CSS for layout adjustments */
        .container {
            padding: 20px;
        }

        .card-header .dropdown {
            flex-grow: 1;
        }

        .hero-section {
            max-height: 100px;
            overflow-y: auto;
        }

        .comments-container {
            max-height: 150px;
            overflow-y: auto;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .btn:hover {
            background-color: rgba(99, 99, 99, 0.9);
            color: black;
        }

        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
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
    <div class="container d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header d-flex justify-content-end">
                <div class="dropdown d-flex align-items-center justify-content-between">
                    <!-- memanggil email -->
                    <?php
                    // Ambil email dari user yang mengupload postingan
                    $stmt = $conn->prepare("SELECT email FROM user WHERE userID = ?");
                    $stmt->bind_param("i", $post['userID']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $postUserEmail = $result->fetch_assoc()['email'] ?? "Unknown User";
                    $postUserEmail = htmlspecialchars($postUserEmail);
                    ?>

                    <label id="email"><?php echo $postUserEmail; ?></label>
                    <button class="btn align-items-center text-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg class="bi me-2 opacity-50" width="1rem" height="1rem">
                            <use href="#hamburger"></use>
                        </svg>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="?page=delete_post&postID=<?php echo $post_id; ?>">Delete Post</a></li>
                        <li><a class="dropdown-item" href="?page=editPost&postID=<?php echo $post_id; ?>">Edit Post</a></li>
                    </ul>
                </div>
            </div>
            <img src="./uploads/<?php echo htmlspecialchars($post['img']); ?>" class="card-img-top img-popup align-self-center" alt="Post Image">
            <div class="card-body">
                <!-- Like and Bookmark Buttons -->
                <div class="d-flex justify-content-start mb-3">
                    <form action="" method="post">
                        <input type="hidden" name="postID" value="<?php echo $post_id; ?>">
                        <button type="submit" name="like" class="btn btn-outline-primary me-2">
                            <svg class="bi me-2 opacity-50" width="1rem" height="1rem">
                                <use href="#like"></use>
                            </svg><?php echo htmlspecialchars($post['likes']); ?> Likes
                        </button>
                    </form>
                    <form action="" method="post">
                        <input type="hidden" name="postID" value="<?php echo $post_id; ?>">
                        <button type="submit" name="bookmark" class="btn btn-outline-secondary">
                            <svg class="bi me-2 opacity-50" width="1rem" height="1rem">
                                <use href="#bookmark"></use>
                            </svg>Bookmark
                        </button>
                    </form>
                </div>

                <!-- Post Caption and Description -->
                <div class="hero-section mb-3">
                    <h5><?php echo htmlspecialchars($post['caption']); ?></h5>
                    <small class="text-muted"><?php echo htmlspecialchars($post['created']); ?></small>
                    <p><?php echo htmlspecialchars($post['description']); ?></p>
                </div>

                <!-- Comments Section -->
                <h6>Comments:</h6>
                <div class="comments-container p-2 rounded" style="max-height: 100px; height:100px; overflow-y: auto; background-color:rgb(0,0,0,0.11);">
                    <?php
                    // Fetch comments for the current post
                    $comment_sql = "
                        SELECT c.commentID, c.comment_text, c.userID as commentUserID, u.email
                        FROM comment c
                        JOIN user u ON c.userID = u.userID
                        WHERE c.postID = '$post_id'
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
    </div>
    <?php
    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        // If not logged in, set a flag to show the toast
        $showToast = true;
    }
    ?>

    <div class='toast-container'>
        <div class='toast' role='alert'>
            <div class='toast-header'>
                <strong class='me-auto'>Warning</strong>
                <button type='button' class='btn-close' data-bs-dismiss='toast'></button>
            </div>
            <div class='toast-body'>You must be logged in to perform this action.</div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($showToast) && $showToast) { ?>
                var toastContainer = document.querySelector('.toast-container');
                var toastElList = [].slice.call(document.querySelectorAll('.toast'));

                toastElList.forEach(function(toastEl) {
                    toastContainer.appendChild(toastEl);
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            <?php } ?>
        });
    </script>

    <script>
        // JavaScript untuk menampilkan popup gambar
        document.addEventListener('DOMContentLoaded', function() {
            const imgPopupElements = document.querySelectorAll('.img-popup');
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            document.body.appendChild(overlay);

            imgPopupElements.forEach(img => {
                img.addEventListener('click', function() {
                    const popupImg = document.createElement('img');
                    popupImg.src = img.src;
                    popupImg.className = 'popup-img';
                    overlay.innerHTML = ''; // Hapus konten sebelumnya
                    overlay.appendChild(popupImg);
                    overlay.classList.add('active');
                });
            });

            overlay.addEventListener('click', function() {
                overlay.classList.remove('active');
            });
        });
    </script>
</body>

</html>