<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<header class="navbar">
    <a class="nav-title-link" href="index.php"><span class="nav-title">Blog Platform</span></a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
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
    <h2>Add a New Post</h2>
    <form action="../server/add_post.php" method="POST" enctype="multipart/form-data" class="add-post-form" onsubmit="return validateImageSize()">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea>
        </div>
        <div class="form-group">
            <label for="keywords">Keywords:</label>
            <input type="text" id="keywords" name="keywords">
        </div>
        <div class="form-group">
            <label for="categories">Categories:</label>
            <div id="category-checkboxes">
                <?php
                include '../server/abstractDAO.php';
                $dao = new abstractDAO();
                $mysqli = $dao->getMysqli();
                $sql = "SELECT id, name FROM categories";
                $result = $mysqli->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<label><input type='checkbox' name='categories[]' value='" . $row['id'] . "'> " . htmlspecialchars($row['name']) . "</label><br>";
                }
                $mysqli->close();
                ?>
            </div>
            <a href="add_category.php" class="add-category-link">Add New Category</a>
        </div>
        <div class="form-group">
            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <button type="submit" class="submit-button">Add Post</button>
    </form>
</main>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>

<script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }

    function validateImageSize() {
        const fileInput = document.getElementById('image');
        const file = fileInput.files[0];
        if (file && file.size > 600 * 1024) {
            alert('Image size should not exceed 600 KB');
            return false;
        }
        return true;
    }
</script>
</body>
</html>
