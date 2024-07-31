<?php
include 'abstractDAO.php';

$dao = new abstractDAO();
$mysqli = $dao->getMysqli();

session_start();

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT id, password FROM users WHERE username=?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();
    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        header("Location: ../pages/index.php");
    } else {
        echo "Invalid password";
    }
} else {
    echo "No user found";
}

$stmt->close();
$mysqli->close();
?>
