<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "
    SELECT
        books.id,
        books.cover,
        books.title,
        books.author,
        books.category,
        (
            books.stock - COUNT(
                CASE
                    WHEN loans.book_id IS NOT NULL
                    AND loans.return_date IS NULL THEN 1
                END
            )
        ) AS available_stock,
        COUNT(
            CASE
                WHEN loans.book_id IS NOT NULL
                AND return_date IS NULL THEN 1
            END
        ) AS borrow,
        books.publisher,
        books.year_published,
        books.isbn,
        books.stock
    FROM books
        LEFT JOIN loans ON loans.book_id = books.id
    WHERE
        books.id = ?
    GROUP BY
        books.id
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();

        $book_loans = [];

        $sql = "
        SELECT
            loans.id,
            loans.member_id,
            members.name AS member_name,
            loans.borrow_date,
            loans.expected_return_date,
            CASE
                WHEN return_date IS NULL THEN 'Belum Dikembalikan'
                ELSE 'Dikembalikan'
            END AS status,
            CASE
                WHEN return_date IS NULL THEN '-'
                ELSE return_date
            END AS return_date,
            CASE
                WHEN fines IS NULL THEN '-'
                ELSE fines
            END AS fines
        FROM loans
            LEFT JOIN members ON member_id = members.id
        WHERE
            book_id = ?
        ORDER BY status ASC, loans.id ASC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $book['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $book_loans = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        ?>
        <script>alert("Buku tidak ditemukan."); location.href = "books.php"</script>
        <?php
    }
} else {
    $selectedCategory = $_GET['category'] ?? '';
    $params = [];

    $sql = "
        SELECT
            books.id,
            books.title,
            books.author,
            books.category,
            COUNT(
                CASE
                    WHEN loans.book_id IS NOT NULL
                    AND loans.return_date IS NULL THEN 1
                END
            ) AS borrow,
            (
                books.stock - COUNT(
                    CASE
                        WHEN loans.book_id IS NOT NULL
                        AND loans.return_date IS NULL THEN 1
                    END
                )
            ) AS available_stock
        FROM books
        LEFT JOIN loans ON loans.book_id = books.id
    ";

    // Add WHERE clause if a category is selected
    if ($selectedCategory) {
        $sql .= " WHERE books.category = ?";
        $params[] = $selectedCategory;
    }

    // Append GROUP BY and ORDER BY clauses
    $sql .= "
        GROUP BY books.id
        ORDER BY books.id ASC
    ";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param("s", ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);

    $sql = "SELECT category FROM books GROUP BY category ORDER BY category ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);

    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = trim($_GET['search']) . '*';

        $sql = "
        SELECT
            books.id,
            books.title,
            books.author,
            books.category,
            COUNT(
                CASE
                    WHEN loans.book_id IS NOT NULL
                    AND return_date IS NULL THEN 1
                END
            ) AS borrow,
            (
                books.stock - COUNT(
                    CASE
                        WHEN loans.book_id IS NOT NULL
                        AND loans.return_date IS NULL THEN 1
                    END
                )
            ) AS available_stock
        FROM books
            LEFT JOIN loans ON loans.book_id = books.id
        WHERE
            MATCH(books.id, books.title, books.author) AGAINST(? IN BOOLEAN MODE)
        GROUP BY
            books.id
        ORDER BY books.id ASC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $searchedBooks = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    if (isset($book)) {
        if (isset($_GET['action']) == 'edit') {
            echo "Edit " . $book['title'] . " - " . APP;
        } else {
            echo $book['title'] . " - " . APP;
        }
    } else {
        if (isset($_GET['action']) == 'add') {
            echo "Tambah Buku - " . APP;
        } else {
            echo "Buku - " . APP;
        }
    }
    ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body>
    <main>
        <div class="top mb16">
            <p class="f14">
                <?php
                if (isset($book)) {
                    if (isset($_GET['action']) == 'edit') {
                        echo "<a class='f-sub' href='books.php'>/ Buku </a><a class='f-sub' href='books.php?id=" . $book['id'] . "'>/ " . $book['id'] . "</a> / Edit";
                    } else {
                        echo "<a class='f-sub' href='books.php'>/ Buku</a> / " . $book['id'];
                    }
                } else {
                    if (isset($_GET['action']) == 'add') {
                        echo "<a class='f-sub' href='books.php'>/ Buku</a> / Tambah";
                    } else {
                        echo "/ Buku";
                    }
                }
                ?>
            </p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <?php if (isset($book)): ?>
            <?php if (isset($_GET['action']) == 'edit'): ?>
                <div class="head mb16">
                    <h3>Edit Buku</h3>
                    <div class="head-button">
                        <a class="sec-b head-b" onclick="confirmEditBook()">
                            <img src="/lokapustaka/img/save-light.png" alt="Simpan">
                            Simpan
                        </a>
                    </div>
                </div>
                <div class="container2">
                    <form id="editBookForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $book['id']; ?>">
                        <div class="input-container2 mb12">
                            <label for="cover" class="f12 f-sub mb4">Sampul</label>
                            <?php if (!empty($book['cover'])): ?>
                                <a href="/lokapustaka/image_view.php?id=<?= $book['id']; ?>" class="f12 f-redir" target="_blank"
                                    rel="noopener noreferrer">
                                    ( Lihat File )
                                </a>
                            <?php endif ?>
                            <input type="file" name="cover" accept=".png, .jpg, .jpeg">
                        </div>
                        <div class=" input-container2 mb12">
                            <label for="isbn" class="f12 f-sub mb4">ISBN*</label>
                            <input type="text" name="isbn" id="isbn" placeholder="e.g. 9786027742113"
                                value="<?= $book['isbn'] ?>" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="title" class="f12 f-sub mb4">Judul*</label>
                            <input type="text" name="title" id="title" placeholder="e.g. Koala Kumal"
                                value="<?= $book['title'] ?>" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="category" class="f12 f-sub mb4">Kategori*</label>
                            <select name="category" id="category" required>
                                <option value="" disabled <?= empty($book['category']) ? 'selected' : '' ?>>Pilih...</option>
                                <option value="Fiksi" <?= $book['category'] === 'Fiksi' ? 'selected' : '' ?>>Fiksi</option>
                                <option value="Non-Fiksi" <?= $book['category'] === 'Non-Fiksi' ? 'selected' : '' ?>>Non-Fiksi
                                </option>
                                <option value="Teknologi dan Sains" <?= $book['category'] === 'Teknologi dan Sains' ? 'selected' : '' ?>>Teknologi dan Sains</option>
                                <option value="Seni dan Budaya" <?= $book['category'] === 'Seni dan Budaya' ? 'selected' : '' ?>>
                                    Seni dan Budaya</option>
                                <option value="Kesehatan" <?= $book['category'] === 'Kesehatan' ? 'selected' : '' ?>>Kesehatan
                                </option>
                                <option value="Pendidikan Anak" <?= $book['category'] === 'Pendidikan Anak' ? 'selected' : '' ?>>
                                    Pendidikan Anak</option>
                                <option value="Referensi" <?= $book['category'] === 'Referensi' ? 'selected' : '' ?>>Referensi
                                </option>
                                <option value="Hukum" <?= $book['category'] === 'Hukum' ? 'selected' : '' ?>>Hukum</option>
                                <option value="Pengembangan Diri" <?= $book['category'] === 'Pengembangan Diri' ? 'selected' : '' ?>>Pengembangan Diri</option>
                                <option value="Petualangan" <?= $book['category'] === 'Petualangan' ? 'selected' : '' ?>>
                                    Petualangan</option>
                                <option value="Karya Ilmiah" <?= $book['category'] === 'Karya Ilmiah' ? 'selected' : '' ?>>Karya
                                    Ilmiah</option>
                            </select>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="author" class="f12 f-sub mb4">Pengarang / Penulis*</label>
                            <input type="text" name="author" id="author" placeholder="e.g. Raditya Dika"
                                value="<?= $book['author'] ?>" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="publisher" class="f12 f-sub mb4">Penerbit*</label>
                            <input type="text" name="publisher" id="publisher" placeholder="e.g. Gramedia Pustaka Utama"
                                value="<?= $book['publisher'] ?>" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="year_published" class="f12 f-sub mb4">Tahun Terbit*</label>
                            <input type="text" name="year_published" id="year_published" placeholder="e.g. 2015"
                                value="<?= $book['year_published'] ?>" required>
                        </div>
                        <div class="input-container2">
                            <label for="stock" class="f12 f-sub mb4">Total Buku*</label>
                            <input type="text" name="stock" id="stock" placeholder="e.g. 12" value="<?= $book['stock'] ?>"
                                required>
                        </div>
                    </form>
                </div><br>
            <?php else: ?>
                <div class="head mb16">
                    <div class="title">
                        <p class="f14 f-sub">Id Buku</p>
                        <h3><?= $book['id'] ?></h3>
                    </div>
                    <div class="head-button">
                        <a href="?id=<?= $book['id'] ?>&action=edit" class="thi-b head-b mr8">
                            <img src="/lokapustaka/img/edit-dark.png" alt="Edit">
                            Edit
                        </a>
                        <a href="#" onclick="deleteBook('<?= $book['id'] ?>')" class="red-b head-b">
                            <img src="/lokapustaka/img/close-light.png" alt="Hapus">
                            Hapus
                        </a>
                    </div>
                </div>
                <div class="container book mb16">
                    <?php if ($book['cover'] !== NULL): ?>
                        <img src="/lokapustaka/image_view.php?id=<?= $book["id"]; ?>" alt="Cover" class="cover mr12">
                    <?php else: ?>
                        <img src="/lokapustaka/img/default-cover.png" alt="Cover" class="cover mr12">
                    <?php endif ?>
                    <div class="content">
                        <p class="f12 f-sub">ISBN</p>
                        <h4 class="mb4"><?= formatISBN($book['isbn']) ?></h4>
                        <p class="f12 f-sub">Judul Buku</p>
                        <h4 class="mb4"><?= $book['title'] ?></h4>
                        <p class="f12 f-sub">Pengarang / Penulis</p>
                        <h4 class="mb4"><?= $book['author'] ?></h4>
                        <p class="f12 f-sub">Kategori</p>
                        <h4 class="mb4"><?= $book['category'] ?></h4>
                        <p class="f12 f-sub">Stok Tersedia</p>
                        <h4 class="mb4"><?= $book['available_stock'] ?></h4>
                        <p class="f12 f-sub">Dipinjam</p>
                        <h4 class="mb4"><?= $book['borrow'] ?></h4>
                        <p class="f12 f-sub">Penerbit</p>
                        <h4 class="mb4"><?= $book['publisher'] ?></h4>
                        <p class="f12 f-sub">Tahun Terbit</p>
                        <h4><?= $book['year_published'] ?></h4>
                    </div>
                </div>
                <p class="f-sub mb8">Riwayat Peminjaman</p>
                <?php if (!empty($book_loans)): ?>
                    <table class="sortable">
                        <thead>
                            <tr>
                                <th class="w75" data-column="id">Id</th>
                                <th class="w75" data-column="member_id">Id Anggota</th>
                                <th data-column="member_name">Nama</th>
                                <th class="w75" data-column="borrow_date">Tgl Pinjam</th>
                                <th class="w75" data-column="expected_return_date">Tenggat Waktu</th>
                                <th class="w150" data-column="status">
                                    Status
                                    <img src="/lokapustaka/img/sort-asc.png" alt="Sort Icon" class="sort-icon">
                                </th>
                                <th class="w75" data-column="return_date">Tgl Pengembalian</th>
                                <th class="w75" data-column="fines">Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($book_loans as $book_loan): ?>
                                <tr>
                                    <td class="t-center" onclick="window.location.href='loans.php?id=<?= $book_loan['id'] ?>';"
                                        style="cursor: pointer;">
                                        <?= $book_loan['id'] ?>
                                    </td>
                                    <td class="t-center" onclick="window.location.href='members.php?id=<?= $book_loan['member_id'] ?>';"
                                        style="cursor: pointer;">
                                        <?= $book_loan['member_id'] ?>
                                    </td>
                                    <td onclick="window.location.href='members.php?id=<?= $book_loan['member_id'] ?>';"
                                        style="cursor: pointer;">
                                        <?= $book_loan['member_name'] ?>
                                    </td>
                                    <td class="t-center"><?= formatDate($book_loan['borrow_date']) ?></td>
                                    <td class="t-center"><?= formatDate($book_loan['expected_return_date']) ?></td>
                                    <td class="t-center"><?= $book_loan['status'] ?></td>
                                    <td class="t-center">
                                        <?= ($book_loan['return_date'] !== '-') ? formatDate($book_loan['return_date']) : $book_loan['return_date'] ?>
                                    </td>
                                    <td><?= ($book_loan['fines'] !== '-') ? 'Rp. ' . $book_loan['fines'] : $book_loan['fines'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table><br>
                <?php else: ?>
                    <p class="no-results">Tidak ada riwayat peminjaman.</p>
                <?php endif ?>
            <?php endif ?>
        <?php else: ?>
            <?php if (isset($_GET['action']) == 'add'): ?>
                <div class="head mb16">
                    <h3>Tambah Buku</h3>
                    <div class="head-button">
                        <a class="sec-b head-b" onclick="confirmAddBook()">
                            <img src="/lokapustaka/img/save-light.png" alt="Simpan">
                            Simpan
                        </a>
                    </div>
                </div>
                <div class="container2">
                    <form id="addBookForm" method="POST" enctype="multipart/form-data">
                        <div class="input-container2 mb12">
                            <label for="cover" class="f12 f-sub mb4">Sampul</label>
                            <input type="file" name="cover" accept=".png, .jpg, .jpeg">
                        </div>
                        <div class="input-container2 mb12">
                            <label for="isbn" class="f12 f-sub mb4">ISBN*</label>
                            <input type="text" name="isbn" id="isbn" placeholder="e.g. 9786027742113" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="title" class="f12 f-sub mb4">Judul*</label>
                            <input type="text" name="title" id="title" placeholder="e.g. Koala Kumal" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="category" class="f12 f-sub mb4">Kategori*</label>
                            <select name="category" id="category" required>
                                <option value="" disabled selected>Pilih...</option>
                                <option value="Fiksi">Fiksi</option>
                                <option value="Non-Fiksi">Non-Fiksi</option>
                                <option value="Teknologi dan Sains">Teknologi dan Sains</option>
                                <option value="Seni dan Budaya">Seni dan Budaya</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Pendidikan Anak">Pendidikan Anak</option>
                                <option value="Referensi">Referensi</option>
                                <option value="Hukum">Hukum</option>
                                <option value="Pengembangan Diri">Pengembangan Diri</option>
                                <option value="Petualangan">Petualangan</option>
                                <option value="Karya Ilmiah">Karya Ilmiah</option>
                            </select>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="author" class="f12 f-sub mb4">Pengarang / Penulis*</label>
                            <input type="text" name="author" id="author" placeholder="e.g. Raditya Dika" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="publisher" class="f12 f-sub mb4">Penerbit*</label>
                            <input type="text" name="publisher" id="publisher" placeholder="e.g. Gramedia Pustaka Utama"
                                required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="year_published" class="f12 f-sub mb4">Tahun Terbit*</label>
                            <input type="text" name="year_published" id="year_published" placeholder="e.g. 2015" required>
                        </div>
                        <div class="input-container2">
                            <label for="stock" class="f12 f-sub mb4">Total Buku*</label>
                            <input type="text" name="stock" id="stock" placeholder="e.g. 12" required>
                        </div>
                    </form>
                </div><br>
            <?php else: ?>
                <div class="head mb16">
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
                                placeholder="Cari Id, Judul, Pengarang"
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </form>
                        <a href="?action=add" class="sec-b head-b">
                            <img src="/lokapustaka/img/plus-light.png" alt="Tambah">
                            Tambah
                        </a>
                    </div>
                </div>
                <?php if (isset($_GET['search']) && !empty(trim($_GET['search']))): ?>
                    <?php if (!empty($searchedBooks)): ?>
                        <table class="sortable">
                            <thead>
                                <tr>
                                    <th class="w75" data-column="id">
                                        Id
                                        <img src="/lokapustaka/img/sort-asc.png" alt="Sort Icon" class="sort-icon">
                                    </th>
                                    <th data-column="title">Judul</th>
                                    <th data-column="author">Pengarang</th>
                                    <th class="w150" data-column="category">Kategori</th>
                                    <th class="w75" data-column="borrow">Dipinjam</th>
                                    <th class="w75" data-column="available_stock">Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchedBooks as $book): ?>
                                    <tr onclick="window.location.href='?id=<?= $book['id'] ?>';" style="cursor: pointer;">
                                        <td class="t-center"><?= $book['id'] ?></td>
                                        <td><?= $book['title'] ?></td>
                                        <td><?= $book['author'] ?></td>
                                        <td><?= $book['category'] ?></td>
                                        <td class="t-center"><?= $book['borrow'] ?></td>
                                        <td class="t-center"><?= $book['available_stock'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table><br>
                    <?php else: ?>
                        <p class="no-results">Tidak ada hasil yang ditemukan untuk "<?= htmlspecialchars($_GET['search']) ?>".</p>
                    <?php endif ?>
                <?php else: ?>
                    <table class="sortable">
                        <thead>
                            <tr>
                                <th class="w75" data-column="id">
                                    Id
                                    <img src="/lokapustaka/img/sort-asc.png" alt="Sort Icon" class="sort-icon">
                                </th>
                                <th data-column="title">Judul</th>
                                <th data-column="author">Pengarang</th>
                                <th data-column="category" class="w150">Kategori</th>
                                <th class="w75" data-column="borrow">Dipinjam</th>
                                <th class="w75" data-column="available_stock">Tersedia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr onclick="window.location.href='?id=<?= $book['id'] ?>';" style="cursor: pointer;">
                                    <td class="t-center"><?= $book['id'] ?></td>
                                    <td><?= $book['title'] ?></td>
                                    <td><?= $book['author'] ?></td>
                                    <td><?= $book['category'] ?></td>
                                    <td class="t-center"><?= $book['borrow'] ?></td>
                                    <td class="t-center"><?= $book['available_stock'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table><br>
                <?php endif ?>
            <?php endif ?>
        <?php endif ?>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>