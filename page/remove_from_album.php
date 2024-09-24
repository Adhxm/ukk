<?php
if (isset($_GET['postID'])) {
    $postID = $_GET['postID'];
    
    // Set albumID menjadi NULL untuk menghapus post dari album
    $sql_remove_from_album = "UPDATE post SET albumID = NULL WHERE postID = ?";
    $stmt = $conn->prepare($sql_remove_from_album);
    $stmt->bind_param("i", $postID);
    
    if ($stmt->execute()) {
        echo "<script>alert('Post berhasil dihapus dari album.');</script>";
        header("Location: ?page=album"); // Redirect ke halaman album setelah sukses
        exit();
    } else {
        echo "<script>alert('Gagal menghapus post dari album.');</script>";
    }
}
?>
