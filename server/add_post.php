<?php
include 'abstractDAO.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$dao = new abstractDAO();
$mysqli = $dao->getMysqli();

$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$content = $_POST['content'];

$sql = "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iss", $user_id, $title, $content);

if ($stmt->execute()) {
    echo "New post created successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
