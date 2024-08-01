<?php
session_start(); // Ensure session is started for user authentication
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Platform</title>
    <link rel="stylesheet" href="../styles/style.css">
    <script>
        function confirmLogoutAndRegister() {
            if (confirm("You will be logged out to register a new user. Do you want to continue?")) {
                window.location.href = "../server/logout.php?redirect=register";
            }
        }
    </script>
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
    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="button" href="javascript:void(0);" onclick="confirmLogoutAndRegister()">
            <span class="button-text">Register</span>
        </a>
        <a class="button" href="../server/logout.php">
            <span class="button-text">Logout</span>
        </a>
    <?php else: ?>
        <a class="button" href="register.php">
            <span class="button-text">Register</span>
        </a>
        <a class="button" href="login.php">
            <span class="button-text">Login</span>
        </a>
    <?php endif; ?>
</div>
</body>
</html>


<!-- MAIN CONTENT -->
<div id="main-content">
    <h2>Recent Blog Posts</h2>
    <form method="GET" action="index.php">
        <label for="search">Search:</label>
        <input type="text" id="search" name="search">
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="">All</option>
            <?php
            // Fetch categories from the database
            include '../server/abstractDAO.php';
            $dao = new abstractDAO();
            $mysqli = $dao->getMysqli();
            $sql = "SELECT id, name FROM categories";
            $result = $mysqli->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
            }
            ?>
        </select>
        <label for="author">Author:</label>
        <select id="author" name="author">
            <option value="">All</option>
            <?php
            // Fetch authors from the database
            $sql = "SELECT id, username FROM users";
            $result = $mysqli->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['username']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Filter</button>
    </form>
    <div id="posts">
        <?php
        // Handle filtering
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $author = isset($_GET['author']) ? $_GET['author'] : '';

        $sql = "SELECT posts.id, posts.title, posts.content, posts.image, posts.keywords, users.username, posts.created_at 
                    FROM posts 
                    JOIN users ON posts.user_id = users.id 
                    LEFT JOIN post_categories ON posts.id = post_categories.post_id 
                    LEFT JOIN categories ON categories.id = post_categories.category_id 
                    WHERE (? = '' OR (posts.title LIKE ? OR posts.content LIKE ? OR posts.keywords LIKE ?))
                    AND (? = '' OR categories.id = ?)
                    AND (? = '' OR users.id = ?)
                    GROUP BY posts.id
                    ORDER BY posts.created_at DESC";
        $stmt = $mysqli->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . $mysqli->error);
        }

        $search_term = '%' . $search . '%';
        $stmt->bind_param('ssssssss', $search, $search_term, $search_term, $search_term, $category, $category, $author, $author);
        $stmt->execute();
        $result = $stmt->get_result();

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

        $stmt->close();
        $mysqli->close();
        ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
