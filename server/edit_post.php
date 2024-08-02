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
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imageUpdated = false;
    $imageError = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        if ($_FILES['image']['size'] <= 600 * 1024) { // 600 KB
            $image = file_get_contents($_FILES['image']['tmp_name']);
            $imageUpdated = true;
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

$mysqli->close();
?>
