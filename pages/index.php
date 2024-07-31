<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<header>
    <h1>Blog Platform</h1>
    <nav>
        <a href="index.php">Home</a> |
        <a href="add_post.php">Add Post</a> |
        <a href="register.php">Register</a> |
        <a href="login.php">Login</a>
    </nav>
</header>

<main>
    <h2>Recent Blog Posts</h2>
    <div id="posts">
        <?php
        include '../server/abstractDAO.php';

        $dao = new abstractDAO();
        $mysqli = $dao->getMysqli();

        $sql = "SELECT posts.id, posts.title, posts.content, users.username, posts.created_at 
                    FROM posts 
                    JOIN users ON posts.user_id = users.id 
                    ORDER BY posts.created_at DESC";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<article>";
                echo "<h3><a href='blog_post.php?id=" . $row['id'] . "'>" . $row['title'] . "</a></h3>";
                echo "<p>by " . $row['username'] . " on " . $row['created_at'] . "</p>";
                echo "<p>" . substr($row['content'], 0, 150) . "...</p>";
                echo "<a href='blog_post.php?id=" . $row['id'] . "'>Read more</a>";
                echo "</article>";
            }
        } else {
            echo "<p>No posts found.</p>";
        }

        $mysqli->close();
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
s