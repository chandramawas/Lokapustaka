<?php

//Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'lokapustaka');

//Web Info
define('APP', 'Lokapustaka');

//Acounts
define('DEFAULT_PASS', 'Lokapustaka2024'); //Password default untuk staff baru

//Rules
define('EXPIRED_DATE', '3 MONTH'); //Keanggotaan kadaluarsa
define('EXPIRED_DATE_TEXT', '3 bulan'); //Keanggotaan kadaluarsa
define('EXPECTED_RETURN_DATE', '7 DAY'); //Batas waktu peminjaman
define('EXPECTED_RETURN_DATE_TEXT', '7 hari'); //Batas waktu peminjaman
define('FINES', '50000'); //Denda keterlambatan per hari


// Set timezone to WIB
date_default_timezone_set('Asia/Jakarta');
$current_time = date('d/m/Y H:i'); // Format: DD/MM/YYYY HH:MM

function formatPhoneNumber($phone)
{
    $length = strlen($phone);
    if ($length >= 12) {
        // Format phone number as 0812-3456-7890
        return preg_replace('/(\d{4})(\d{4})(\d{4})/', '$1-$2-$3', $phone);
    } else {
        // Format phone number as 081-2345-678
        return preg_replace('/(\d{3})(\d{4})(\d{1})/', '$1-$2-$3', $phone);
    }
}

function formatDate($datetime)
{
    // Convert 2024/11/23 20:42:00 to 23/11/2024
    return date('d/m/Y', strtotime($datetime));
}

function formatISBN($isbn)
{
    return preg_replace('/(\d{3})(\d{7})/', '$1-$2', $isbn);
}