<?php
    $conn = mysqli_connect("localhost", "root", "", "db_gramess");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>