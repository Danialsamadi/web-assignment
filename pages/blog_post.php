<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Post</title>
    <link rel="stylesheet" href="../styles/style.css">
    <script src="../scripts/ajax_comments.js" defer></script>
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
    <div id="post">
        <?php
        include '../server/abstractDAO.php';

        $dao = new abstractDAO();
        $mysqli = $dao->getMysqli();

        $post_id = $_GET['id'];

        $sql = "SELECT posts.title, posts.content, users.username, posts.created_at 
                    FROM posts 
                    JOIN users ON posts.user_id = users.id 
                    WHERE posts.id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($title, $content, $username, $created_at);
        $stmt->fetch();

        echo "<h2>" . $title . "</h2>";
        echo "<p>by " . $username . " on " . $created_at . "</p>";
        echo "<div>" . nl2br(htmlspecialchars($content)) . "</div>";

        $stmt->close();
        ?>
    </div>

    <div id="comments">
        <h3>Comments</h3>
        <form id="commentForm">
            <textarea id="commentContent" name="content" required></textarea>
            <input type="hidden" id="postId" name="post_id" value="<?php echo $post_id; ?>">
            <input type="submit" value="Add Comment">
        </form>
        <div id="commentsList">
            <?php
            $sql = "SELECT comments.content, users.username, comments.created_at 
                        FROM comments 
                        JOIN users ON comments.user_id = users.id 
                        WHERE comments.post_id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($comment_content, $comment_username, $comment_created_at);

            while ($stmt->fetch()) {
                echo "<div class='comment'>";
                echo "<p>" . nl2br(htmlspecialchars($comment_content)) . "</p>";
                echo "<p>by " . $comment_username . " on " . $comment_created_at . "</p>";
                echo "</div>";
            }

            $stmt->close();
            $mysqli->close();
            ?>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
