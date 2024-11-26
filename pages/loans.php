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
            <p class="f14">/ Peminjaman</p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <div class="head mb16">
            <div class="title">
                <h3 class="mb4">Daftar Peminjaman</h3>
                <span class="subtitle">
                    <input type="checkbox" name="" id="" class="f12 mr4">
                    <p class="f-sub f12">Belum Dikembalikan</p>
                </span>
            </div>
            <div class="head-button">
                <a href="" class="thi-b head-b mr8">
                    <img src="/lokapustaka/img/search-dark.png" alt="Cari">
                    Cari
                </a>
                <a href="" class="sec-b head-b">
                    <img src="/lokapustaka/img/plus-light.png" alt="Tambah">
                    Peminjaman Baru
                </a>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="w75">Id</th>
                    <th class="w75">Id Anggota</th>
                    <th>Nama Anggota</th>
                    <th class="w75">Id Buku</th>
                    <th>Judul Buku</th>
                    <th class="w75">Tgl Pinjam</th>
                    <th class="w75">Tenggat Waktu</th>
                    <th class="w150">Status</th>
                    <th class="w75">Tgl Pengembalian</th>
                    <th class="w75">Denda</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="t-center" onclick="window.location.href='loan_view.php';" style="cursor: pointer;">100001
                    </td>
                    <td class="t-center">A0001</td>
                    <td>JohnDoe</td>
                    <td class="t-center">B0001</td>
                    <td>Koala Kumal</td>
                    <td class="t-center">06/11/2024</td>
                    <td class="t-center">08/11/2024</td>
                    <td class="t-center">Belum Mengembalikan</td>
                    <td class="t-center">-</td>
                    <td class="t-center">-</td>
                </tr>
            </tbody>
        </table>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>