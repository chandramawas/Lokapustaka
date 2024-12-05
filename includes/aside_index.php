<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/sweet_alert.php";
?>

<aside class="mr20">
    <div class="logo mb16">
        <img src="/lokapustaka/img/icon.png" onclick="location.href='/lokapustaka/'" style="cursor: pointer;">
    </div>
    <div class="aside top">
        <hr class="mb4">
        <?php if (!empty($_SESSION['members_id']) || !empty($_SESSION['members_name']) || !empty($_SESSION['members_expired_date']) || !empty($_SESSION['members_phone_num'])): ?>
            <p class="font-medium">[<?= $_SESSION['members_id'] ?>]</p>
            <p class="font-bold"><?= $_SESSION['members_name'] ?></p>
            <p class="mb4"><?= $_SESSION['members_phone_num'] ?></p>
            <hr class="mb4">
            <a href="/lokapustaka/pages/member_loans.php" class="opt">
                <img src="/lokapustaka/img/loans-dark.png" alt="" data-light-src="/lokapustaka/img/loans-light.png"
                    data-dark-src="/lokapustaka/img/loans-dark.png">
                <p class="font-medium">Peminjaman Saya</p>
            </a>
        <?php endif ?>
        <a href="/lokapustaka/" class="opt mb4">
            <img src="/lokapustaka/img/books-dark.png" alt="" data-light-src="/lokapustaka/img/books-light.png"
                data-dark-src="/lokapustaka/img/books-dark.png">
            <p class="font-medium">Buku</p>
        </a>
        <hr>
    </div>
    <div class="aside bot">
        <?php if (!empty($_SESSION['members_id']) || !empty($_SESSION['members_name']) || !empty($_SESSION['members_expired_date']) || !empty($_SESSION['members_phone_num'])): ?>
            <p class="t-center f12 mb4">Masa Aktif sd. <?= formatDate($_SESSION['members_expired_date']) ?></p>
            <hr class="mb4">
        <?php endif ?>
        <a href="/lokapustaka/pages/rules.php" class="opt mb4">
            <img src="/lokapustaka/img/rules-dark.png" alt="" data-light-src="/lokapustaka/img/rules-light.png"
                data-dark-src="/lokapustaka/img/rules-dark.png">
            <p class="font-medium">Peraturan</p>
        </a>
        <?php if (!empty($_SESSION['members_id']) || !empty($_SESSION['members_name']) || !empty($_SESSION['members_expired_date']) || !empty($_SESSION['members_phone_num'])): ?>
            <hr class="mb4">
            <a href="#" class="exit" onclick="changePassword()">
                <img src="/lokapustaka/img/password-dark.png" alt="" data-light-src="/lokapustaka/img/password-light.png"
                    data-dark-src="/lokapustaka/img/password-dark.png">
                <p class="font-medium">Ganti Password</p>
            </a>
            <a href="#" class="exit" onclick="logout()">
                <img src="/lokapustaka/img/exit.png" alt="">
                <p class="font-medium f-red">Logout</p>
            </a>
        <?php else: ?>
            <hr class="mb4">
            <a href="/lokapustaka/login.php" class="exit mb4">
                <img src="/lokapustaka/img/login.png" alt="">
                <p class="font-medium">Login</p>
            </a>
        <?php endif ?>
    </div>
</aside>