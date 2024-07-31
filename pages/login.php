<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<header>
    <h1>Blog Platform</h1>
    <nav>
        <a href="index.php">Home</a> |
        <a href="add_post.php">Add Post</a> |
        <a href="register.php">Register</a> |
        <a href="login.php">Login</a>
    </nav>
</header>

<main>
    <h2>Login</h2>
    <form action="../server/login_user.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
</main>

<footer>
    <p>&copy; 2024 Blog Platform. All rights reserved.</p>
</footer>
</body>
</html>
