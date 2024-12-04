# Lokapustaka

**Lokapustaka** adalah sistem manajemen perpustakaan berbasis web yang dibuat untuk mengelola data buku, anggota, peminjaman, dan pengembalian secara digital. Proyek ini dibangun menggunakan **PHP**, **MySQL**, **HTML**, **CSS**, dan **JavaScript**. Sistem ini juga mendukung pengelolaan denda, serta memiliki fitur manajemen keanggotaan yang dapat diperpanjang.

## Fitur Utama
1. **Manajemen Buku**: Tambah, ubah, dan hapus data buku.
2. **Manajemen Anggota**: Kelola data anggota perpustakaan.
3. **Peminjaman dan Pengembalian**: Catat peminjaman dan pengembalian buku dengan perhitungan otomatis untuk keterlambatan.
4. **Denda Otomatis**: Hitung denda berdasarkan keterlambatan pengembalian buku.
5. **Manajemen Keanggotaan**: Pantau status keanggotaan (aktif/kadaluarsa) dengan masa berlaku yang dapat diperpanjang.
6. **Pencarian Buku**: Cari buku berdasarkan judul, pengarang, atau kategori.
7. **SweetAlert Integrasi**: Notifikasi interaktif untuk aksi seperti logout, penggantian sesi, dan konfirmasi lainnya.

## Teknologi yang Digunakan
- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS (dibuat dari awal), JavaScript
- **Library Tambahan**: SweetAlert untuk notifikasi interaktif

## Instalasi
1. Clone repositori ini ke direktori lokal:
   ```bash
   git clone https://github.com/chandramawas/Lokapustaka.git
   ```
2. Pastikan **XAMPP** atau server lokal lainnya sudah berjalan.
3. Letakkan proyek ini di dalam folder `htdocs` (jika menggunakan XAMPP).
4. Import file database ke MySQL melalui phpMyAdmin:
   - Nama database: `lokapustaka`
   - Import file SQL yang disediakan di dalam direktori `database/`.

## Konfigurasi
1. Buka file `config/db.php` dan sesuaikan konfigurasi database sesuai dengan server lokal Anda:
   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'lokapustaka');
   ?>
   ```

## Akun Default
Gunakan akun berikut untuk login pertama kali:
- **ID**: `root`
- **Password**: `root`

## Penggunaan
1. Buka browser dan akses `http://localhost/Lokapustaka/`.
2. Login menggunakan akun default atau buat akun baru jika diperlukan.
3. Gunakan antarmuka untuk mengelola buku, anggota, peminjaman, dan lainnya.

## Kontribusi
Jika Anda ingin berkontribusi, silakan buat pull request atau buka issue untuk saran perbaikan.

## Lisensi
Proyek ini berada di bawah lisensi MIT. Silakan lihat file `LICENSE` untuk informasi lebih lanjut.

---

Terima kasih telah menggunakan **Lokapustaka**! Jika Anda memiliki pertanyaan atau masalah, jangan ragu untuk menghubungi kami melalui [Issues](https://github.com/chandramawas/Lokapustaka/issues).
