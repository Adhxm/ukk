<?php
include 'config/config.php'; // Pastikan koneksi database sudah terhubung

// Proses Tambah Album
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitAlbum'])) {
    $namaAlbum = $_POST['namaAlbum'];
    $keteranganAlbum = $_POST['keteranganAlbum'];
    $userID = $_SESSION['userID'];

    $sql_insert_album = "INSERT INTO album (namaAlbum, keteranganAlbum, userID) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_insert_album);
    $stmt->bind_param("ssi", $namaAlbum, $keteranganAlbum, $userID);

    if ($stmt->execute()) {
        echo "<script>alert('Album berhasil ditambahkan.');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan album: " . $conn->error . "');</script>";
    }
}

// Proses Tambah ke Album
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveAlbum'])) {
    $postID = $_POST['postID'];
    $albumID = $_POST['albumID'];

    if (!empty($postID) && !empty($albumID)) {
        // Cek apakah post sudah ada di album
        $sql_check = "SELECT * FROM album_posts WHERE albumID = ? AND postID = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("ii", $albumID, $postID);
        $stmt->execute();
        $result_check = $stmt->get_result();

        if ($result_check->num_rows === 0) {
            // Insert ke album_posts
            $sql_add_to_album = "INSERT INTO album_posts (albumID, postID) VALUES (?, ?)";
            $stmt = $conn->prepare($sql_add_to_album);
            $stmt->bind_param("ii", $albumID, $postID);

            if ($stmt->execute()) {
                echo "<script>alert('Post berhasil ditambahkan ke album.');</script>";
            } else {
                echo "<script>alert('Gagal menambahkan post ke album: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Post sudah ada di album ini.');</script>";
        }
    } else {
        echo "<script>alert('Album atau Post ID tidak valid.');</script>";
    }
}

