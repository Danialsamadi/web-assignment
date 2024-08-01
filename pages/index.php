<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Platform</title>
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
    <a class="button" href="register.php">
        <span class="button-text">Register</span>
    </a>
    <a class="button" href="login.php">
        <span class="button-text">Login</span>
    </a>
</div>

<!-- MAIN CONTENT -->
<div id="main-content">
    <h2>Recent Blog Posts</h2>
    <div id="posts">
        <?php
        include '../server/abstractDAO.php';
        $dao = new abstractDAO();
        $mysqli = $dao->getMysqli();

        $sql = "SELECT posts.id, posts.title, posts.content, posts.image, users.username, posts.created_at 
                    FROM posts 
                    JOIN users ON posts.user_id = users.id 
                    ORDER BY posts.created_at DESC";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<article>";
                echo "<h3><a href='blog_post.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['title']) . "</a></h3>";
                if (!empty($row['image'])) {
                    $imgData = base64_encode($row['image']);
                    $src = 'data:image/jpeg;base64,' . $imgData;
                    echo "<img src='" . $src . "' alt='Post Image' style='max-width:100%;height:auto;'/>";
                }
                echo "<p>by " . htmlspecialchars($row['username']) . " on " . htmlspecialchars($row['created_at']) . "</p>";
                echo "<p>" . htmlspecialchars(substr($row['content'], 0, 150)) . "...</p>";
                echo "<a href='blog_post.php?id=" . $row['id'] . "'>Read more</a>";
                echo "</article>";
            }
        } else {
            echo "<p>No posts found.</p>";
        }

        $mysqli->close();
        ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
