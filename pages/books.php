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
    <title>Buku - <?= APP ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body>
    <main>
        <div class="top mb16">
            <p class="f14">/ Buku</p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <div class="head mb16">
            <h3>Daftar Buku</h3>
            <div class="head-button">
                <a href="" class="thi-b head-b mr8">
                    <img src="/lokapustaka/img/search-dark.png" alt="Cari">
                    Cari
                </a>
                <a href="" class="sec-b head-b">
                    <img src="/lokapustaka/img/plus-light.png" alt="Tambah">
                    Tambah
                </a>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="w75">Kode</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th class="w150">Kategori</th>
                    <th class="w75">Dipinjam</th>
                    <th class="w75">Tersedia</th>
                </tr>
            </thead>
            <tbody>
                <tr onclick="window.location.href='book_view.php';" style="cursor: pointer;">
                    <td class="t-center">B0001</td>
                    <td>Koala Kumal</td>
                    <td>Raditya Dika</td>
                    <td>Fiksi</td>
                    <td class="t-center">2</td>
                    <td class="t-center">4</td>
                </tr>
            </tbody>
        </table>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>