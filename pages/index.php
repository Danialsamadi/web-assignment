<!-- pages/index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<h2>Blog Posts</h2>
<div id="posts">
    <?php
    include '../server/db_connect.php';
    $sql = "SELECT posts.id, posts.title, posts.content, users.username, posts.created_at 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY posts.created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<article>";
            echo "<h3>" . $row['title'] . "</h3>";
            echo "<p>by " . $row['username'] . " on " . $row['created_at'] . "</p>";
            echo "<div>" . $row['content'] . "</div>";
            echo "</article>";
        }
    } else {
        echo "No posts found.";
    }

    $conn->close();
    ?>
</div>
</body>
</html>
