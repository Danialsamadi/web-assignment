<?php
session_start(); // Ensure session is started for user authentication

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
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
<!-- NAVBAR -->
<div class="navbar">
    <a class="nav-title-link" href="index.php">
        <span class="nav-title">Blog Platform</span>
    </a>
    <a class="button" href="index.php">
        <span class="button-text">Home</span>
    </a>
</div>

<!-- MAIN PAGE CONTENT -->
<div id="main-content">
    <h2>Add a New Post</h2>
    <form action="../server/add_post.php" method="POST" enctype="multipart/form-data" class="add-post-form">
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
            <select id="categories" name="categories[]" multiple>
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
                $mysqli->close();
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <button type="submit" class="submit-button">Add Post</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
