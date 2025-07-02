<?php
include "./templates/header.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "./templates/db.php";

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $quality = $_POST['quality'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO books (title, quality, price, user_id, category_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdii", $title, $quality, $price, $user_id, $category_id);
    $stmt->execute();

    $message = "Book added successfully!";
}
?>



<h2>Add a New Book</h2>

<?php if ($message): ?>
    <p style="color:green;"><?= $message ?></p>
<?php endif; ?>

<form method="post">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Quality (%):</label><br>
    <select name="quality">
        <option value="20%">20%</option>
        <option value="30%">30%</option>
        <option value="50%">50%</option>
        <option value="70%">70%</option>
        <option value="90%">90%</option>
    </select><br><br>

    <label>Price ($):</label><br>
    <input type="number" step="1" name="price" required><br><br>

    <label>Category:</label><br>
    <select name="category" required>
        <option value="">-- Select Category --</option>
        <?php
        $cats = $conn->query("SELECT * FROM categories");
        while ($cat = $cats->fetch_assoc()):
            echo "<option value='{$cat['category_id']}'>" . htmlspecialchars($cat['name']) . "</option>";
        endwhile;
        ?>
    </select><br><br>

    <input type="submit" value="Add Book">
</form>

<?php include "./templates/footer.php"; ?>
