<?php
include 'abstractDAO.php';
session_start(); // Ensure session is started for user authentication

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    if (!empty($title) && !empty($content)) {
        $dao = new abstractDAO();
        $mysqli = $dao->getMysqli();

        $sql = "INSERT INTO posts (title, content, user_id, image, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $mysqli->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . $mysqli->error);
        }

        $stmt->bind_param('ssis', $title, $content, $user_id, $image);
        $stmt->send_long_data(3, $image);
        $result = $stmt->execute();

        if ($result) {
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
