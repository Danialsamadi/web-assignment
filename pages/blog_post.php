<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Post</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<header class="navbar">
    <a class="nav-title-link" href="index.php"><span class="nav-title">Blog Platform</span></a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="add_post.php">Add Post</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="account.php">My Account</a>
            <a href="../server/logout.php">Logout</a>
        <?php else: ?>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
</header>

<main id="main-content">
    <article>
        <?php
        include '../server/abstractDAO.php';
        $dao = new abstractDAO();
        $mysqli = $dao->getMysqli();

        $post_id = $_GET['id'];

        $sql = "SELECT posts.title, posts.content, posts.image, posts.keywords, users.username, posts.created_at 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                WHERE posts.id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($title, $content, $image, $keywords, $username, $created_at);
        $stmt->fetch();

        echo "<h2 class='post-title'>" . htmlspecialchars($title) . "</h2>";
        echo "<p class='post-meta'>by " . htmlspecialchars($username) . " on " . htmlspecialchars($created_at) . "</p>";
        if (!empty($image)) {
            $imgData = base64_encode($image);
            $src = 'data:image/jpeg;base64,' . $imgData;
            echo "<img src='" . $src . "' alt='Post Image'/>";
        }
        echo "<div>" . nl2br(htmlspecialchars($content)) . "</div>";
        if (!empty($keywords)) {
            echo "<p><strong>Keywords:</strong> " . htmlspecialchars($keywords) . "</p>";
        }
        $stmt->close();

        $sql = "SELECT categories.name FROM categories 
                JOIN post_categories ON categories.id = post_categories.category_id 
                WHERE post_categories.post_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "<p><strong>Categories:</strong> ";
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = htmlspecialchars($row['name']);
            }
            echo implode(", ", $categories);
            echo "</p>";
        }
        $stmt->close();
        ?>
    </article>

    <section id="comments">
        <h3>Comments</h3>
        <form id="commentForm">
            <textarea id="commentContent" name="content" required></textarea>
            <input type="hidden" id="postId" name="post_id" value="<?php echo $post_id; ?>">
            <input type="submit" value="Add Comment">
        </form>
        <div id="commentsList">
            <?php
            $sql = "SELECT comments.id, comments.content, users.username, comments.created_at 
                    FROM comments 
                    JOIN users ON comments.user_id = users.id 
                    WHERE comments.post_id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($comment_id, $comment_content, $comment_username, $comment_created_at);

            while ($stmt->fetch()) {
                echo "<div class='comment' id='comment-$comment_id'>";
                echo "<p>" . nl2br(htmlspecialchars($comment_content)) . "</p>";
                echo "<p>by " . htmlspecialchars($comment_username) . " on " . htmlspecialchars($comment_created_at) . "</p>";
                if (isset($_SESSION['user_id']) && $_SESSION['username'] == $comment_username) {
                    echo "<button class='delete-comment-button' data-comment-id='$comment_id'>Delete</button>";
                }
                echo "</div>";
            }

            $stmt->close();
            $mysqli->close();
        ?>
        </div>
    </section>
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
<script src="../scripts/ajax_comments.js"></script>
</body>
</html>
