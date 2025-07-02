<?php
include "./templates/header.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "./templates/db.php";

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT books.*, categories.name AS category_name 
    FROM books 
    LEFT JOIN categories ON books.category_id = categories.category_id 
    WHERE books.user_id = ? 
    ORDER BY books.created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h2><i class="fa fa-book"></i> My Posted Books</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="panel panel-default book-card" style="margin-bottom: 20px;">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= htmlspecialchars($row['title']) ?>
                        <?php if ($row['is_sold']): ?>
                            <span class="label label-danger" style="margin-left: 10px;"><i class="fa fa-check-circle"></i> Sold</span>
                        <?php else: ?>
                            <span class="label label-success" style="margin-left: 10px;"><i class="fa fa-clock"></i> Available</span>
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <p><i class="fa fa-star-half-alt"></i> Quality: <?= htmlspecialchars($row['quality']) ?></p>
                    <p><i class="fa fa-dollar-sign"></i> Price: $<?= number_format($row['price'], 2) ?></p>
                    <p><i class="fa fa-list"></i> Category: <?= htmlspecialchars($row['category_name']) ?></p>


                    <a href="editBook.php?id=<?= $row['book_id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="deleteBook.php?id=<?= $row['book_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?');">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">You haven't posted any books yet.</div>
    <?php endif; ?>
</div>

<?php include "./templates/footer.php"; ?>
