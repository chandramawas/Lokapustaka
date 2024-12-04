<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside_index.php";

if (empty($_SESSION['members_id']) || empty($_SESSION['members_name']) || empty($_SESSION['members_expired_date']) || empty($_SESSION['members_phone_num'])) {
    echo '<script>alert("Sesi Habis. Silahkan login kembali!"); location.href = "/lokapustaka/login.php"</script>';
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "
    SELECT
        loans.id,
        CASE
            WHEN books.cover IS NOT NULL THEN 1
            ELSE NULL
        END cover,
        CASE
            WHEN loans.book_id IS NULL THEN '[Buku Dihapus]'
            ELSE loans.book_id
        END book_id,
        CASE
            WHEN books.title IS NULL THEN '[Buku Dihapus]'
            ELSE books.title
        END book_title,
        loans.borrow_date,
        loans.expected_return_date,
        CASE
            WHEN max_extend = 0 THEN 'Belum'
            ELSE 'Sudah'
        END extend,
        CASE
            WHEN return_date IS NULL THEN 'Belum Dikembalikan'
            ELSE 'Dikembalikan'
        END AS status,
        loans.return_date,
        loans.fines
    FROM loans
        LEFT JOIN members ON loans.member_id = members.id
        LEFT JOIN books ON loans.book_id = books.id
    WHERE
        loans.id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $loan = $result->fetch_assoc();
    } else {
        $loan = null; // No book found

        echo "<script>alert('Data Peminjaman tidak ditemukan.'); history.back()</script>";
    }
}

$sql = "
SELECT
    loans.id,
    CASE
        WHEN books.cover IS NOT NULL THEN 1
        ELSE NULL
    END cover,
    loans.book_id,
    books.title,
    loans.expected_return_date,
    CASE
        WHEN max_extend = 0 THEN 'Belum'
        ELSE 'Sudah'
    END extend,
    CASE
        WHEN DATEDIFF(
            NOW(),
            loans.expected_return_date
        ) <= 0 THEN NULL
        ELSE DATEDIFF(
            NOW(),
            loans.expected_return_date
        )
    END AS day_late
FROM loans
    LEFT JOIN books ON loans.book_id = books.id
WHERE
    loans.member_id = ?
    AND loans.return_date IS NULL
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['members_id']);
$stmt->execute();
$result = $stmt->get_result();
$cur_loan = $result->fetch_assoc();

$sql = "
SELECT
    loans.id,
    loans.book_id,
    CASE
        WHEN books.cover IS NOT NULL THEN 1
        ELSE NULL
    END cover,
    loans.borrow_date,
    books.title,
    CASE
        WHEN DATEDIFF(
            return_date,
            loans.expected_return_date
        ) <= 0 THEN NULL
        ELSE DATEDIFF(
            return_date,
            loans.expected_return_date
        )
    END AS day_late
FROM loans
    LEFT JOIN books ON loans.book_id = books.id
WHERE
    return_date IS NOT NULL
    AND member_id = ?
