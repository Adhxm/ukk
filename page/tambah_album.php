<?php
if (isset($_POST['submitAlbum'])) {
    $namaAlbum = $_POST['namaAlbum'];
    $keteranganAlbum = $_POST['keteranganAlbum'];
    $userID = $_POST['userID'];

    // Query untuk menyimpan album tanpa memasukkan postID
    $sql_insert_album = "INSERT INTO album (namaAlbum, keteranganAlbum, userID) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_insert_album);
    $stmt->bind_param("ssi", $namaAlbum, $keteranganAlbum, $userID);

    if ($stmt->execute()) {
        echo "Album berhasil disimpan.";
    } else {
        echo "Terjadi kesalahan saat menyimpan album: " . $stmt->error;
    }
    // Redirect or handle success message
    header("Location: ?page=album"); // Adjust as needed
    exit();
}
