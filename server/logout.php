<?php
session_start();
session_unset();
session_destroy();

if (isset($_GET['redirect']) && $_GET['redirect'] === 'register') {
    header('Location: ../pages/register.php');
} else {
    header('Location: ../pages/index.php'); // Adjust the path to your homepage accordingly
}
exit();
?>
