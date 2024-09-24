<?php
// Inisialisasi variabel untuk menyimpan status like dan jumlah likes
$likes = isset($_POST['likes']) ? $_POST['likes'] : 0;
$isLiked = isset($_POST['isLiked']) ? $_POST['isLiked'] : false;

// Fungsi untuk menambah like
function likePost(&$likes, &$isLiked)
{
    if (!$isLiked) {
        $likes++;
        $isLiked = true;
    }
}

// Fungsi untuk mengurangi like (unlike)
function unlikePost(&$likes, &$isLiked)
{
    if ($isLiked) {
        $likes--;
        $isLiked = false;
    }
}

// Menangani permintaan POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['like'])) {
        likePost($likes, $isLiked);
    } elseif (isset($_POST['unlike'])) {
        unlikePost($likes, $isLiked);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Like & Unlike Example</title>
</head>

<body>
    <h2>Post Title</h2>
    <p>Likes: <?php echo $likes; ?></p>

    <!-- Form untuk like/unlike -->
    <form method="post">
        <input type="hidden" name="likes" value="<?php echo $likes; ?>">
        <input type="hidden" name="isLiked" value="<?php echo $isLiked ? '1' : ''; ?>">
        <?php if (!$isLiked): ?>
            <button type="submit" name="like">Like</button>
        <?php else: ?>
            <button type="submit" name="unlike">Unlike</button>
        <?php endif; ?>
    </form>
</body>

</html>