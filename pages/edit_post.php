<?php
session_start(); // Ensure session is started for user authentication

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../server/abstractDAO.php';
$dao = new abstractDAO();
$mysqli = $dao->getMysqli();
$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'];

// Fetch post details
$post_sql = "SELECT title, content, image FROM posts WHERE id=? AND user_id=?";
$post_stmt = $mysqli->prepare($post_sql);
$post_stmt->bind_param("ii", $post_id, $user_id);
$post_stmt->execute();
$post_stmt->bind_result($title, $content, $image);
$post_stmt->fetch();
$post_stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_image'])) {
        // Delete image
        $update_sql = "UPDATE posts SET image=NULL WHERE id=? AND user_id=?";
        $update_stmt = $mysqli->prepare($update_sql);
        $update_stmt->bind_param("ii", $post_id, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
        header('Location: edit_post.php?id=' . $post_id);
        exit();
    } else {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $imageUpdated = false;
        $imageError = '';

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            if ($_FILES['image']['size'] <= 600 * 1024) { // 600 KB
                $image = file_get_contents($_FILES['image']['tmp_name']);
                $imageUpdated = true;

                // Add error logging
                error_log('Image size: ' . $_FILES['image']['size']);
                error_log('Image error: ' . $_FILES['image']['error']);
                error_log('Image content length: ' . strlen($image));
            } else {
                die('Image size should not exceed 600 KB');
            }
        } elseif ($_FILES['image']['error'] != 4) { // 4 means no file was uploaded
            $imageError = "Image upload error: " . $_FILES['image']['error'];
        }

        if (!empty($title) && !empty($content)) {
            if ($imageUpdated) {
                $update_sql = "UPDATE posts SET title=?, content=?, image=? WHERE id=? AND user_id=?";
                $update_stmt = $mysqli->prepare($update_sql);
                if ($update_stmt) {
                    $update_stmt->bind_param("sssii", $title, $content, $image, $post_id, $user_id);
                    $update_stmt->send_long_data(2, $image);
                } else {
                    $error = "Prepare failed: " . $mysqli->error;
                }
            } else {
                $update_sql = "UPDATE posts SET title=?, content=? WHERE id=? AND user_id=?";
                $update_stmt = $mysqli->prepare($update_sql);
                if ($update_stmt) {
                    $update_stmt->bind_param("ssii", $title, $content, $post_id, $user_id);
                } else {
                    $error = "Prepare failed: " . $mysqli->error;
                }
            }

            if ($update_stmt->execute()) {
                header('Location: account.php?message=Post+updated+successfully&message_type=success');
                exit();
            } else {
                $error = "Error updating post: " . $update_stmt->error;
            }

            $update_stmt->close();
        } else {
            $error = "Title and content cannot be empty.";
        }
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="../styles/style.css">
    <script>
        function validateImageSize() {
            const fileInput = document.getElementById('image');
            const file = fileInput.files[0];
            if (file && file.size > 600 * 1024) { // 600 KB
                alert('Image size should not exceed 600 KB');
                return false;
            }
            return true;
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
    <a class="button" href="account.php">
        <span class="button-text">My Account</span>
    </a>
    <a class="button" href="../server/logout.php">
        <span class="button-text">Logout</span>
    </a>
</div>

<!-- MAIN CONTENT -->
<div id="main-content">
    <h2>Edit Post</h2>
    <?php if (isset($error)): ?>
        <div class="alert error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif (isset($imageError)): ?>
        <div class="alert error">
            <?php echo htmlspecialchars($imageError); ?>
        </div>
    <?php endif; ?>
    <form action="edit_post.php?id=<?php echo $post_id; ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateImageSize()">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        <label for="content">Content:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>
        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($image)): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" alt="Post Image" style="max-width:100%;height:auto;">
            <button type="submit" name="delete_image" class="delete-image-button">Delete Image</button>
        <?php endif; ?>
        <button type="submit">Update Post</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
