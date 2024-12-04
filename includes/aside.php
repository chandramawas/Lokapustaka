<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/sweet_alert.php";

if (empty($_SESSION['staffs_id']) || empty($_SESSION['staffs_name']) || empty($_SESSION['staffs_roles'])) {
    echo '<script>alert("Sesi Habis. Silahkan login kembali!"); location.href = "/lokapustaka/login.php"</script>';
}
?>

<aside>
    <div class="logo mb16">
        <img src="/lokapustaka/img/icon.png" onclick="location.href='/lokapustaka/staff/dashboard.php'"
            style="cursor: pointer;">
    </div>
    <div class="aside top">
        <hr class="mb4">
        <p class="font-medium">[<?= $_SESSION['staffs_roles'] ?>]</p>
        <p class="font-bold"><?= $_SESSION['staffs_name'] ?></p>
        <p class="font-medium mb4"><?= $_SESSION['staffs_id'] ?></p>
        <hr class="mb4">
        <a href="/lokapustaka/staff/dashboard.php" class="opt mb4">
            <img src="/lokapustaka/img/dashboard-light.png" alt="" data-light-src="/lokapustaka/img/dashboard-light.png"
                data-dark-src="/lokapustaka/img/dashboard-dark.png">
            <p class="font-medium">Dasbor</p>
        </a>
        <hr class="mb4">
        <a href="/lokapustaka/staff/books.php" class="opt mb4">
            <img src="/lokapustaka/img/books-dark.png" alt="" data-light-src="/lokapustaka/img/books-light.png"
                data-dark-src="/lokapustaka/img/books-dark.png">
            <p class="font-medium">Buku</p>
        </a>
        <a href="/lokapustaka/staff/members.php" class="opt mb4">
            <img src="/lokapustaka/img/members-dark.png" alt="" data-light-src="/lokapustaka/img/members-light.png"
                data-dark-src="/lokapustaka/img/members-dark.png">
            <p class="font-medium">Anggota</p>
        </a>
        <a href="/lokapustaka/staff/loans.php" class="opt mb4">
            <img src="/lokapustaka/img/loans-dark.png" alt="" data-light-src="/lokapustaka/img/loans-light.png"
                data-dark-src="/lokapustaka/img/loans-dark.png">
            <p class="font-medium">Peminjaman</p>
        </a>
        <hr class="mb4">
        <?php if ($_SESSION['staffs_roles'] == 'Admin' || $_SESSION['staffs_roles'] == 'Superadmin'): ?>
            <a href="/lokapustaka/staff/staff.php" class="opt mb4">
                <img src="/lokapustaka/img/staff-dark.png" alt="" data-light-src="/lokapustaka/img/staff-light.png"
                    data-dark-src="/lokapustaka/img/staff-dark.png">
                <p class="font-medium">Staff</p>
            </a>
            <hr>
        <?php endif ?>
    </div>
    <div class="aside bot">
        <hr class="mb4">
        <a href="#" class="exit  mb4" onclick="changePassword()">
            <img src="/lokapustaka/img/password-dark.png" alt="" data-light-src="/lokapustaka/img/password-light.png"
                data-dark-src="/lokapustaka/img/password-dark.png">
            <p class="font-medium">Ganti Password</p>
        </a>
        <a href="#" class="exit" onclick="logout()">
            <img src="/lokapustaka/img/exit.png" alt="">
            <p class="font-medium f-red">Logout</p>
        </a>
    </div>
</aside>