<?php
include './config/config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentID = $_POST['commentID'];

    if ($commentID) {
        // Ensure the commentID is numeric to prevent SQL injection
        if (is_numeric($commentID)) {
            $sql = "DELETE FROM comment WHERE commentID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $commentID);

            if ($stmt->execute()) {
                $_SESSION['flash_message'] = 'Comment deleted successfully.';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Failed to delete comment.';
                $_SESSION['flash_type'] = 'error';
            }
        } else {
            $_SESSION['flash_message'] = 'Invalid comment ID.';
            $_SESSION['flash_type'] = 'error';
        }
    } else {
        $_SESSION['flash_message'] = 'No comment ID provided.';
        $_SESSION['flash_type'] = 'error';
    }
}

// Redirect back to the previous page or homepage
header("Location: ?page=home");
exit();