ORDER BY
    id DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['members_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        if (isset($loan)) {
            echo $loan['id'] . " - " . APP;
        } else {
            echo "Peminjaman Saya - " . APP;
        }
        ?>
        <?= APP ?>
    </title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body>
    <main>
        <div class="top mb8">
            <?php
            if (isset($loan)) {
                echo "<p class='f14'><a class='f-sub' href='/lokapustaka/pages/member_loans.php'>/ Peminjaman Saya</a> / " . $loan['id'] . "</p>";
            } else {
                echo "<h2>Peminjaman Saya</h2>";
            }
            ?>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <hr class="mb8">
        <?php if (isset($loan)): ?>
            <div class="head mb16">
                <div class="title">
                    <p class="f14 f-sub">ID Peminjaman</p>
                    <h3><?= $loan['id'] ?></h3>
                </div>
                <div class="head-button">
                    <?php if ($loan['status'] === 'Belum Dikembalikan'): ?>
                        <a href="#" onclick="extendLoan('<?= $loan['id'] ?>')" class="sec-b head-b mr8">
                            <img src="/lokapustaka/img/plus-light.png" alt="Perpanjang">
                            Perpanjang
                        </a>
                    <?php endif ?>
                </div>
            </div>
            <div class="container book">
                <?php if ($loan['cover'] !== NULL): ?>
                    <img src="/lokapustaka/image_view.php?id=<?= $loan['book_id'] ?>" alt="Cover" class="cover mr12">
                <?php else: ?>
                    <img src="/lokapustaka/img/default-cover.jpg" alt="Cover" class="cover mr12">
                <?php endif ?>
                <div class="content">
                    <p class="f12 f-sub">ID Buku</p>
                    <h4 class="mb8" <?php if ($loan['book_id'] !== '[Buku Dihapus]'): ?>
                            onclick="window.location.href='books.php?id=<?= $loan['book_id'] ?>';" style="cursor: pointer;"
                        <?php endif ?>>
                        <?= $loan['book_id'] ?>
                    </h4>
                    <p class="f12 f-sub">Judul Buku</p>
                    <h4 class="mb8" <?php if ($loan['book_title'] !== '[Buku Dihapus]'): ?>
                            onclick="window.location.href='books.php?id=<?= $loan['book_id'] ?>';" style="cursor: pointer;"
                        <?php endif ?>>
                        <?= $loan['book_title'] ?>
                    </h4>
                    <p class="f12 f-sub">Tanggal Peminjaman</p>
                    <h4 class="mb8"><?= formatDate($loan['borrow_date']) ?></h4>
                    <p class="f12 f-sub">Tenggat Waktu</p>
                    <h4 class="mb8"><?= formatDate($loan['expected_return_date']) ?></h4>
                    <p class="f12 f-sub">Status</p>
                    <h4 class="mb8"><?= $loan['status'] ?></h4>
                    <p class="f12 f-sub">Tanggal Pengembalian</p>
                    <h4 class="mb8"><?= ($loan['return_date'] !== NULL) ? formatDate($loan['return_date']) : '-' ?></h4>
                    <p class="f12 f-sub">Denda</p>
                    <h4 class="mb8"><?= ($loan['fines'] !== NULL) ? 'Rp. ' . $loan['fines'] : '-' ?></h4>
                </div>
            </div><br>
        <?php else: ?>
            <?php if (isset($cur_loan)): ?>
                <div class="container3 loan" onclick="location.href='?id=<?= $cur_loan['id'] ?>'">
                    <?php if ($cur_loan['cover'] !== NULL): ?>
                        <img src="/lokapustaka/image_view.php?id=<?= $cur_loan['book_id']; ?>" alt="Cover" class="cover mr12">
                    <?php else: ?>
                        <img src="/lokapustaka/img/default-cover.jpg" alt="Cover" class="cover mr12">
                    <?php endif ?>
                    <div class="content">
                        <p class="f-sub f12">Id</p>
                        <h4 class="mb4"><?= $cur_loan['id'] ?></h4>
                        <p class="f-sub f12">Judul Buku</p>
                        <h4 class="mb4"><?= $cur_loan['title'] ?></h4>
                        <p class="f-sub f12">Tenggat Waktu</p>
                        <h4 class="mb4"><?= formatDate($cur_loan['expected_return_date']) ?></h4>
                        <p class="f-sub f12">Perpanjang</p>
                        <h4 class="mb4"><?= $cur_loan['extend'] ?></h4>
                        <p class="f-sub f12">Telat</p>
                        <h4><?= ($cur_loan['day_late'] !== NULL) ? $cur_loan['day_late'] . ' Hari' : '-' ?></h4>
                    </div>
                </div>
                <hr class="mb12">
            <?php endif ?>
            <p class="f-sub f14 mb4">Riwayat Peminjaman</p>
            <div class="horizontal-scroll-container">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($loan_list = $result->fetch_assoc()): ?>
                        <div class="container3 scroll" onclick="location.href='?id=<?= $loan_list['id'] ?>'">
                            <?php if ($loan_list['cover'] !== NULL): ?>
                                <img src="/lokapustaka/image_view.php?id=<?= $loan_list['book_id'] ?>" alt="Cover" class="cover mb8">
                            <?php else: ?>
                                <img src="/lokapustaka/img/default-cover.jpg" alt="Cover" class="cover mb8">
                            <?php endif ?>
                            <p class="f-sub f12"><?= $loan_list['id'] ?></p>
                            <p class="f12"><?= $loan_list['borrow_date'] ?></p>
                            <p class="font-bold f14"><?= $loan_list['title'] ?></p>
                            <p class="f-sub f12">
                                <?= ($loan_list['day_late'] !== NULL) ? 'Telat ' . $loan_list['day_late'] . ' Hari' : 'Tepat Waktu' ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-results">Tidak ada riwayat peminjaman.</p>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>