// Proses Hapus dari Album
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeAlbum'])) {
    $postID = $_POST['postID'];
    $albumID = $_POST['albumID'];

    if (!empty($postID) && !empty($albumID)) {
        // Hapus dari album_posts
        $sql_remove_from_album = "DELETE FROM album_posts WHERE albumID = ? AND postID = ?";
        $stmt = $conn->prepare($sql_remove_from_album);
        $stmt->bind_param("ii", $albumID, $postID);

        if ($stmt->execute()) {
            echo "<script>alert('Post berhasil dihapus dari album.');</script>";
        } else {
            echo "<script>alert('Gagal menghapus post dari album: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Album atau Post ID tidak valid.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Head content seperti sebelumnya -->
</head>

<body>
    <!-- ini untuk icon navbar -->
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
        <!-- Daftar Album (Bubble Text) -->
        <div class="album-list mb-4">
            <button class="btn btn-outline-info m-1" onclick="filterPostsByAlbum('all')">All</button>
            <?php
            // Ambil album pengguna
            $sql_album = "SELECT albumID, namaAlbum FROM album WHERE userID = ?";
            $stmt = $conn->prepare($sql_album);
            $stmt->bind_param("i", $_SESSION['userID']);
            $stmt->execute();
            $result_album = $stmt->get_result();

            if ($result_album->num_rows > 0) {
                while ($album = $result_album->fetch_assoc()) {
                    $albumID = htmlspecialchars($album['albumID']);
                    $namaAlbum = htmlspecialchars($album['namaAlbum']);
            ?>
                    <div class="position-relative d-inline-block m-1">
                        <button class="btn btn-outline-info" onclick="filterPostsByAlbum('<?php echo $albumID; ?>')">
                            <?php echo $namaAlbum; ?>
                        </button>
                        <form action="?page=delete_album" method="post" class="position-absolute top-0 end-0 p-1">
                            <input type="hidden" name="albumID" value="<?php echo $albumID; ?>">
                            <button type="submit" class="position-absolute top-0 translate-middle p-1 bg-transparent border-0 btn-jos" onclick="return confirm('Are you sure you want to delete this album?');">
                                <svg class="bi" width="1.2rem" height="1.2rem" fill="#fff" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                </svg>
                            </button>
                        </form>
                    </div>
            <?php
                }
            } else {
                echo "<p>Belum ada album</p>";
            }
            ?>
        </div>
    </div>

    <div class="container">
        <!-- Modal Tambah ke Album -->
        <div class="modal fade" id="addToAlbumModal" tabindex="-1" aria-labelledby="addToAlbumModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addToAlbumModalLabel">Tambahkan ke Album</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="?page=album">
                            <input type="hidden" name="postID" id="modalPostID">
                            <div class="mb-3">
                                <label for="albumID" class="form-label">Pilih Album</label>
                                <select class="form-select" name="albumID" required>
                                    <?php
                                    // Ambil semua album pengguna
                                    $sql_user_albums = "SELECT albumID, namaAlbum FROM album WHERE userID = ?";
                                    $stmt_albums = $conn->prepare($sql_user_albums);
                                    $stmt_albums->bind_param("i", $_SESSION['userID']);
                                    $stmt_albums->execute();
                                    $result_albums = $stmt_albums->get_result();

                                    while ($album = $result_albums->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($album['albumID']) . "'>" . htmlspecialchars($album['namaAlbum']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="saveAlbum" class="btn btn-success">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tambah Album Button and Form -->
        <button class="btn btn-primary mb-3" data-bs-toggle="collapse" data-bs-target="#albumForm">Tambah Album</button>
        <div id="albumForm" class="collapse">
            <form method="POST" action="?page=tambah_album">
                <div class="mb-3">
                    <label for="namaAlbum" class="form-label">Nama Album</label>
                    <input type="text" class="form-control" id="namaAlbum" name="namaAlbum" required>
                </div>
                <div class="mb-3">
                    <label for="keteranganAlbum" class="form-label">Keterangan Album</label>
                    <textarea class="form-control" id="keteranganAlbum" name="keteranganAlbum" required></textarea>
                </div>
                <input type="hidden" name="userID" value="<?php echo $_SESSION['userID']; ?>">
                <button type="submit" name="submitAlbum" class="btn btn-success">Simpan Album</button>
            </form>
        </div>
        <div class="row">
            <?php
            // Ambil post berdasarkan filter album
            if (isset($_GET['albumID']) && $_GET['albumID'] !== 'all') {
                $albumID = $_GET['albumID'];

                // Ambil post yang terkait dengan album tertentu
                $sql_posts_by_album = "
                    SELECT p.postID, p.created, p.img, p.caption, p.description, p.likes, u.email 
                    FROM post p
                    JOIN album_posts ap ON p.postID = ap.postID
                    JOIN album a ON ap.albumID = a.albumID
                    JOIN user u ON p.userID = u.userID
                    WHERE a.albumID = ?
                ";
                $stmt = $conn->prepare($sql_posts_by_album);
                $stmt->bind_param("i", $albumID);
                $stmt->execute();
                $result_posts = $stmt->get_result();
            } else {
                // Ambil semua post jika tidak ada filter atau 'all' dipilih
                $sql_posts_all = "
                    SELECT p.postID, p.created, p.img, p.caption, p.description, p.likes, u.email 
                    FROM post p
                    JOIN user u ON p.userID = u.userID
                ";
                $result_posts = $conn->query($sql_posts_all);
            }

            // Penanganan Like dan Bookmark seperti sebelumnya

            if ($result_posts->num_rows > 0) {
                while ($row = $result_posts->fetch_assoc()) {
                    $userEmail = htmlspecialchars($row['email']);
                    $postID = htmlspecialchars($row['postID']);
                    $img = htmlspecialchars($row['img']);
                    $created = htmlspecialchars($row['created']);
                    $caption = htmlspecialchars($row['caption']);
                    $description = htmlspecialchars($row['description']);
                    $likes = htmlspecialchars($row['likes']);
            ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-end">
                                <div class="dropdown">
                                    <label id="email"><?php echo $userEmail; ?></label>
                                    <button class="btn align-items-center text-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg class="bi me-2 opacity-50" width="1rem" height="1rem">
                                            <use href="#hamburger"></use>
                                        </svg>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><a class="dropdown-item" href="?page=delete_post&postID=<?php echo $postID; ?>">Delete Post</a></li>
                                        <li><a class="dropdown-item" href="?page=editPost&postID=<?php echo $postID; ?>">Edit Post</a></li>

                                        <?php
                                        // Ambil semua album pengguna
                                        $sql_user_albums = "SELECT albumID, namaAlbum FROM album WHERE userID = ?";
                                        $stmt_albums = $conn->prepare($sql_user_albums);
                                        $stmt_albums->bind_param("i", $_SESSION['userID']);
                                        $stmt_albums->execute();
                                        $result_albums = $stmt_albums->get_result();

                                        $postInAlbum = false; // Flag untuk memeriksa apakah post ada di album

                                        while ($album = $result_albums->fetch_assoc()) {
                                            $albumID = htmlspecialchars($album['albumID']);
                                            $namaAlbum = htmlspecialchars($album['namaAlbum']);

                                            // Cek apakah post ada di album ini
                                            $sql_check = "SELECT * FROM album_posts WHERE albumID = ? AND postID = ?";
                                            $stmt_check = $conn->prepare($sql_check);
                                            $stmt_check->bind_param("ii", $albumID, $postID);
                                            $stmt_check->execute();
                                            $result_check = $stmt_check->get_result();

                                            if ($result_check->num_rows > 0) {
                                                // Post sudah ada di album, tampilkan opsi 'Hapus dari Album'
                                                echo "<li>
                                                        <form method='post' action='?page=album'>
                                                            <input type='hidden' name='albumID' value='$albumID'>
                                                            <input type='hidden' name='postID' value='$postID'>
                                                            <button type='submit' name='removeAlbum' class='dropdown-item'>Hapus dari $namaAlbum</button>
                                                        </form>
                                                    </li>";
                                                $postInAlbum = true; // Tandai bahwa post ada di setidaknya satu album
                                            }
                                        }

                                        // Jika post belum ada di album manapun, tampilkan opsi 'Tambahkan ke Album'
                                        if (!$postInAlbum) {
                                            echo "<li>
                                                    <button class='dropdown-item' data-bs-toggle='modal' data-bs-target='#addToAlbumModal' data-post-id='$postID'>Tambahkan ke Album</button>
                                                </li>";
                                        }
                                        ?>
                                    </ul>

                                </div>
                            </div>

                            <!-- Tampilkan gambar dan detail post -->
                            <a href="?page=detail_post&postID=<?php echo $postID; ?>">
                                <img src="./uploads/<?php echo $img; ?>" class="card-img-top img-popup" alt="Post Image">
                            </a>

                            <div class="card-body">
                                <!-- Like and Bookmark Section -->
                                <form method="post" action="" class="d-flex justify-content-start">
                                    <input type="hidden" name="postID" value="<?php echo $postID; ?>">
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
                                    <label id="created">update :</label>
                                    <label id="created"><?php echo $created; ?></label>
                                    <p class="card-text"><?php echo $description; ?></p>
                                </div>

                                <!-- Tampilkan Komentar -->
                                <h6>Comments:</h6>
                                <div class="comments-container p-2 rounded" style="max-height: 100px; height:100px; overflow-y: auto; background-color:rgba(0,0,0,0.11);">
                                    <?php
                                    // Ambil komentar untuk post ini
                                    $comment_sql = "
                                        SELECT c.commentID, c.comment_text, c.userID as commentUserID, u.email
                                        FROM comment c
                                        JOIN user u ON c.userID = u.userID
                                        WHERE c.postID = ?
                                    ";
                                    $stmt_comment = $conn->prepare($comment_sql);
                                    $stmt_comment->bind_param("i", $postID);
                                    $stmt_comment->execute();
                                    $comment_result = $stmt_comment->get_result();

                                    if ($comment_result->num_rows > 0) {
                                        while ($comment_row = $comment_result->fetch_assoc()) {
                                            $commentID = htmlspecialchars($comment_row['commentID']);
                                            $commentUserID = htmlspecialchars($comment_row['commentUserID']);
                                            $email = htmlspecialchars($comment_row['email']);
                                            $comment_text = htmlspecialchars($comment_row['comment_text']);
                                    ?>
                                            <div class="comment mb-2 d-flex justify-content-between align-items-center">
                                                <p><strong><?php echo $email; ?></strong>: <?php echo $comment_text; ?></p>
                                                <?php if (isset($_SESSION['userID']) && $_SESSION['userID'] == $commentUserID) { ?>
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

                                <!-- Formulir Tambah Komentar -->
                                <form action="?page=add_comment" method="post" class="mt-3">
                                    <div class="form-group d-flex">
                                        <input type="hidden" name="postID" value="<?php echo $postID; ?>">
                                        <textarea class="form-control m-1" id="comment_text" name="comment_text" rows="1" placeholder="Tambahkan komentar..."></textarea>
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
                echo "<p>No posts found</p>";
            }
            ?>
        </div>
    </div>

    <!-- JavaScript dan Script tambahan seperti sebelumnya -->
    <script>
        var addToAlbumModal = document.getElementById('addToAlbumModal');
        addToAlbumModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Tombol yang mengaktifkan modal
            var postID = button.getAttribute('data-post-id'); // Ambil post ID
            var inputPostID = addToAlbumModal.querySelector('input[name="postID"]');
            inputPostID.value = postID; // Isi input dengan post ID
        });
    </script>
    <script>
        function filterPostsByAlbum(albumID) {
            console.log("Filtering by album ID:", albumID);
            window.location.href = "?page=album&albumID=" + albumID;
        }
    </script>
</body>

</html>