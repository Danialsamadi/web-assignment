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
    <form action="../server/add_post.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>
        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*">
        <button type="submit">Add Post</button>
    </form>
</div>

<!-- FOOTER -->

</body>
</html>
