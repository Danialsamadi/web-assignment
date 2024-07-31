<!-- pages/add_post.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<h2>Add Post</h2>
<form action="../server/add_post.php" method="POST">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>
    <label for="content">Content:</label>
    <textarea id="content" name="content" required></textarea>
    <button type="submit">Add Post</button>
</form>
</body>
</html>
