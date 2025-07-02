<?php
include "./templates/db.php";
include "./templates/header.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid book ID.</div>";
    include "./templates/footer.php";
    exit;
}

$book_id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT books.*, users.username, categories.name AS category_name 
                        FROM books 
                        JOIN users ON books.user_id = users.user_id 
                        LEFT JOIN categories ON books.category_id = categories.category_id 
                        WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Book not found.</div>";
    include "./templates/footer.php";
    exit;
}

$book = $result->fetch_assoc();
?>

<div class="container">
    <div class="panel panel-default" style="margin-top: 20px;">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-book"></i> <?= htmlspecialchars($book['title']) ?></h2>
        </div>
        <div class="panel-body">
            <p><i class="fa fa-star-half-alt"></i> <strong>Quality:</strong> <?= htmlspecialchars($book['quality']) ?></p>
            <p><i class="fa fa-dollar-sign"></i> <strong>Price:</strong> $<?= number_format($book['price'], 2) ?></p>
            <p><i class="fa fa-list"></i> <strong>Category:</strong> <?= htmlspecialchars($book['category_name']) ?></p>
            <p><i class="fa fa-user"></i> <strong>Seller:</strong> <?= htmlspecialchars($book['username']) ?></p>
            <p><i class="fa fa-clock"></i> <strong>Posted on:</strong> <?= date("F j, Y", strtotime($book['created_at'])) ?></p>

            <?php if (!$book['is_sold']): ?>
                <a href="order.php?book_id=<?= $book['book_id'] ?>" class="btn btn-success">
                    <i class="fa fa-cart-plus"></i> Buy Now
                </a>
            <?php else: ?>
                <div class="alert alert-info">This book has already been sold.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "./templates/footer.php"; ?>
