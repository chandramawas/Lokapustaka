<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "
    SELECT
        members.*,
        COUNT(loans.id) AS total_loans,
        COUNT(
            CASE
                WHEN loans.return_date > loans.expected_return_date THEN 1
                ELSE NULL
            END
        ) AS late_loans,
        CASE
            WHEN COUNT(loans.id) > 0
            AND COUNT(
                CASE
                    WHEN loans.return_date IS NULL THEN 1
                    ELSE NULL
                END
            ) > 0 THEN MAX(
                CASE
                    WHEN loans.return_date IS NULL THEN loans.expected_return_date
                    ELSE NULL
                END 
            )
            ELSE '-'
        END AS expected_return_date,
        CASE
            WHEN COUNT(loans.id) > 0
            AND COUNT(
                CASE
                    WHEN loans.return_date IS NULL THEN 1
                    ELSE NULL
                END
            ) > 0 THEN 'Meminjam'
            ELSE 'Tidak Meminjam'
        END AS status,
        CASE
            WHEN members.created_by IS NULL THEN '[Staff Dihapus]'
            ELSE users.name
        END AS created_by_name
    FROM members
        LEFT JOIN loans ON members.id = loans.member_id
        LEFT JOIN users ON members.created_by = users.id
    WHERE
        members.id = ?
    GROUP BY
        members.id;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
        $member_loans = [];

        $sql = "
        SELECT
            loans.id,
            loans.book_id,
            books.title AS book_title,
            loans.borrow_date,
            loans.expected_return_date,
            CASE
                WHEN return_date IS NULL THEN 'Belum Dikembalikan'
                ELSE 'Dikembalikan'
            END AS status,
            CASE
                WHEN return_date IS NULL THEN '-'
                ELSE return_date
            END AS return_date,
            CASE
                WHEN fines IS NULL THEN '-'
                ELSE fines
            END AS fines
        FROM loans
            LEFT JOIN books ON book_id = books.id
        WHERE
            member_id = ?
        ORDER BY status ASC, loans.id ASC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $member['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $member_loans = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        ?>
        <script>alert("Anggota tidak ditemukan."); location.href = "members.php"</script>
        <?php
    }
} else {
    $sql = "
    SELECT
        members.id,
        members.name,
        members.phone_num,
        members.district,
        CASE
            WHEN COUNT(loans.id) > 0
            AND COUNT(
                CASE
                    WHEN loans.return_date IS NULL THEN 1
                    ELSE NULL
                END
            ) > 0 THEN 'Meminjam'
            ELSE 'Tidak Meminjam'
        END AS status,
        CASE
            WHEN COUNT(loans.id) > 0
            AND COUNT(
                CASE
                    WHEN loans.return_date IS NULL THEN 1
                    ELSE NULL
                END
            ) > 0 THEN MAX(
                CASE
                    WHEN loans.return_date IS NULL THEN loans.expected_return_date
                    ELSE NULL
                END
            )
            ELSE '-'
        END AS expected_return_date
    FROM members
        LEFT JOIN loans ON members.id = loans.member_id
    GROUP BY
        members.id
    ORDER BY members.id ASC;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $members = $result->fetch_all(MYSQLI_ASSOC);

    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = trim($_GET['search']) . '*';

        $sql = "
        SELECT
            members.id,
            members.name,
            members.phone_num,
            members.district,
            CASE
                WHEN COUNT(loans.id) > 0
                AND COUNT(
                    CASE
                        WHEN loans.return_date IS NULL THEN 1
                        ELSE NULL
                    END
                ) > 0 THEN 'Meminjam'
                ELSE 'Tidak Meminjam'
            END AS status,
            CASE
                WHEN COUNT(loans.id) > 0
                AND COUNT(
                    CASE
                        WHEN loans.return_date IS NULL THEN 1
                        ELSE NULL
                    END
                ) > 0 THEN MAX(
                    CASE
                        WHEN loans.return_date IS NULL THEN loans.expected_return_date
                        ELSE NULL
                    END
                )
                ELSE '-'
            END AS expected_return_date
        FROM members
            LEFT JOIN loans ON members.id = loans.member_id
        WHERE
            MATCH(members.id, members.name) AGAINST (? IN BOOLEAN MODE)
        GROUP BY
            members.id
        ORDER BY members.id ASC;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $searchedMembers = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    if (isset($member)) {
        if (isset($_GET['action']) == 'edit') {
            echo "Edit " . $member['name'] . " - " . APP;
        } else {
            echo $member['name'] . " - " . APP;
        }
    } else {
        if (isset($_GET['action']) == 'add') {
            echo "Tambah Anggota - " . APP;
        } else {
            echo "Anggota - " . APP;
        }
    }
    ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/lokapustaka/style.css">
</head>

<body>
    <main>
        <div class="top mb16">
            <p class="f14">
                <?php
                if (isset($member)) {
                    if (isset($_GET['action']) == 'edit') {
                        echo "<a class='f-sub' href='members.php'>/ Anggota </a><a class='f-sub' href='members.php?id=" . $member['id'] . "'>/ " . $member['id'] . "</a> / Edit";
                    } else {
                        echo "<a class='f-sub' href='members.php'>/ Anggota</a> / " . $member['id'];
                    }
                } else {
                    if (isset($_GET['action']) == 'add') {
                        echo "<a class='f-sub' href='members.php'>/ Anggota</a> / Tambah";
                    } else {
                        echo "/ Anggota";
                    }
                }
                ?>
            </p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>

        <?php if (isset($member)): ?>
            <?php if (isset($_GET['action']) == 'edit'): ?>
                <div class="head mb16">
                    <h3>Edit Anggota</h3>
                    <div class="head-button">
                        <a class="sec-b head-b" onclick="confirmEditMember()">
                            <img src="/lokapustaka/img/save-light.png" alt="Simpan">
                            Simpan
                        </a>
                    </div>
                </div>
                <div class="container2">
                    <form id="editMemberForm" method="post">
                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                        <div class="input-container2 mb12">
                            <label for="name" class="f12 f-sub mb4">Nama*</label>
                            <input type="text" name="name" id="name" placeholder="e.g. Adul Temon"
                                value="<?= $member['name'] ?>" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="phone_num" class="f12 f-sub mb4">No Telepon*</label>
                            <input type="text" name="phone_num" id="phone_num" placeholder="e.g. 081234567890"
                                value="<?= $member['phone_num'] ?>" required>
                        </div>
                        <p class="f14 mb12">Alamat</p>
                        <div class="input-container2 mb12">
                            <label for="street" class="f12 f-sub mb4">Jalan*</label>
                            <input type="text" name="street" id="street" placeholder="e.g. Jl. Fatmawati Raya"
                                value="<?= $member['street'] ?>" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="home_num" class="f12 f-sub mb4">No Rumah*</label>
                            <input type="text" name="home_num" id="home_num" placeholder="e.g. 24"
                                value="<?= $member['home_num'] ?>" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="province" class="f12 f-sub mb4">Daerah / Provinsi*</label><br>
                            <select name="province" id="province" required>
                                <option value="" disabled selected>Pilih...</option>
                            </select>
                        </div>
                        <div class="input-container2 mb12" id="regency_container" style="display: none;">
                            <label for="regency" class="f12 f-sub mb4">Kabupaten / Kota*</label><br>
                            <select name="regency" id="regency" required>
                                <option value="" disabled selected>Pilih...</option>
                            </select>
                        </div>
                        <div class="input-container2 mb12" id="district_container" style="display: none;">
                            <label for="district" class="f12 f-sub mb4">Kecamatan*</label><br>
                            <select name="district" id="district" required>
                                <option value="" disabled selected>Pilih...</option>
                            </select>
                        </div>
                        <div class="input-container2 mb12" id="village_container" style="display: none;">
                            <label for="village" class="f12 f-sub mb4">Kelurahan / Desa*</label><br>
                            <select name="village" id="village" required>
                                <option value="" disabled selected>Pilih...</option>
                            </select>
                        </div>
                        <div class="input-container2">
                            <label for="postal_code" class="f12 f-sub mb4">Kode Pos*</label>
                            <input type="text" name="postal_code" id="postal_code" placeholder="e.g. 12450"
                                value="<?= $member['postal_code'] ?>" required>
                        </div>
                    </form>
                </div><br>
            <?php else: ?>
                <div class="head mb16">
                    <div class="title">
                        <p class="f14 f-sub">Id Anggota</p>
                        <h3><?= $member['id'] ?></h3>
                    </div>
                    <div class="head-button">
                        <a href="?id=<?= $member['id'] ?>&action=edit" class="thi-b head-b mr8">
                            <img src="/lokapustaka/img/edit-dark.png" alt="Edit">
                            Edit
                        </a>
                        <a href="#" onclick="deleteMember('<?= $member['id'] ?>')" class="red-b head-b">
                            <img src="/lokapustaka/img/close-light.png" alt="Hapus">
                            Hapus
                        </a>
                    </div>
                </div>
                <div class="container mb16">
                    <div class="content">
                        <p class="f12 f-sub">Nama</p>
                        <h4 class="mb8"><?= $member['name'] ?></h4>
                        <p class="f12 f-sub">No Handphone</p>
                        <h4 class="mb8"><?= formatPhoneNumber($member['phone_num']) ?></h4>
                        <p class="f12 f-sub">Alamat Lengkap</p>
                        <h4 class="mb8">
                            <?= $member['street'] ?>, <?= $member['home_num'] ?>, <?= $member['village'] ?>,
                            <?= $member['district'] ?>, <?= $member['regency'] ?>, <?= $member['province'] ?>,
                            <?= $member['postal_code'] ?>
                        </h4>
                        <p class="f12 f-sub">Tanggal Daftar</p>
                        <h4 class="mb8"><?= formatDate($member['created_at']) ?></h4>
                        <p class="f12 f-sub">Total Peminjaman</p>
                        <h4 class="mb8"><?= $member['total_loans'] ?> Peminjaman</h4>
                        <p class="f12 f-sub">Peminjaman yang Telat Dikembalikan</p>
                        <h4 class="mb8"><?= $member['late_loans'] ?> Peminjaman</h4>
                        <p class="f12 f-sub">Status Terkini</p>
                        <h4 class="mb8"><?= $member['status'] ?></h4>
                        <p class="f12 f-sub">Tenggat Waktu</p>
                        <h4 class="mb8">
                            <?= ($member['expected_return_date'] !== '-') ? formatDate($member['expected_return_date']) : $member['expected_return_date'] ?>
                        </h4>
                        <p class="f12 f-sub">Didaftarkan oleh</p>
                        <h4><?= $member['created_by_name'] ?></h4>
                    </div>
                </div>
                <p class="f-sub mb8">Riwayat Peminjaman</p>
                <?php if (!empty($member_loans)): ?>
                    <table class="sortable">
                        <thead>
                            <tr>
                                <th class="w75" data-column="id">Id</th>
                                <th class="w75" data-column="book_id">Id Buku</th>
                                <th data-column="book_title">Judul Buku</th>
                                <th class="w75" data-column="borrow_date">Tgl Pinjam</th>
                                <th class="w75" data-column="expected_return_date">Tenggat Waktu</th>
                                <th class="w125" data-column="status">
                                    Status
                                    <img src="/lokapustaka/img/sort-asc.png" alt="Sort Icon" class="sort-icon">
                                </th>
                                <th class="w75" data-column="return_date">Tgl Pengembalian</th>
                                <th class="w125" data-column="fines">Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($member_loans as $loans): ?>
                                <tr>
                                    <td class="t-center" onclick="window.location.href='loans.php?id=<?= $loans['id'] ?>';"
                                        style="cursor: pointer;">
                                        <?= $loans['id'] ?>
                                    </td>
                                    <td class="t-center" onclick="window.location.href='books.php?id=<?= $loans['book_id'] ?>';"
                                        style="cursor: pointer;">
                                        <?= $loans['book_id'] ?>
                                    </td>
                                    <td onclick="window.location.href='books.php?id=<?= $loans['book_id'] ?>';"
                                        style="cursor: pointer;">
                                        <?= $loans['book_title'] ?>
                                    </td>
                                    <td class="t-center"><?= formatDate($loans['borrow_date']) ?></td>
                                    <td class="t-center"><?= formatDate($loans['expected_return_date']) ?></td>
                                    <td class="t-center"><?= $loans['status'] ?></td>
                                    <td class="t-center">
                                        <?= ($loans['return_date'] !== '-') ? formatDate($loans['return_date']) : $loans['return_date'] ?>
                                    </td>
                                    <td><?= ($loans['fines'] !== '-') ? 'Rp. ' . $loans['fines'] : $loans['fines'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table><br>
                <?php else: ?>
                    <p class="no-results">Tidak ada riwayat peminjaman.</p>
                <?php endif ?>
            <?php endif ?>
        <?php else: ?>
            <?php if (isset($_GET['action']) == 'add'): ?>
                <div class="head mb16">
                    <h3>Tambah Anggota</h3>
                    <div class="head-button">
                        <a class="sec-b head-b" onclick="confirmAddMember()">
                            <img src="/lokapustaka/img/save-light.png" alt="Simpan">
                            Simpan
                        </a>
                    </div>
                </div>
                <div class="container2">
                    <form id="addMemberForm" method="post">
                        <div class="input-container2 mb12">
                            <label for="name" class="f12 f-sub mb4">Nama*</label>
                            <input type="text" name="name" id="name" placeholder="e.g. Adul Temon" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="phone_num" class="f12 f-sub mb4">No Telepon*</label>
                            <input type="text" name="phone_num" id="phone_num" placeholder="e.g. 081234567890" required>
                        </div>
                        <p class="f14 mb12">Alamat</p>
                        <div class="input-container2 mb12">
                            <label for="street" class="f12 f-sub mb4">Jalan*</label>
                            <input type="text" name="street" id="street" placeholder="e.g. Jl. Fatmawati Raya" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="home_num" class="f12 f-sub mb4">No Rumah*</label>
                            <input type="text" name="home_num" id="home_num" placeholder="e.g. 24" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="province" class="f12 f-sub mb4">Daerah / Provinsi*</label><br>
                            <select name="province" id="province" required>
                                <option value="" disabled selected>Pilih...</option>
                            </select>
                        </div>
                        <div class="input-container2 mb12" id="regency_container" style="display: none;">
                            <label for="regency" class="f12 f-sub mb4">Kabupaten / Kota*</label><br>
                            <select name="regency" id="regency" required>
                                <option value="" disabled selected>Pilih...</option>
                            </select>
                        </div>
                        <div class="input-container2 mb12" id="district_container" style="display: none;">
                            <label for="district" class="f12 f-sub mb4">Kecamatan*</label><br>
                            <select name="district" id="district" required>
                                <option value="" disabled selected>Pilih...</option>
                            </select>
                        </div>
                        <div class="input-container2 mb12" id="village_container" style="display: none;">
                            <label for="village" class="f12 f-sub mb4">Kelurahan / Desa*</label><br>
                            <select name="village" id="village" required>
                                <option value="" disabled selected>Pilih...</option>
                            </select>
                        </div>
                        <div class="input-container2">
                            <label for="postal_code" class="f12 f-sub mb4">Kode Pos*</label>
                            <input type="text" name="postal_code" id="postal_code" placeholder="e.g. 12450" required>
                        </div>
                    </form>
                </div><br>
            <?php else: ?>
                <div class="head mb16">
                    <h3>Daftar Anggota</h3>
                    <div class="head-button">
                        <form action="" method="get">
                            <input type="text" class="search-b head-b mr8" name="search" id="search"
                                placeholder="Cari Id atau Nama"
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </form>
                        <a href="members.php?action=add" class="sec-b head-b">
                            <img src="/lokapustaka/img/plus-light.png" alt="Tambah">
                            Tambah
                        </a>
                    </div>
                </div>
                <?php if (isset($_GET['search']) && !empty(trim($_GET['search']))): ?>
                    <?php if (!empty($searchedMembers)): ?>
                        <table class="sortable">
                            <thead>
                                <tr>
                                    <th class="w75" data-column="id">
                                        Id
                                        <img src="/lokapustaka/img/sort-asc.png" alt="Sort Icon" class="sort-icon">
                                    </th>
                                    <th data-column="name">Nama</th>
                                    <th class="w125" data-column="phone_num">No Handphone</th>
                                    <th class="w150" data-column="district">Wilayah</th>
                                    <th class="w125" data-column="status">Status</th>
                                    <th class="w75" data-column="expected_return_date">Tenggat Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchedMembers as $searchedMember): ?>
                                    <tr onclick="window.location.href='members.php?id=<?= $searchedMember['id'] ?>';"
                                        style="cursor: pointer;">
                                        <td class="t-center"><?= $searchedMember['id'] ?></td>
                                        <td><?= $searchedMember['name'] ?></td>
                                        <td><?= formatPhoneNumber($searchedMember['phone_num']) ?></td>
                                        <td><?= $searchedMember['district'] ?></td>
                                        <td class="t-center"><?= $searchedMember['status'] ?></td>
                                        <td class="t-center">
                                            <?= ($searchedMember['expected_return_date'] !== '-') ? formatDate($searchedMember['expected_return_date']) : $searchedMember['expected_return_date'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table><br>
                    <?php else: ?>
                        <p class="no-results">Tidak ada hasil yang ditemukan untuk "<?= htmlspecialchars($_GET['search']) ?>".</p>
                    <?php endif ?>
                <?php else: ?>
                    <table class="sortable">
                        <thead>
                            <tr>
                                <th class="w75" data-column="id">
                                    Id
                                    <img src="/lokapustaka/img/sort-asc.png" alt="Sort Icon" class="sort-icon">
                                </th>
                                <th data-column="name">Nama</th>
                                <th class="w125" data-column="phone_num">No Handphone</th>
                                <th class="w150" data-column="district">Wilayah</th>
                                <th class="w125" data-column="status">Status</th>
                                <th class="w75" data-column="expected_return_date">Tenggat Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr onclick="window.location.href='members.php?id=<?= $member['id'] ?>';" style="cursor: pointer;">
                                    <td class="t-center"><?= $member['id'] ?></td>
                                    <td><?= $member['name'] ?></td>
                                    <td><?= formatPhoneNumber($member['phone_num']) ?></td>
                                    <td><?= $member['district'] ?></td>
                                    <td class="t-center"><?= $member['status'] ?></td>
                                    <td class="t-center">
                                        <?= ($member['expected_return_date'] !== '-') ? formatDate($member['expected_return_date']) : $member['expected_return_date'] ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table><br>
                <?php endif ?>
            <?php endif ?>
        <?php endif ?>
    </main>

    <script src="/lokapustaka/js/script.js"></script>
    <script src="/lokapustaka/js/address.js"></script>
</body>

</html>