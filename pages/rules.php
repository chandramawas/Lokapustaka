<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside_index.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peraturan - <?= APP ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body>
    <main>
        <div class="top mb16">
            <p class="f14">/ Peraturan</p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <h3 class="mb16">Peraturan Perpustakaan</h3>
        <div class="container">
            <div class="content">
                <h4 class="mb4">Keanggotaan</h4>
                <div class="content ml16 mr16 mb16">
                    <p class="f14">&#x2022; Keanggotaan terbuka untuk semua individu yang memenuhi syarat pendaftaran.
                    </p>
                    <p class="f14">&#x2022; Anggota harus memberikan indentifikasi dan informasi kontak yang valid saat
                        mendaftar.</p>
                    <p class="f14">&#x2022; Keanggotaan berlaku selama 3 bulan dari tanggal pendaftaran.</p>
                    <p class="f14">&#x2022; Anggota akan diberitahu tentang masa berlaku keanggotaan mereka dan dapat
                        memperpanjang
                        keanggotaan sebelum tanggal kadaluarsa</p>
                </div>
                <h4 class="mb4">Peminjaman Buku</h4>
                <div class="content ml16 mr16 mb16">
                    <p class="f14">&#x2022; Anggota hanya dapat meminjam 1 buku pada satu waktu dan harus dilakukan
                        sebelum tanggal jatuh tempo asli.
                    </p>
                    <p class="f14">&#x2022; Periode peminjaman untuk setiap buku adalah
                        <?= EXPECTED_RETURN_DATE_TEXT ?>.
                    </p>
                    <p class="f14">&#x2022; Anggota dapat memperpanjang buku sebanyak satu kali
                        (<?= EXPECTED_RETURN_DATE_TEXT ?>).</p>
                    <p class="f14">&#x2022; Anggota harus mengembalikan buku yang dipinjam sebelum tanggal jatuh tempo
                        untuk menghindari denda.</p>
                </div>
                <h4 class="mb4">Denda</h4>
                <div class="content ml16 mr16 mb16">
                    <p class="f14">&#x2022; Denda sebesar Rp. <?= FINES ?> akan dikenakan untuk setiap hari buku
                        terlambat dikembalikan.
                    </p>
                    <p class="f14">&#x2022; Anggota bertanggung jawab untuk membayar denda sebelum melakukan
                        peminjaman lain.</p>
                    <p class="f14">&#x2022; Anggota mungkin diminta untuk membayar biaya perbaikan atau perggantian buku
                        yang rusak atau hilang.</p>
                </div>
                <h4 class="mb4">Privasi dan Kerahasiaan</h4>
                <div class="content ml16 mr16">
                    <p class="f14">&#x2022; Perpustakaan menghormati privasi anggotanya. Informasi pribadi tidak akan
                        dibagikan tanpa izin.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>