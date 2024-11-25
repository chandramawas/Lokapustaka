<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/includes/aside.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "
    SELECT a.*, b.name AS created_by_name
    FROM users as a
    LEFT JOIN users AS b ON a.created_by = b.id
    WHERE a.id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $staff = $result->fetch_assoc();
    } else {
        ?>
        <script>alert("Staff tidak ditemukan."); location.href = "staff.php"</script>
        <?php
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM users ORDER BY id ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $staffs = $result->fetch_all(MYSQLI_ASSOC);

    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = trim($_GET['search']) . '*';

        $sql = "
        SELECT * FROM users
        WHERE MATCH(id, name) AGAINST(? IN BOOLEAN MODE)
        ORDER BY id ASC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $searchedStaffs = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    if (isset($staff)) {
        if (isset($_GET['action']) == 'edit') {
            echo "Edit " . $staff['name'] . " - " . APP;
        } else {
            echo $staff['name'] . " - " . APP;
        }
    } else {
        if (isset($_GET['action']) == 'add') {
            echo "Tambah Staff - " . APP;
        } else {
            echo "Staff - " . APP;
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
                if (isset($staff)) {
                    if (isset($_GET['action']) == 'edit') {
                        echo "<a class='f-sub' href='staff.php'>/ Staff </a><a class='f-sub' href='staff.php?id=" . $staff['id'] . "'>/ " . $staff['id'] . "</a> / Edit";
                    } else {
                        echo "<a class='f-sub' href='staff.php'>/ Staff</a> / " . $staff['id'];
                    }
                } else {
                    if (isset($_GET['action']) == 'add') {
                        echo "<a class='f-sub' href='staff.php'>/ Staff</a> / Tambah";
                    } else {
                        echo "/ Staff";
                    }
                }
                ?>
            </p>
            <p id="wib-time" class="f12"><?= $current_time . " WIB" ?></p>
        </div>
        <?php if (isset($staff)): ?>
            <?php if (isset($_GET['action']) == 'edit'): ?>
                <div class="head mb16">
                    <h3>Edit Staff</h3>
                    <div class="head-button">
                        <a class="sec-b head-b" onclick="confirmEditStaff()">
                            <img src="/lokapustaka/img/save-light.png" alt="Simpan">
                            Simpan
                        </a>
                    </div>
                </div>
                <div class="container2">
                    <form id="editStaffForm" method="post">
                        <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                        <div class="input-container2 mb12">
                            <label for="name" class="f12 f-sub mb4">Nama</label>
                            <input type="text" name="name" id="name" placeholder="e.g. Adul Temon" value="<?= $staff['name'] ?>"
                                required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="phone_num" class="f12 f-sub mb4">No Telepon</label>
                            <input type="text" name="phone_num" id="phone_num" placeholder="e.g. 081234567890"
                                value="<?= $staff['phone_num'] ?>" required>
                        </div>
                        <div class="input-container2">
                            <label for="roles" class="f12 f-sub mb4">Role</label><br>
                            <select name="roles" id="roles" required>
                                <option value="Staff" <?= $staff['roles'] === 'Staff' ? 'selected' : '' ?>>Staff</option>
                                <option value="Admin" <?= $staff['roles'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Staff View -->
                <div class="head mb16">
                    <div class="title">
                        <p class="f14 f-sub">Kode Staff</p>
                        <h3><?= $staff['id'] ?></h3>
                    </div>
                    <div class="head-button">
                        <a href="#" onclick="resetPasswordStaff('<?= $staff['id'] ?>')" class="fou-b head-b mr8">
                            Reset Password
                        </a>
                        <a href="?id=<?= $staff['id'] ?>&action=edit" class="thi-b head-b mr8">
                            <img src="/lokapustaka/img/edit-dark.png" alt="Edit">
                            Edit
                        </a>
                        <a href="#" onclick="deleteStaff('<?= $staff['id'] ?>')" class="red-b head-b">
                            <img src="/lokapustaka/img/close-light.png" alt="Hapus">
                            Hapus
                        </a>
                    </div>
                </div>
                <div class="container mb16">
                    <div class="content">
                        <p class="f12 f-sub">Nama</p>
                        <h4 class="mb8"><?= $staff['name'] ?></h4>
                        <p class="f12 f-sub">No Telepon</p>
                        <h4 class="mb8"><?= formatPhoneNumber($staff['phone_num']) ?></h4>
                        <p class="f12 f-sub">Role</p>
                        <h4 class="mb8"><?= $staff['roles'] ?></h4>
                        <?php if ($staff['created_by'] !== NULL): ?>
                            <p class="f12 f-sub">Didaftarkan oleh</p>
                            <h4 class="mb8"><?= $staff['created_by_name'] ?></h4>
                        <?php endif ?>
                        <p class="f12 f-sub">Waktu Didaftarkan</p>
                        <h4 class=""><?= formatDate($staff['created_at']) ?></h4>
                    </div>
                </div>
            <?php endif ?>
        <?php else: ?>
            <?php if (isset($_GET['action']) == 'add'): ?>
                <div class="head mb16">
                    <h3>Tambah Staff</h3>
                    <div class="head-button">
                        <a class="sec-b head-b" onclick="confirmAddStaff()">
                            <img src="/lokapustaka/img/save-light.png" alt="Simpan">
                            Simpan
                        </a>
                    </div>
                </div>
                <div class="container2">
                    <form id="addStaffForm" method="post">
                        <div class="input-container2 mb12">
                            <label for="name" class="f12 f-sub mb4">Nama</label>
                            <input type="text" name="name" id="name" placeholder="e.g. Adul Temon" required>
                        </div>
                        <div class="input-container2 mb12">
                            <label for="phone_num" class="f12 f-sub mb4">No Telepon</label>
                            <input type="text" name="phone_num" id="phone_num" placeholder="e.g. 081234567890" required>
                        </div>
                        <div class="input-container2">
                            <label for="roles" class="f12 f-sub mb4">Role</label><br>
                            <select name="roles" id="roles" required>
                                <option value="Staff">Staff</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="head mb16">
                    <h3>Daftar Staff</h3>
                    <div class="head-button">
                        <form action="" method="get">
                            <input type="text" class="search-b head-b mr8" name="search" id="search"
                                placeholder="Cari Id atau Nama Staff"
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </form>
                        <a href="staff.php?action=add" class="sec-b head-b">
                            <img src="/lokapustaka/img/plus-light.png" alt="Tambah">
                            Tambah
                        </a>
                    </div>
                </div>
                <?php if (isset($_GET['search']) && !empty(trim($_GET['search']))): ?>
                    <?php if (!empty($searchedStaffs)): ?>
                        <table class="sortable">
                            <thead>
                                <tr>
                                    <th class="w75" data-column="id">
                                        Id
                                        <img src="/lokapustaka/img/sort-asc.png" alt="Sort Icon" class="sort-icon">
                                    </th>
                                    <th data-column="name">
                                        Nama
                                    </th>
                                    <th class="w125" data-column="phone_num">
                                        No Telepon
                                    </th>
                                    <th class="w75" data-column="roles">
                                        Role
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchedStaffs as $searchedStaff): ?>
                                    <tr onclick="window.location.href='?id=<?= $searchedStaff['id'] ?>';" style="cursor: pointer;">
                                        <td class="t-center"><?= $searchedStaff['id'] ?></td>
                                        <td><?= $searchedStaff['name'] ?></td>
                                        <td><?= formatPhoneNumber($searchedStaff['phone_num']) ?></td>
                                        <td class="t-center"><?= $searchedStaff['roles'] ?></td>
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
                                <th data-column="name">
                                    Nama
                                </th>
                                <th class="w125" data-column="phone_num">
                                    No Telepon
                                </th>
                                <th class="w75" data-column="roles">
                                    Role
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staffs as $staff): ?>
                                <tr onclick="window.location.href='?id=<?= $staff['id'] ?>';" style="cursor: pointer;">
                                    <td class="t-center"><?= $staff['id'] ?></td>
                                    <td><?= $staff['name'] ?></td>
                                    <td><?= formatPhoneNumber($staff['phone_num']) ?></td>
                                    <td class="t-center"><?= $staff['roles'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table><br>
                <?php endif ?>
            <?php endif ?>
        <?php endif ?>
    </main>
    <script src="/lokapustaka/js/script.js"></script>
</body>

</html>