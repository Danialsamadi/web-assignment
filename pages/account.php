<?php
session_start(); // Ensure session is started for user authentication

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../server/abstractDAO.php';
$dao = new abstractDAO();
$mysqli = $dao->getMysqli();
$user_id = $_SESSION['user_id'];

// Fetch user details
$user_sql = "SELECT username, email, created_at FROM users WHERE id=?";
$user_stmt = $mysqli->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($username, $email, $created_at);
$user_stmt->fetch();
$user_stmt->close();

// Fetch user posts
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
<!-- NAVBAR -->
<div class="navbar">
    <a class="nav-title-link" href="index.php">
        <span class="nav-title">Blog Platform</span>
    </a>
    <a class="button" href="index.php">
        <span class="button-text">Home</span>
    </a>
    <a class="button" href="add_post.php">
        <span class="button-text">Add Post</span>
    </a>
    <a class="button" href="../server/logout.php">
        <span class="button-text">Logout</span>
    </a>
</div>

<!-- MAIN CONTENT -->
<div id="main-content">
    <h2>My Account</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Member since:</strong> <?php echo htmlspecialchars($created_at); ?></p>

    <h2>My Posts</h2>
    <?php while ($posts_stmt->fetch()): ?>
        <div class="post">
            <h3><a href="blog_post.php?id=<?php echo $post_id; ?>"><?php echo htmlspecialchars($title); ?></a></h3>
            <?php if (!empty($image)): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" alt="Post Image" style="max-width:100%;height:auto;">
            <?php endif; ?>
            <p><?php echo htmlspecialchars(substr($content, 0, 150)); ?>...</p>
            <p><a href="edit_post.php?id=<?php echo $post_id; ?>">Edit</a> | <a href="../server/delete_post.php?id=<?php echo $post_id; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a></p>
            <p><small>Posted on: <?php echo htmlspecialchars($post_created_at); ?></small></p>
        </div>
    <?php endwhile; ?>
    <?php $posts_stmt->close(); ?>
</div>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
<?php $mysqli->close(); ?>
