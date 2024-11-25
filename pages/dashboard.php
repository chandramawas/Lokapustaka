<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside.php";
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
        <div class="b-list">
            <a href="" class="pri-b mr16">
                <img src="/lokapustaka/img/loans-add-light.png" alt="Tambah Peminjaman">
                <p class="f-white font-bold">PEMINJAMAN BARU</p>
            </a>
            <a href="" class="sec-b mr16">
                <img src="/lokapustaka/img/return-book-light.png" alt="Buku Kembali">
                <p class="f-white font-bold">PENGEMBALIAN / PERPANJANG</p>
            </a>
            <a href="" class="thi-b mr16">
                <img src="/lokapustaka/img/books-search-dark.png" alt="Cari Buku">
                <p class="font-bold">BUKU</p>
            </a>
            <a href="" class="thi-b">
                <img src="/lokapustaka/img/members-search-dark.png" alt="Cari Anggota">
                <p class="font-bold">ANGGOTA</p>
            </a>
        </div>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>