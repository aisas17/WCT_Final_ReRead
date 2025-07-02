<?php
include "./templates/db.php";
include "./templates/header.php";

if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger'>You must be logged in to buy a book.</div>";
    include "./templates/footer.php";
    exit;
}

if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    echo "<div class='alert alert-danger'>Invalid book ID.</div>";
    include "./templates/footer.php";
    exit;
}

$book_id = (int) $_GET['book_id'];
$user_id = $_SESSION['user_id'];

// Check if the book exists and is not already sold
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ? AND is_sold = 0");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>This book is not available for purchase.</div>";
    include "./templates/footer.php";
    exit;
}

// Update book status to sold
$update = $conn->prepare("UPDATE books SET is_sold = 1 WHERE book_id = ?");
$update->bind_param("i", $book_id);
$update->execute();

echo "<div class='alert alert-success'>You have successfully purchased the book!</div>";
echo "<a href='index.php' class='btn btn-primary'><i class='fa fa-arrow-left'></i> Back to Home</a>";

include "./templates/footer.php";
?>
