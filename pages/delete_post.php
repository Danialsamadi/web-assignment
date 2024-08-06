<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'abstractDAO.php';
    $dao = new abstractDAO();
    $mysqli = $dao->getMysqli();

    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . $mysqli->error);
    }

    $stmt->bind_param('ii', $post_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: ../pages/index.php?message=Post+deleted+successfully&message_type=success');
    } else {
        header('Location: ../pages/index.php?message=Failed+to+delete+post&message_type=error');
    }

    $stmt->close();
    $mysqli->close();
}
