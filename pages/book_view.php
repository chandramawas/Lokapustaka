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
            <p class="f14"><a href="books.php"><span class="f-sub">/ Buku </span></a>/ B0001</p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <div class="head mb16">
            <div class="title">
                <p class="f14 f-sub">Kode Buku</p>
                <h3>B0001</h3>
            </div>
            <div class="head-button">
                <a href="" class="thi-b head-b mr8">
                    <img src="/lokapustaka/img/edit-dark.png" alt="Edit">
                    Edit
                </a>
                <a href="" class="red-b head-b">
                    <img src="/lokapustaka/img/close-light.png" alt="Hapus">
                    Hapus
                </a>
            </div>
        </div>
        <div class="container mb16">
            <img src="/lokapustaka/img/cover-example.png" alt="Sampul" class="mr12">
            <div class="content">
                <p class="f12 f-sub">Judul Buku</p>
                <h4 class="mb8">Koala Kumal</h4>
                <p class="f12 f-sub">Pengarang / Penulis</p>
                <h4 class="mb8">Raditya Dika</h4>
                <p class="f12 f-sub">Kategori</p>
                <h4 class="mb8">Fiksi</h4>
                <p class="f12 f-sub">Stok Tersedia</p>
                <h4 class="mb8">4</h4>
                <p class="f12 f-sub">Dipinjam</p>
                <h4 class="mb8">2</h4>
                <p class="f12 f-sub">Penerbit</p>
                <h4 class="mb8">GagasMedia</h4>
                <p class="f12 f-sub">Tahun Terbit</p>
                <h4 class="mb8">2015</h4>
                <p class="f12 f-sub">ISBN</p>
                <h4 class="mb8">978-979-780-899-0</h4>
            </div>
        </div>
        <p class="f-sub mb8">Riwayat Peminjaman</p>
        <table>
            <thead>
                <tr>
                    <th class="w75">id</th>
                    <th class="w75">Kode Anggota</th>
                    <th>Nama</th>
                    <th class="w75">Tgl Pinjam</th>
                    <th class="w75">Tenggat Waktu</th>
                    <th class="w150">Status</th>
                    <th class="w75">Tgl Pengembalian</th>
                    <th class="w75">Denda</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="t-center" onclick="window.location.href='loan_view.php';" style="cursor: pointer;">100012
                    </td>
                    <td class="t-center" onclick="window.location.href='member_view.php';" style="cursor: pointer;">
                        A0001</td>
                    <td onclick="window.location.href='member_view.php';" style="cursor: pointer;">JohnDoe</td>
                    <td class="t-center">31/10/2024</td>
                    <td class="t-center">06/11/2024</td>
                    <td class="t-center">Belum Dikembalikan</td>
                    <td class="t-center">-</td>
                    <td class="t-center">-</td>
                </tr>
            </tbody>
        </table>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>