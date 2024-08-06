<?php
session_start();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
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
    <?php if (isset($_GET['message']) && isset($_GET['message_type'])): ?>
        <div class="alert <?php echo htmlspecialchars($_GET['message_type']); ?>">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    <h2>Recent Blog Posts</h2>
    <form action="index.php" method="GET" class="filter-form">
        <div class="form-group">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="">All</option>
                <?php
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
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <select id="author" name="author">
                <option value="">All</option>
                <?php
                $author_sql = "SELECT id, username FROM users";
                $author_result = $mysqli->query($author_sql);
                while ($author_row = $author_result->fetch_assoc()) {
                    $selected = $author == $author_row['id'] ? 'selected' : '';
                    echo "<option value='" . $author_row['id'] . "' $selected>" . htmlspecialchars($author_row['username']) . "</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="submit-button">Filter</button>
    </form>
    <div id="posts">
        <?php
        $sql = "SELECT posts.id, posts.title, posts.content, posts.image, posts.user_id, users.username, posts.created_at, categories.id AS category_id 
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

        $search_term = '%' . strtolower($search) . '%';
        $stmt->bind_param('ssssssss', $search, $search_term, $search_term, $search_term, $category, $category, $author, $author);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $category_id = isset($row['category_id']) ? htmlspecialchars($row['category_id']) : '';
                echo "<article class='post' data-category='$category_id'>";
                echo "<h3 class='post-title'><a href='blog_post.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['title']) . "</a></h3>";
                echo "<p class='post-author'>" . htmlspecialchars($row['username']) . "</p>";
                if (!empty($row['image'])) {
                    $imgData = base64_encode($row['image']);
                    $src = 'data:image/jpeg;base64,' . $imgData;
                    echo "<img src='" . $src . "' alt='Post Image'/>";
                }
                echo "<p class='post-content'>" . htmlspecialchars(substr($row['content'], 0, 150)) . "...</p>";
                echo "<p class='post-date'>" . htmlspecialchars($row['created_at']) . "</p>";
                if ($row['user_id'] == $user_id) {
                    echo "<form method='POST' action='../server/delete_post.php' onsubmit='return confirm(\"Are you sure you want to delete this post?\")'>";
                    echo "<input type='hidden' name='post_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='delete-button'>Delete</button>";
                    echo "</form>";
                }
                echo "</article>";
            }
        } else {
            echo "<p>No posts found.</p>";
        }

        $stmt->close();
        $mysqli->close();
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
    <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
</footer>

<script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }

    function filterPosts() {
        const searchInput = document.getElementById('search').value.trim().toLowerCase();
        const selectedCategory = document.getElementById('category').value;
        const selectedAuthor = document.getElementById('author').value;
        const posts = document.querySelectorAll('.post');
        let hasResults = false;

        posts.forEach(post => {
            const title = post.querySelector('.post-title').textContent.toLowerCase();
            const author = post.querySelector('.post-author').textContent.toLowerCase();
            const category = post.dataset.category;

            if (title.includes(searchInput) && 
                (selectedCategory === '' || category === selectedCategory) && 
                (selectedAuthor === '' || author === selectedAuthor)) {
                post.style.display = '';
                hasResults = true;
            } else {
                post.style.display = 'none';
            }
        });

        const noResultsMessage = document.getElementById('no-results-message');
        if (!hasResults) {
            if (!noResultsMessage) {
                const message = document.createElement('p');
                message.id = 'no-results-message';
                message.textContent = 'No results found.';
                document.getElementById('posts').appendChild(message);
            }
        } else {
            if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    }
</script>
<script src="../scripts/validation.js"></script>
</body>
</html>
