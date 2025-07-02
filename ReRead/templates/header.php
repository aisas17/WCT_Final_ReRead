<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>ReRead | Book Resell</title>
    <link rel="stylesheet" href="includes/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>
<header>
    <h1>📚 ReRead - Resell Your Books</h1>
    <nav>
        <a href="index.php">Home</a> |
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="addBook.php">Post Book</a> |
            <a href="my_books.php">My Books</a> |
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> |
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
