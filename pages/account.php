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

// Fetch user's posts
$post_sql = "SELECT id, title, content, created_at FROM posts WHERE user_id=? ORDER BY created_at DESC";
$post_stmt = $mysqli->prepare($post_sql);
$post_stmt->bind_param("i", $user_id);
$post_stmt->execute();
$post_result = $post_stmt->get_result();
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
    <a class="button" href="account.php">
        <span class="button-text">My Account</span>
    </a>
    <a class="button" href="../server/logout.php">
        <span class="button-text">Logout</span>
    </a>
</div>

<!-- MAIN CONTENT -->
<div id="main-content">
    <h2>My Account</h2>
    <div>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Account Created:</strong> <?php echo htmlspecialchars($created_at); ?></p>
    </div>
    <h2>My Posts</h2>
    <div id="posts">
        <?php
        if ($post_result->num_rows > 0) {
            while ($row = $post_result->fetch_assoc()) {
                echo "<article>";
                echo "<h3><a href='edit_post.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['title']) . "</a></h3>";
                echo "<p>" . htmlspecialchars(substr($row['content'], 0, 150)) . "...</p>";
                echo "<p>Posted on " . htmlspecialchars($row['created_at']) . "</p>";
                echo "<a href='edit_post.php?id=" . $row['id'] . "'>Edit</a> | ";
                echo "<a href='../server/delete_post.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this post?\");'>Delete</a>";
                echo "</article>";
            }
        } else {
            echo "<p>No posts found.</p>";
        }

        $post_stmt->close();
        $mysqli->close();
        ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
