<?php
session_start(); // Ensure session is started for user authentication

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];

    if (!empty($category_name)) {
        include '../server/abstractDAO.php';
        $dao = new abstractDAO();
        $mysqli = $dao->getMysqli();

        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $mysqli->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . $mysqli->error);
        }

        $stmt->bind_param('s', $category_name);
        $result = $stmt->execute();

        if ($result) {
            header('Location: ../pages/add_post.php'); // Redirect to add_post page after successful category addition
        } else {
            die('Execute failed: ' . $stmt->error);
        }

        $stmt->close();
        $mysqli->close();
    } else {
        echo "Category name cannot be empty.";
    }
} else {
    echo "Invalid request method.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
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
    <a class="button" href="add_category.php">
        <span class="button-text">Add Category</span>
    </a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="button" href="javascript:void(0);" onclick="confirmLogoutAndRegister()">
            <span class="button-text">Register</span>
        </a>
        <a class="button" href="../server/logout.php">
            <span class="button-text">Logout</span>
        </a>
    <?php else: ?>
        <a class="button" href="login.php">
            <span class="button-text">Login</span>
        </a>
    <?php endif; ?>
</div>

<!-- MAIN PAGE CONTENT -->
<div id="main-content">
    <h2>Add a New Category</h2>
    <form action="add_category.php" method="POST" class="add-category-form">
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="category_name" required>
        </div>
        <button type="submit" class="submit-button">Add Category</button>
    </form>
</div>
</body>
</html>
