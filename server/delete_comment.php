<?php
session_start();
include 'abstractDAO.php';

header('Content-Type: application/json');

$response = [];

if (!isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You must be logged in to delete a comment.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = trim($_POST['comment_id']);
    $user_id = $_SESSION['user_id'];

    $dao = new abstractDAO();
    $mysqli = $dao->getMysqli();

    // Ensure the comment belongs to the user
    $stmt = $mysqli->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
    if ($stmt === false) {
        $response['status'] = 'error';
        $response['message'] = 'Prepare failed: ' . $mysqli->error;
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param('ii', $comment_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $response['status'] = 'error';
        $response['message'] = 'You do not have permission to delete this comment.';
        echo json_encode($response);
        exit();
    }

    $stmt->close();

    $stmt = $mysqli->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    if ($stmt === false) {
        $response['status'] = 'error';
        $response['message'] = 'Prepare failed: ' . $mysqli->error;
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param('ii', $comment_id, $user_id);
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Comment deleted successfully.';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
    echo json_encode($response);
}
?>
