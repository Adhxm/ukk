<?php

// Include the database connection file
require './config/config.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if 'postID' is set in POST data
    if (isset($_POST['postID'])) {
        // Sanitize and validate input
        $comment_text = htmlspecialchars(trim($_POST['comment_text']));
        $postID = intval($_POST['postID']);
        
        // Check if the user is logged in
        if (isset($_SESSION['userID'])) {
            $userID = $_SESSION['userID'];

            // Prepare and execute the SQL statement
            $stmt = $conn->prepare("INSERT INTO comment (userID, postID, comment_text) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $userID, $postID, $comment_text);

            if ($stmt->execute()) {
                // Redirect to the home page or a specific page
                header("Location: ?page=home");
                exit();
            } else {
                // Display error message if query fails
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "You must be logged in to post a comment.";
        }
    } else {
        echo "Error: 'postID' is missing.";
    }
}

// Close the database connection
$conn->close();
?>
