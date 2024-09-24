<?php
include './config/config.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    // Store error message in session
    $_SESSION['flash_message'] = 'You are not logged in.';
    $_SESSION['flash_type'] = 'error'; // Message type (error, success, etc.)
    // Redirect to the login page
    header("Location: ?page=login");
    exit();
}

// Get the post ID from the URL
$postID = $_GET['postID'] ?? null;

// If postID is not provided, show an error and stop the script
if (!$postID) {
    $_SESSION['flash_message'] = 'Post ID not provided.';
    $_SESSION['flash_type'] = 'error';
    header("Location: ?page=home");
    exit();
}

// Fetch post data from the database
$sql = "SELECT * FROM post WHERE postID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $postID);
$stmt->execute();
$result = $stmt->get_result();

// If the post is not found, show an error
if ($result->num_rows == 0) {
    $_SESSION['flash_message'] = 'Post not found.';
    $_SESSION['flash_type'] = 'error';
    header("Location: ?page=home");
    exit();
}

$post = $result->fetch_assoc();

// Check if the logged-in user is the owner of the post
if ($post['userID'] != $_SESSION['userID']) {
    $_SESSION['flash_message'] = 'You are not authorized to edit this post.';
    $_SESSION['flash_type'] = 'error';
    header("Location: ?page=home"); // Redirect to the homepage
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caption = $_POST['caption'];
    $description = $_POST['description'];
    $fileName = $post['img']; // Default to existing image

    // Handle file upload
    if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['img']['tmp_name'];
        $fileName = $_FILES['img']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Define allowed file extensions
        $allowedExts = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
        if (in_array($fileExtension, $allowedExts)) {
            // Directory where the image will be saved
            $uploadFileDir = './uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            $dest_path = $uploadFileDir . $fileName;

            // Move the file to the directory
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // If new image is uploaded, use the new image name
                $fileName = $fileName;
            }
        }
    }

    // Update data in the database
    $sql = "UPDATE post SET img = ?, caption = ?, description = ? WHERE postID = ? AND userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $fileName, $caption, $description, $postID, $_SESSION['userID']);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = 'Post updated successfully!';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'Failed to update post.';
        $_SESSION['flash_type'] = 'error';
    }

    header("Location: ?page=home"); // Redirect to the homepage
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
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
            /* Make sure the toast is above other elements */
        }
    </style>
</head>

<body>

    <div class="container card mt-5 w-100">
        <h2 class="text-center">Edit Post</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Current Image:</label><br>
                <img src="./uploads/<?php echo $post['img']; ?>" alt="Post Image" style="max-width: 100%; height: auto;">
            </div>
            <div class="form-group">
                <label for="image">Upload New Image (optional):</label>
                <input type="file" class="form-control" id="image" name="img">
            </div>
            <div class="form-group">
                <label for="caption">Caption:</label>
                <input type="text" class="form-control" id="caption" name="caption" value="<?php echo htmlspecialchars($post['caption']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($post['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Update Post</button>
        </form>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?php
                if (isset($_SESSION['flash_message'])) {
                    echo $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']); // Clear the message after displaying
                }
                ?>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onload = function() {
            // Show the toast notification if there's a flash message
            var toast = document.getElementById('toast');
            if (toast.querySelector('.toast-body').innerText.trim() !== '') {
                var bootstrapToast = new bootstrap.Toast(toast);
                bootstrapToast.show();
            }
        }
    </script>

</body>

</html>