<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "
    SELECT
        loans.id,
        books.cover,
        CASE
            WHEN loans.book_id IS NULL THEN '[Buku Dihapus]'
            ELSE loans.book_id
        END book_id,
        CASE
            WHEN books.title IS NULL THEN '[Buku Dihapus]'
            ELSE books.title
        END book_title,
        CASE
            WHEN loans.member_id IS NULL THEN '[Anggota Dihapus]'
            ELSE loans.member_id
        END member_id,
        CASE
            WHEN members.name IS NULL THEN '[Anggota Dihapus]'
            ELSE members.name
        END member_name,
        loans.borrow_date,
        loans.expected_return_date,
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
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $loan = $result->fetch_assoc();
    } else {
        ?>
        <script>alert("Peminjaman tidak ditemukan."); history.back()</script>
        <?php
    }
} else {
    $notReturned = isset($_GET['notReturned']) ? 1 : 0;

    $sql = "
    SELECT
        loans.id,
        CASE
            WHEN loans.book_id IS NULL THEN '[Dihapus]'
            ELSE loans.book_id
        END book_id,
        CASE
            WHEN loans.member_id IS NULL THEN '[Dihapus]'
            ELSE loans.member_id
        END member_id,
        CASE
            WHEN members.name IS NULL THEN '[Dihapus]'
            ELSE members.name
        END member_name,
        loans.borrow_date,
        loans.expected_return_date,
        CASE
            WHEN return_date IS NULL THEN 'Belum Dikembalikan'
            ELSE 'Dikembalikan'
        END AS status,
        loans.return_date,
        loans.fines
    FROM loans
        LEFT JOIN members ON loans.member_id = members.id
        LEFT JOIN books ON loans.book_id = books.id
    ";

    if ($notReturned) {
        $sql .= " WHERE loans.return_date IS NULL";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $loans = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    if (isset($loan)) {
        echo "Peminjaman " . $loan['id'] . " - " . APP;
    } else {
        if (isset($_GET['action']) == 'add') {
            echo "Tambah Peminjaman - " . APP;
        } else {
            echo "Peminjaman - " . APP;
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
            <p class="f14"><?php
            if (isset($loan)) {
                echo "<a class='f-sub' href='loans.php'>/ Peminjaman</a> / " . $loan['id'];
            } else {
                if (isset($_GET['action']) == 'add') {
                    echo "<a class='f-sub' href='loans.php'>/ Peminjaman</a> / Tambah";
                } else {
                    echo "/ Peminjaman";
                }
            }
            ?></p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <?php if (isset($loan)): ?>
            <div class="head mb16">
                <div class="title">
                    <p class="f14 f-sub">ID Peminjaman</p>
                    <h3><?= $loan['id'] ?></h3>
                </div>
                <div class="head-button">
                    <?php if ($_SESSION['staffs_roles'] === 'Superadmin' || $_SESSION['staffs_roles'] === 'Admin'): ?>
                        <a href="#" onclick="deleteLoan('<?= $loan['id'] ?>')" <?= ($loan['status'] === 'Belum Dikembalikan') ? "class='fou-b head-b mr8'" : "class='red-b head-b'" ?>>
                            <?= ($loan['status'] === 'Belum Dikembalikan') ? "" : "<img src='/lokapustaka/img/close-light.png' alt='Hapus'>" ?>
                            Hapus
                        </a>
                    <?php endif ?>
                    <?php if ($loan['status'] === 'Belum Dikembalikan'): ?>
                        <a href="#" onclick="extendLoan('<?= $loan['id'] ?>')" class="thi-b head-b mr8">
                            <img src="/lokapustaka/img/plus-dark.png" alt="Perpanjang">
                            Perpanjang
                        </a>
                        <a href="#" onclick="returnLoan('<?= $loan['id'] ?>')" class="sec-b head-b">
                            <img src="/lokapustaka/img/done-light.png" alt="Dikembalikan">
                            Dikembalikan
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
                    <p class="f12 f-sub">ID Peminjam</p>
                    <h4 class="mb8" <?php if ($loan['member_id'] !== '[Anggota Dihapus]'): ?>
                            onclick="window.location.href='members.php?id=<?= $loan['member_id'] ?>';" style="cursor: pointer;"
                        <?php endif ?>>
                        <?= $loan['member_id'] ?>
                    </h4>
                    <p class="f12 f-sub">Nama Peminjam</p>
                    <h4 class="mb8" <?php if ($loan['member_name'] !== '[Anggota Dihapus]'): ?>
                            onclick="window.location.href='members.php?id=<?= $loan['member_id'] ?>';" style="cursor: pointer;"
                        <?php endif ?>>
                        <?= $loan['member_name'] ?>
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
            <?php if (isset($_GET['action']) == 'add'): ?>
                <div class="head mb16">
                    <h3>Tambah Peminjaman</h3>
                    <div class="head-button">
                        <a class="sec-b head-b" onclick="confirmAddLoan()">
                            <img src="/lokapustaka/img/save-light.png" alt="Simpan">
                            Simpan
                        </a>
                    </div>
                </div>
                <div class="container2">
                    <form id="addLoanForm" method="post">
                        <div class="input-container2 mb12">
                            <label for="book_id_isbn" class="f12 f-sub mb4">ID Buku atau ISBN*</label>
                            <input type="text" name="book_id_isbn" id="book_id_isbn" placeholder="e.g. B0001 atau 9786027742113"
                                required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="member_id" class="f12 f-sub mb4">ID Peminjam*</label>
                            <input type="text" name="member_id" id="member_id" placeholder="e.g. A0001" required>
                        </div>
                        <div class="input-container2">
                            <label for="phone_num" class="f12 f-sub mb4">No Telepon (sesuai dengan data anggota)*</label>
                            <input type="text" name="phone_num" id="phone_num" placeholder="e.g. 081234567890" required>
                        </div>
                    </form>
                </div><br>
            <?php else: ?>
                <div class="head mb16">
                    <div class="title">
                        <h3 class="mb4">Daftar Peminjaman</h3>
                        <span class="subtitle">
                            <form id="notReturnedForm" action="" method="get">
                                <input type="checkbox" name="notReturned" id="notReturned" class="f12 mr4" <?php echo $notReturned ? 'checked' : ''; ?>
                                    onchange="document.getElementById('notReturnedForm').submit()">
                            </form>
                            <label for="notReturned" class="f-sub f12">Belum Dikembalikan</label>
                        </span>
                    </div>
                    <div class="head-button">
                        <form action="" method="get">
                            <input type="text" class="search-b head-b mr8" name="id" id="id" placeholder="Cari ID Peminjaman">
                        </form>
                        <a href="?action=add" class="sec-b head-b">
                            <img src="/lokapustaka/img/plus-light.png" alt="Tambah">
                            Peminjaman Baru
                        </a>
                    </div>
                </div>
                <table class="sortable">
                    <thead>
                        <tr>
                            <th class="w75" data-column="id">
                                ID
                                <img src="/lokapustaka/img/sort-asc.png" alt="Sort Icon" class="sort-icon">
                            </th>
                            <th class="w75" data-column="book_id">ID Buku</th>
                            <th class="w75" data-column="member_id">ID Peminjam</th>
                            <th data-column="member_name">Nama Peminjam</th>
                            <th class="w75" data-column="borrow_date">Tgl Pinjam</th>
                            <th class="w75" data-column="expected_return_date">Tenggat Waktu</th>
                            <th class="w125" data-column="status">Status</th>
                            <th class="w75" data-column="return_date">Tgl Pengembalian</th>
                            <th class="w100" data-column="fines">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <tr onclick="window.location.href='loans.php?id=<?= $loan['id'] ?>';" style="cursor: pointer;">
                                <td class="t-center">
                                    <?= $loan['id'] ?>
                                </td>
                                <td class="t-center"><?= $loan['book_id'] ?></td>
                                <td class="t-center"><?= $loan['member_id'] ?></td>
                                <td><?= $loan['member_name'] ?></td>
                                <td class="t-center"><?= formatDate($loan['borrow_date']) ?></td>
                                <td class="t-center"><?= formatDate($loan['expected_return_date']) ?></td>
                                <td class="t-center"><?= $loan['status'] ?></td>
                                <td class="t-center"><?= ($loan['return_date'] !== NULL) ? formatDate($loan['return_date']) : '-' ?>
                                </td>
                                <td><?= ($loan['fines'] !== NULL) ? 'Rp. ' . $loan['fines'] : '-' ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table><br>
            <?php endif ?>
        <?php endif ?>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>