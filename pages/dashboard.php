<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside.php";

$sql = "
SELECT COUNT(id) AS total
FROM loans
WHERE
";

$last_12_months = $sql . "borrow_date > DATE_SUB(NOW(), INTERVAL 12 MONTH)";
$last_30_days = $sql . "borrow_date > DATE_SUB(NOW(), INTERVAL 30 DAY)";
$last_7_days = $sql . "borrow_date > DATE_SUB(NOW(), INTERVAL 7 DAY)";
$not_returned = $sql . "return_date IS NULL";
$not_returned_today_before = $sql . "return_date IS NULL AND DATE(expected_return_date) <= CURDATE()";
$returned_today = $sql . "DATE(return_date) = CURDATE()";

// Execute for last 12 months
$stmt = $conn->prepare($last_12_months);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$last_12_months = $row['total']; // Store result into variable

// Execute for last 30 days
$stmt = $conn->prepare($last_30_days);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$last_30_days = $row['total']; // Store result into variable

// Execute for last 7 days
$stmt = $conn->prepare($last_7_days);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$last_7_days = $row['total']; // Store result into variable

// Execute for not returned
$stmt = $conn->prepare($not_returned);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$not_returned = $row['total']; // Store result into variable

// Execute for not returned today before
$stmt = $conn->prepare($not_returned_today_before);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$not_returned_today_before = $row['total']; // Store result into variable

// Execute for returned today
$stmt = $conn->prepare($returned_today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$returned_today = $row['total']; // Store result into variable
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor - <?= APP ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body>
    <main>
        <div class="top mb16">
            <p class="f14">/ Dasbor</p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <h3 class="mb16">Dasbor</h3>
        <div class="b-list mb16">
            <a href="/lokapustaka/pages/loans.php?action=add" class="pri-b mr16">
                <img src="/lokapustaka/img/loans-add-light.png" alt="Tambah Peminjaman">
                <p class="f-white font-bold">PEMINJAMAN BARU</p>
            </a>
            <a href="#" onclick="searchLoan()" class="sec-b mr16">
                <img src="/lokapustaka/img/return-book-light.png" alt="Cari Peminjaman">
                <p class="f-white font-bold">PENGEMBALIAN / PERPANJANG</p>
            </a>
            <a href="#" onclick="searchBook()" class="thi-b mr16">
                <img src="/lokapustaka/img/books-search-dark.png" alt="Cari Buku">
                <p class="font-bold">BUKU</p>
            </a>
            <a href="#" onclick="searchMember()" class="thi-b">
                <img src="/lokapustaka/img/members-search-dark.png" alt="Cari Anggota">
                <p class="font-bold">ANGGOTA</p>
            </a>
        </div>
        <div class="b-list mb16">
            <div class="container dashboard button mr16"
                onclick="window.location.href = '/lokapustaka/pages/loans.php?notReturned=on'">
                <p class="f14 f-sub">Belum dikembalikan dengan tenggat waktu hari ini (dan sebelumnya)</p>
                <h4><?= $not_returned_today_before ?> Buku</h4>
            </div>
            <div class="container dashboard button mr16"
                onclick="window.location.href = '/lokapustaka/pages/loans.php'">
                <p class="f14 f-sub">Buku yang dikembalikan hari ini</p>
                <h4><?= $returned_today ?> Buku</h4>
            </div>
        </div>
        <hr class="mb16">
        <p class="f16 font-bold mb8 t-center">Total Peminjaman</p>
        <div class="b-list">
            <div class="container dashboard mr16">
                <p class="f14 f-sub">12 Bulan Terakhir</p>
                <h4><?= $last_12_months ?> Peminjaman</h4>
            </div>
            <div class="container dashboard mr16">
                <p class="f14 f-sub">30 Hari Terakhir</p>
                <h4><?= $last_30_days ?> Peminjaman</h4>
            </div>
            <div class="container dashboard mr16">
                <p class="f14 f-sub">7 Hari Terakhir</p>
                <h4><?= $last_7_days ?> Peminjaman</h4>
            </div>
            <div class="container dashboard">
                <p class="f14 f-sub">Belum Dikembalikan</p>
                <h4><?= $not_returned ?> Peminjaman</h4>
            </div>
        </div>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>