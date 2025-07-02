<?php include "./templates/db.php"; ?>
<?php include "./templates/header.php"; ?>

<h2>Available Books</h2>

<!-- Search and Filter -->
<form method="GET" class="form-inline" style="margin-bottom: 20px;">
    <div class="form-group" style="margin-right: 10px;">
        <label>
            <i class="fa fa-search"></i>
            <input type="text" name="search" class="form-control" placeholder="Search by title or quality" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </label>
    </div>

    <div class="form-group" style="margin-right: 10px;">
        <label>
            <i class="fa fa-list"></i>
            <select name="category" class="form-control">
                <option value="">All Categories</option>
                <?php
                $catResult = $conn->query("SELECT * FROM categories");
                while ($cat = $catResult->fetch_assoc()):
                    $selected = (isset($_GET['category']) && $_GET['category'] == $cat['category_id']) ? 'selected' : '';
                    echo "<option value='{$cat['category_id']}' $selected>" . htmlspecialchars($cat['name']) . "</option>";
                endwhile;
                ?>
            </select>
        </label>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fa fa-filter"></i> Filter
    </button>
</form>


<?php
// Build query with filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT books.*, users.username, categories.name AS category_name
        FROM books
        JOIN users ON books.user_id = users.user_id
        LEFT JOIN categories ON books.category_id = categories.category_id
        WHERE is_sold = FALSE";

if (!empty($search)) {
    $searchTerm = "%$search%";
    $sql .= " AND (books.title LIKE ? OR books.quality LIKE ?)";
}
if (!empty($category)) {
    $sql .= " AND books.category_id = ?";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

// Bind parameters
if (!empty($search) && !empty($category)) {
    $stmt->bind_param("ssi", $searchTerm, $searchTerm, $category);
} elseif (!empty($search)) {
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
} elseif (!empty($category)) {
    $stmt->bind_param("i", $category);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
    <div class="panel panel-default book-card">
    <div class="panel-heading">
        <h3 class="panel-title"><?= htmlspecialchars($row['title']) ?></h3>
    </div>
    <div class="panel-body">
        <p><i class="fa fa-star-half-alt"></i> Quality: <?= $row['quality'] ?></p>
        <p><i class="fa fa-dollar-sign"></i> Price: $<?= number_format($row['price'], 2) ?></p>
        <p><i class="fa fa-tag"></i> Category: <?= htmlspecialchars($row['category_name']) ?></p>
        <p><i class="fa fa-user"></i> Posted by: <?= htmlspecialchars($row['username']) ?></p>
        <a href="book_details.php?id=<?= $row['book_id'] ?>" class="btn btn-success">
            <i class="fa fa-book-open"></i> View / Buy
        </a>
    </div>
</div>

<?php
    endwhile;
else:
    echo "<p>No books match your search/filter.</p>";
endif;

$conn->close();
?>

<?php include "./templates/footer.php"; ?>
