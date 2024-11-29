<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - <?= APP ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body class="p0">
    <div class="center">
        <div class="login-container">
            <div class="logo login mb8">
                <img src="/lokapustaka/img/icon.png" class="mr8">
            </div>
            <p class="font-bold t-center mb16">Staff Login</p>
            <form action="/lokapustaka/request_handler.php?action=login" method="post">
                <div class="input-container mb12">
                    <img src="/lokapustaka/img/staff-dark.png" alt="Staff Icon" class="icon">
                    <input type="text" name="id" id="id" placeholder="ID Staff" required>
                </div>
                <div class="input-container mb12">
                    <img src="/lokapustaka/img/password-dark.png" alt="Password Icon" class="icon">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <input type="submit" value="Masuk" class="sec-b">
            </form>
        </div>
    </div>
</body>

</html>