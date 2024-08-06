<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../server/abstractDAO.php';
$dao = new abstractDAO();
$mysqli = $dao->getMysqli();
$user_id = $_SESSION['user_id'];

$user_sql = "SELECT username, email, created_at FROM users WHERE id=?";
$user_stmt = $mysqli->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($username, $email, $created_at);
$user_stmt->fetch();
$user_stmt->close();

$posts_sql = "SELECT id, title, content, image, created_at FROM posts WHERE user_id=? ORDER BY created_at DESC";
$posts_stmt = $mysqli->prepare($posts_sql);
$posts_stmt->bind_param("i", $user_id);
$posts_stmt->execute();
$posts_stmt->bind_result($post_id, $title, $content, $image, $post_created_at);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<header class="navbar">
    <a class="nav-title-link" href="index.php"><span class="nav-title">Blog Platform</span></a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="add_post.php">Add Post</a>
        <a href="../server/logout.php">Logout</a>
    </nav>
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
</header>

<main id="main-content">
    <h2>My Account</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Member since:</strong> <?php echo htmlspecialchars($created_at); ?></p>

    <h2>My Posts</h2>
    <?php while ($posts_stmt->fetch()): ?>
        <div class="post">
            <h2 class='post-title'>
                <a href='blog_post.php?id=<?php echo $post_id; ?>'><?php echo htmlspecialchars($title); ?></a>
            </h2>
            <?php if (!empty($image)): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" alt="Post Image">
            <?php endif; ?>
            <p><?php echo htmlspecialchars(substr($content, 0, 150)); ?>...</p>
            <p>
    <button class='edit-comment-button' onclick="window.location.href='edit_post.php?id=<?php echo $post_id; ?>'">Edit</button>
    <button class='delete-post-a-button' onclick="if(confirm('Are you sure you want to delete this post?')) { window.location.href='../server/delete_post.php?id=<?php echo $post_id; ?>'; }">Delete</button>
</p>

            <p><small>Posted on: <?php echo htmlspecialchars($post_created_at); ?></small></p>
        </div>
    <?php endwhile; ?>
    <?php $posts_stmt->close(); ?>
</main>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>

<script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }
</script>
</body>
</html>
<?php $mysqli->close(); ?>
