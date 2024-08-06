<?php
include 'abstractDAO.php';
session_start();

header('Content-Type: application/json');

$response = [];

if (!isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You must be logged in to comment.';
    echo json_encode($response);
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
    $response['status'] = 'success';
    $response['comment_id'] = $mysqli->insert_id;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error: ' . $stmt->error;
}

$stmt->close();
$mysqli->close();

echo json_encode($response);
?>
