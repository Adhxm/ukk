<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['albumID'])) {
    $albumID = $_POST['albumID'];

    // Ensure user is authorized to delete this album
    // Perform the delete query
    $sql = "DELETE FROM album WHERE albumID = ? AND userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $albumID, $_SESSION['userID']);
    $stmt->execute();

    // Redirect or handle success message
    header("Location: ?page=album"); // Adjust as needed
    exit();
}
?>
