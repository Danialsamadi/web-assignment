<?php
include 'abstractDAO.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to comment.";
    exit();
}

$dao = new abstractDAO();
$mysqli = $dao->getMysqli();

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];
$content = $_POST['content'];

$sql = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iis", $post_id, $user_id, $content);

if ($stmt->execute()) {
    echo "New comment added successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
