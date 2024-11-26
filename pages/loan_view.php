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
    <title>Peminjaman - <?= APP ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body>
    <main>
        <div class="top mb16">
            <p class="f14"><a href="loans.php"><span class="f-sub">/ Peminjaman </span></a>/ 100012</p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <div class="head mb16">
            <div class="title">
                <p class="f14 f-sub">Id Peminjaman</p>
                <h3>100012</h3>
            </div>
            <div class="head-button">
                <a href="" class="fou-b head-b mr8">Hapus</a>
                <a href="" class="thi-b head-b mr8">
                    <img src="/lokapustaka/img/plus-dark.png" alt="Perpanjang">
                    Perpanjang
                </a>
                <a href="" class="sec-b head-b">
                    <img src="/lokapustaka/img/done-light.png" alt="Dikembalikan">
                    Dikembalikan
                </a>
            </div>
        </div>
        <div class="container mb16">
            <img src="/lokapustaka/img/cover-example.png" alt="Sampul" class="mr12">
            <div class="content">
                <p class="f12 f-sub">Id Buku</p>
                <h4 class="mb8" onclick="window.location.href='book_view.php';" style="cursor: pointer;">B0001</h4>
                <p class="f12 f-sub">Judul Buku</p>
                <h4 class="mb8" onclick="window.location.href='book_vies.php';" style="cursor: pointer;">Koala Kumal
                </h4>
                <p class="f12 f-sub">Id Peminjam</p>
                <h4 class="mb8" onclick="window.location.href='member_view.php';" style="cursor: pointer;">A0001</h4>
                <p class="f12 f-sub">Nama Peminjam</p>
                <h4 class="mb8" onclick="window.location.href='member_view.php';" style="cursor: pointer;">JohnDoe</h4>
                <p class="f12 f-sub">Tanggal Peminjaman</p>
                <h4 class="mb8">31/10/2024</h4>
                <p class="f12 f-sub">Tenggat Waktu</p>
                <h4 class="mb8">06/11/2024</h4>
                <p class="f12 f-sub">Status</p>
                <h4 class="mb8">Belum Dikembalikan</h4>
                <p class="f12 f-sub">Tanggal Pengembalian</p>
                <h4 class="mb8">-</h4>
                <p class="f12 f-sub">Denda</p>
                <h4 class="mb8">-</h4>
            </div>
        </div>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>