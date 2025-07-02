<?php
session_start();
include "./templates/db.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM books WHERE book_id = ? AND user_id = ?");
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();

header("Location: my_books.php");
exit;
