<?php
session_start(); // Ensure session is started for user authentication

// Fetch search and filter parameters from the query string
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';

?>
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
    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="button" href="account.php">
            <span class="button-text">My Account</span>
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

<!-- MAIN CONTENT -->
<div id="main-content">
    <?php if (isset($_GET['message']) && isset($_GET['message_type'])): ?>
        <div class="alert <?php echo htmlspecialchars($_GET['message_type']); ?>">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    <h2>Recent Blog Posts</h2>
    <form action="index.php" method="GET">
        <label for="search">Search:</label>
        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="">All</option>
            <?php
            // Fetch categories from the database
            include '../server/abstractDAO.php';
            $dao = new abstractDAO();
            $mysqli = $dao->getMysqli();
            $category_sql = "SELECT id, name FROM categories";
            $category_result = $mysqli->query($category_sql);
            while ($category_row = $category_result->fetch_assoc()) {
                $selected = $category == $category_row['id'] ? 'selected' : '';
                echo "<option value='" . $category_row['id'] . "' $selected>" . htmlspecialchars($category_row['name']) . "</option>";
            }
            ?>
        </select>
        <label for="author">Author:</label>
        <select id="author" name="author">
            <option value="">All</option>
            <?php
            // Fetch authors from the database
            $author_sql = "SELECT id, username FROM users";
            $author_result = $mysqli->query($author_sql);
            while ($author_row = $author_result->fetch_assoc()) {
                $selected = $author == $author_row['id'] ? 'selected' : '';
                echo "<option value='" . $author_row['id'] . "' $selected>" . htmlspecialchars($author_row['username']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Filter</button>
    </form>
    <div id="posts">
        <?php
        $sql = "SELECT posts.id, posts.title, posts.content, posts.image, users.username, posts.created_at 
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
