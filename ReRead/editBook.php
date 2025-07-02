<?php
include "./templates/db.php";
include "./templates/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid book ID.</div>";
    include "./templates/footer.php";
    exit;
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch current book data
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ? AND user_id = ?");
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<div class='alert alert-danger'>Book not found or access denied.</div>";
    include "./templates/footer.php";
    exit;
}

$book = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $quality = $_POST['quality'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];

    $updateStmt = $conn->prepare("UPDATE books SET title = ?, quality = ?, price = ?, category_id = ? WHERE book_id = ? AND user_id = ?");
    $updateStmt->bind_param("ssdiii", $title, $quality, $price, $category_id, $book_id, $user_id);

    if ($updateStmt->execute()) {
        echo "<div class='alert alert-success'>Book updated successfully!</div>";
        // Refresh data
        $book = array_merge($book, [
            'title' => $title,
            'quality' => $quality,
            'price' => $price,
            'category_id' => $category_id
        ]);
    } else {
        echo "<div class='alert alert-danger'>Failed to update book.</div>";
    }
}
?>

<div class="container">
    <h2><i class="fa fa-edit"></i> Edit Book</h2>
    <form method="POST" class="form-horizontal" style="max-width: 600px;">

        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($book['title']) ?>">
        </div>

        <div class="form-group">
            <label>Quality:</label>
            <input type="text" name="quality" class="form-control" required value="<?= htmlspecialchars($book['quality']) ?>">
        </div>

        <div class="form-group">
            <label>Price (USD):</label>
            <input type="number" name="price" step="0.01" class="form-control" required value="<?= htmlspecialchars($book['price']) ?>">
        </div>

        <div class="form-group">
            <label>Category:</label>
            <select name="category" class="form-control" required>
                <option value="">Select Category</option>
                <?php
                $catResult = $conn->query("SELECT * FROM categories");
                while ($cat = $catResult->fetch_assoc()):
                    $selected = ($cat['category_id'] == $book['category_id']) ? "selected" : "";
                    echo "<option value='{$cat['category_id']}' $selected>" . htmlspecialchars($cat['name']) . "</option>";
                endwhile;
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Changes
        </button>
        <a href="my_books.php" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Cancel
        </a>
    </form>
</div>

<?php include "./templates/footer.php"; ?>
