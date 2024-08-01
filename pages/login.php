<?php
session_start(); // Ensure session is started for user authentication

if (isset($_SESSION['user_id'])) {
    // If the user is already logged in, redirect to the home page
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Your login logic here
    // Example:
    $username = $_POST['username'];
    $password = $_POST['password'];

    include '../server/abstractDAO.php';
    $dao = new abstractDAO();
    $mysqli = $dao->getMysqli();

    $sql = "SELECT id, password FROM users WHERE username=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && password_verify($password, $hashed_password)) {
        // User authenticated, set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit();
    } else {
        // Invalid credentials
        $error = "Invalid username or password.";
    }

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<!-- NAVBAR -->
<div class="navbar">
    <a class="nav-title-link" href="index.php">
        <span class="nav-title">Blog Platform</span>
    </a>
    <a class="button" href="index.php">
        <span class="button-text">Home</span>
    </a>
    <a class="button" href="add_post.php">
        <span class="button-text">Add Post</span>
    </a>
    <a class="button" href="register.php">
        <span class="button-text">Register</span>
    </a>
    <a class="button" href="login.php">
        <span class="button-text">Login</span>
    </a>
</div>

<!-- MAIN CONTENT -->
<div id="main-content">
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST" class="login-form">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="submit-button">Login</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
