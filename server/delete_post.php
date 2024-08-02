<?php
session_start(); // Ensure session is started for user authentication

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'abstractDAO.php';
$dao = new abstractDAO();
$mysqli = $dao->getMysqli();
$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'];

// Delete post
$delete_sql = "DELETE FROM posts WHERE id=? AND user_id=?";
$delete_stmt = $mysqli->prepare($delete_sql);
$delete_stmt->bind_param("ii", $post_id, $user_id);

if ($delete_stmt->execute()) {
    header('Location: ../pages/account.php?message=Post+deleted+successfully&message_type=success');
    exit();
} else {
    header('Location: ../pages/account.php?message=Error+deleting+post&message_type=error');
    exit();
}

$delete_stmt->close();
$mysqli->close();
?>
