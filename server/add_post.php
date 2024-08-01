<?php
include 'abstractDAO.php';
session_start(); // Ensure session is started for user authentication

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $keywords = $_POST['keywords'];
    $categories = $_POST['categories']; // This will be an array of category IDs
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    if (!empty($title) && !empty($content)) {
        $dao = new abstractDAO();
        $mysqli = $dao->getMysqli();

        $sql = "INSERT INTO posts (title, content, user_id, image, keywords, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $mysqli->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . $mysqli->error);
        }

        $stmt->bind_param('ssiss', $title, $content, $user_id, $image, $keywords);
        $stmt->send_long_data(3, $image);
        $result = $stmt->execute();

        if ($result) {
            $post_id = $mysqli->insert_id;
            foreach ($categories as $category_id) {
                $cat_stmt = $mysqli->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
                $cat_stmt->bind_param('ii', $post_id, $category_id);
                $cat_stmt->execute();
                $cat_stmt->close();
            }
            header('Location: ../pages/index.php'); // Redirect to home page after successful post
        } else {
            die('Execute failed: ' . $stmt->error);
        }

        $stmt->close();
        $mysqli->close();
    } else {
        echo "Title and content cannot be empty.";
    }
} else {
    echo "Invalid request method.";
}
?>