<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside_index.php";

// Initialize variables for search and category
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch categories for the dropdown
$sql = "SELECT DISTINCT category FROM books";
$stmt = $conn->prepare($sql);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch books from the database based on search and category
$sql = "SELECT id, cover, title, author, available_stock FROM books WHERE 1=1";

// Add search condition using full-text search
if (!empty($search)) {
    $sql .= " AND MATCH(books.id, books.title, books.isbn) AGAINST(? IN BOOLEAN MODE)";
}

// Add category condition
if (!empty($selectedCategory)) {
    $sql .= " AND category = ?";
}

$stmt = $conn->prepare($sql);

// Bind parameters
if (!empty($search) && !empty($selectedCategory)) {
    $likeSearch = "$search*"; // Adding wildcard for full-text search
    $stmt->bind_param("ss", $likeSearch, $selectedCategory);
} elseif (!empty($search)) {
    $likeSearch = "$search*"; // Adding wildcard for full-text search
    $stmt->bind_param("s", $likeSearch);
} elseif (!empty($selectedCategory)) {
    $stmt->bind_param("s", $selectedCategory);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body>
    <main>
        <div class="top mb16">
            <h2>Selamat Datang di <?= APP ?>!</h2>
            <p id="wib-time" class="f14"><?= $current_time . " WIB" ?></p>
        </div>
        <hr class="mb8">
        <div class="head m-hor">
            <div class="title">
                <h3 class="mb4">Daftar Buku</h3>
                <span class="subtitle">
                    <label for="category" class="f-sub f12 mr4">Pencarian Berdasarkan Kategori:</label>
                    <form id="categoryForm" method="GET" action="">
                        <select name="category" id="category" class="f12"
                            onchange="document.getElementById('categoryForm').submit()">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['category']) ?>" <?= isset($_GET['category']) && $_GET['category'] == $category['category'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['category']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </span>
            </div>
            <div class="head-button">
                <form action="" method="get">
                    <input type="text" class="search-b head-b mr8" name="search" id="search"
                        placeholder="Cari ID, ISBN, Judul"
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </form>
                <a href="?action=add" class="sec-b head-b">
                    <img src="/lokapustaka/img/plus-light.png" alt="Tambah">
                    Tambah
                </a>
            </div>
        </div>
        <div class="main">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($book = $result->fetch_assoc()): ?>
                    <div class="container3 <?= $book['available_stock'] == 0 ? 'out-of-stock' : '' ?>"
                        onclick="location.href='/lokapustaka/pages/index_books.php?id=<?= $book['id'] ?>'"> <img
                            src="<?= !empty($book['cover']) ? '/lokapustaka/image_view.php?id=' . $book['id'] : '/lokapustaka/img/default-cover.png' ?>"
                            alt="Cover" class="cover mb8">
                        <p class="f-sub f12"><?= $book['id'] ?></p>
                        <p class="f12"><?= $book['author'] ?></p>
                        <p class="font-bold f14"><?= $book['title'] ?></p>
                        <p class="f-sub f12"><?= $book['available_stock'] ?> Tersedia</p>
                        <div class="overlay">Stok Habis</div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-results">Tidak ada buku yang tersedia.</p>
            <?php endif; ?>
        </div>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>