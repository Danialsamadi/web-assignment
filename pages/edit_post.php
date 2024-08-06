<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../server/abstractDAO.php';
$dao = new abstractDAO();
$mysqli = $dao->getMysqli();
$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'];

$post_sql = "SELECT title, content, image FROM posts WHERE id=? AND user_id=?";
$post_stmt = $mysqli->prepare($post_sql);
$post_stmt->bind_param("ii", $post_id, $user_id);
$post_stmt->execute();
$post_stmt->bind_result($title, $content, $image);
$post_stmt->fetch();
$post_stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_image'])) {
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

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            if ($_FILES['image']['size'] <= 600 * 1024) {
                $image = file_get_contents($_FILES['image']['tmp_name']);
                $imageUpdated = true;
            } else {
                die('Image size should not exceed 600 KB');
            }
        } elseif ($_FILES['image']['error'] != 4) {
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
</head>
<body>
<header class="navbar">
    <a class="nav-title-link" href="index.php"><span class="nav-title">Blog Platform</span></a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="add_post.php">Add Post</a>
        <a href="account.php">My Account</a>
        <a href="../server/logout.php">Logout</a>
    </nav>
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
</header>

<main id="main-content">
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
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        </div>
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <?php if (!empty($image)): ?>
            <div class="post">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" alt="Post Image">
                <div class="image-buttons">
                    <button type="submit" name="delete_image" class="delete">Delete Image</button>
                </div>
            </div>
        <?php endif; ?>
        <div class="image-buttons">
            <button type="submit">Update Post</button>
        </div>
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
