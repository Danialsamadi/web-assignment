<?php
session_start(); // Ensure session is started for user authentication
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Platform</title>
    <link rel="stylesheet" href="../styles/style.css">
    <script>
        function confirmLogoutAndRegister() {
            if (confirm("You will be logged out to register a new user. Do you want to continue?")) {
                window.location.href = "../server/logout.php?redirect=register";
            }
        }
    </script>
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
    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="button" href="javascript:void(0);" onclick="confirmLogoutAndRegister()">
            <span class="button-text">Register</span>
        </a>
        <a class="button" href="../server/logout.php">
            <span class="button-text">Logout</span>
        </a>
    <?php else: ?>
        <a class="button" href="register.php">
            <span class="button-text">Register</span>
        </a>
        <a class="button" href="login.php">
            <span class="button-text">Login</span>
        </a>
    <?php endif; ?>
</div>
</body>
</html>

<!-- MAIN CONTENT -->
<div id="main-content">
    <h2>Register</h2>
    <form action="../server/register_user.php" method="post" id="registerForm">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <input type="submit" value="Register" class="submit-button">
    </form>
</div>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
