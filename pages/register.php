<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../styles/style.css">
    <script src="../scripts/validation.js" defer></script>
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
