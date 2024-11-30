<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/sweet_alert.php";

if (empty($_SESSION['staffs_id']) || empty($_SESSION['staffs_name']) || empty($_SESSION['staffs_roles'])) {
    echo '<script>alert("Sesi Habis. Silahkan login kembali!"); location.href = "/lokapustaka/pages/login.php"</script>';
}
?>

<aside class="mr16">
    <div class="logo mb16">
        <img src="/lokapustaka/img/icon.png">
    </div>
    <p class="font-medium">[<?= $_SESSION['staffs_roles'] ?>]</p>
    <p class="font-bold"><?= $_SESSION['staffs_name'] ?></p>
    <p class="font-medium mb16"><?= $_SESSION['staffs_id'] ?></p>
    <a href="/lokapustaka/pages/dashboard.php" class="opt mb12">
        <img src="/lokapustaka/img/dashboard-light.png" alt="" data-light-src="/lokapustaka/img/dashboard-light.png"
            data-dark-src="/lokapustaka/img/dashboard-dark.png">
        <p class="font-medium">Dasbor</p>
    </a>
    <a href="/lokapustaka/pages/books.php" class="opt">
        <img src="/lokapustaka/img/books-dark.png" alt="" data-light-src="/lokapustaka/img/books-light.png"
            data-dark-src="/lokapustaka/img/books-dark.png">
        <p class="font-medium">Buku</p>
    </a>
    <a href="/lokapustaka/pages/members.php" class="opt">
        <img src="/lokapustaka/img/members-dark.png" alt="" data-light-src="/lokapustaka/img/members-light.png"
            data-dark-src="/lokapustaka/img/members-dark.png">
        <p class="font-medium">Anggota</p>
    </a>
    <a href="/lokapustaka/pages/loans.php" class="opt mb12">
        <img src="/lokapustaka/img/loans-dark.png" alt="" data-light-src="/lokapustaka/img/loans-light.png"
            data-dark-src="/lokapustaka/img/loans-dark.png">
        <p class="font-medium">Peminjaman</p>
    </a>
    <?php if ($_SESSION['staffs_roles'] == 'Admin' || $_SESSION['staffs_roles'] == 'Superadmin'): ?>
        <a href="/lokapustaka/pages/staff.php" class="opt mb12">
            <img src="/lokapustaka/img/staff-dark.png" alt="" data-light-src="/lokapustaka/img/staff-light.png"
                data-dark-src="/lokapustaka/img/staff-dark.png">
            <p class="font-medium">Staff</p>
        </a>
    <?php endif ?>
    <a href="#" class="exit" onclick="changePassword()">
        <img src="/lokapustaka/img/password-dark.png" alt="" data-light-src="/lokapustaka/img/password-light.png"
            data-dark-src="/lokapustaka/img/password-dark.png">
        <p class="font-medium">Ganti Password</p>
    </a>
    <a href="#" class="exit" onclick="logout()">
        <img src="/lokapustaka/img/exit.png" alt="">
        <p class="font-medium f-red">Ganti Sesi</p>
    </a>
</aside>