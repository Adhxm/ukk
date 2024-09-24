<?php
include './config/config.php'; // Pastikan koneksi ke database sudah di-include

// Periksa apakah user sudah login
if (!isset($_SESSION['userID'])) {
    // Simpan pesan error di session
    $_SESSION['flash_message'] = 'Not login yet.';
    $_SESSION['flash_type'] = 'error'; // Tipe pesan (error, success, dll.)
    // Redirect ke halaman login
    header("Location: ?page=login");
    exit();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
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
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050; /* Pastikan toast berada di atas elemen lain */
        }
    </style>
</head>
<body>
    <div class="container card mt-5 w-100">
        <h2 class="text-center">Add New Post</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" class="form-control" id="userID" name="userID" value="<?php echo $userID; ?>" required>
            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" class="form-control" id="image" name="img" required>
            </div>
            <div class="form-group">
                <label for="caption">Caption:</label>
                <input type="text" class="form-control" id="caption" name="caption" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Post</button>
        </form>
    </div>

    <?php
    if (isset($_SESSION['userID'])) {
        $userID = $_SESSION['userID']; 

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $caption = $_POST['caption'];
        $description = $_POST['description'];

        // Handle file upload
        if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['img']['tmp_name'];
            $fileName = $_FILES['img']['name'];
            $fileSize = $_FILES['img']['size'];
            $fileType = $_FILES['img']['type'];
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
                    // Insert data into the database
                    $sql = "INSERT INTO post (img, caption, description, userID) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $fileName, $caption, $description, $userID);

                    if ($stmt->execute()) {
                        // echo "Post added successfully!";
                        ?>
                            <div class="toast-container">
                                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header">
                                    <strong class="me-auto">Success</strong>
                                    <small>1 mins ago</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    Post added successfully!
                                </div>
                                </div>
                            </div>
                        <?php
                    } else {
                        // echo "Failed to add post.";
                        ?>
                            <div class="toast-container">
                                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header">
                                    <strong class="me-auto">Error</strong>
                                    <small>1 mins ago</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    Post failed to add!
                                </div>
                                </div>
                            </div>
                        <?php
                    }
                } else {
                    // echo "Failed to move uploaded file.";
                    ?>
                            <div class="toast-container">
                                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header">
                                    <strong class="me-auto">Failed</strong>
                                    <small>1 mins ago</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    Failed to move uploaded file.
                                </div>
                                </div>
                            </div>
                        <?php
                }
            } else {
                // echo "Invalid file type.";
                ?>
                    <div class="toast-container">
                        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Invalid</strong>
                            <small>1 mins ago</small>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Invalid file type.
                        </div>
                        </div>
                    </div>
                <?php
            }
        } else {
            // echo "No file uploaded or upload error.";
            ?>
                    <div class="toast-container">
                        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Error</strong>
                            <small>1 mins ago</small>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            No file uploaded or upload error.
                        </div>
                        </div>
                    </div>
                <?php
        }
    }
}
    ?>

<script>
 document.addEventListener('DOMContentLoaded', function() {
  var toastContainer = document.querySelector('.toast-container');
  var toastElList = [].slice.call(document.querySelectorAll('.toast'));

  toastElList.forEach(function (toastEl) {
    // Pindahkan toast ke dalam kontainer
    toastContainer.appendChild(toastEl);
    var toast = new bootstrap.Toast(toastEl);
    toast.show();
  });
});

</script>
</body>
</html>
