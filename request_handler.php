<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";

// Cek apakah parameter 'action' ada
if (!isset($_GET['action'])) {
    echo 'alert("Aksi tidak valid."); location.href = "/lokapustaka/pages/dashboard.php"';
}

// Ambil parameter 'action'
$action = $_GET['action'];

switch ($action) {
    case 'login':
        handleLogin($conn);
        break;

    case 'logout':
        handleLogout();
        break;

    case 'change_password':
        handleChangePassword($conn);
        break;

    case 'add_staff':
        handleAddStaff($conn);
        break;

    case 'edit_staff':
        handleEditStaff($conn);
        break;

    case 'delete_staff':
        handleDeleteStaff($conn);
        break;

    case 'reset_password':
        handleResetPassword($conn);
        break;

    case 'add_member':
        handleAddMember($conn);
        break;

    case 'edit_member':
        handleEditMember($conn);
        break;

    case 'delete_member':
        handleDeleteMember($conn);
        break;

    case 'extend_member':
        handleExtendMember($conn);
        break;

    case 'add_book':
        handleAddBook($conn);
        break;

    case 'edit_book':
        handleEditBook($conn);
        break;

    case 'delete_book':
        handleDeleteBook($conn);
        break;

    case 'add_loan':
        handleAddLoan($conn);
        break;

    case 'delete_loan':
        handleDeleteLoan($conn);
        break;

    case 'extend_loan':
        handleExtendLoan($conn);
        break;

    case 'return_loan':
        handleReturnLoan($conn);
        break;

    case 'fines_paid':
        handleFinesPaid($conn);
        break;

    default:
        echo '<script>alert("Aksi tidak valid."); window.location.href = "/lokapustaka/pages/dashboard.php"</script>';
}

function handleLogin($conn)
{
    // Pastikan permintaan POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo '<script>alert("Metode tidak valid."); window.location.href = "/lokapustaka/pages/dashboard.php"</script>';
    }

    // Ambil data dari form
    $id = $_POST['id'];
    $password = $_POST['password'];

    // Query ke database
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Login berhasil
            $_SESSION['users_id'] = $row['id'];
            $_SESSION['users_name'] = $row['name'];
            $_SESSION['users_roles'] = $row['roles'];
            header("Location: /lokapustaka/pages/dashboard.php");
        } else {
            echo '<script>alert("Password Salah."); history.back()</script>';
        }
    } else {
        echo '<script>alert("ID Staff tidak ditemukan."); history.back()</script>';
    }

    $stmt->close();
}

function handleLogout()
{
    session_start();
    session_unset();
    session_destroy();
    header("Location: /lokapustaka/pages/login.php");
    exit;
}

function handleChangePassword($conn)
{
    $userId = $_SESSION['users_id'];

    // Get the JSON data sent via fetch
    $data = json_decode(file_get_contents('php://input'), true);
    $currentPassword = $data['currentPassword'];
    $newPassword = $data['newPassword'];

    // Fetch the user's current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify the current password
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Password lama salah']);
        exit;
    }

    // Hash the new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("ss", $newPasswordHash, $userId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate password']);
    }
    $stmt->close();
    exit;
}

function handleAddStaff($conn)
{
    // Set header to ensure the response is in JSON format
    header('Content-Type: application/json');

    // Get the raw POST data (since it's JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        // If JSON is invalid, return an error message
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
        return;
    }

    $name = $data['name'];
    $phone_num = $data['phone_num'];
    $roles = $data['roles'];
    $created_by = $_SESSION['users_id'];
    $password = password_hash(DEFAULT_PASS, PASSWORD_DEFAULT);

    // Check if the phone number already exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE phone_num = ?');
    $stmt->bind_param('s', $phone_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Return JSON error response
        echo json_encode(['success' => false, 'message' => 'No Telepon sudah terdaftar!']);
    } else {
        // Insert new staff into the database
        $stmt = $conn->prepare('INSERT INTO users (name, password, phone_num, roles, created_by) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $name, $password, $phone_num, $roles, $created_by);

        if ($stmt->execute()) {
            $stmt = $conn->prepare('SELECT id FROM users WHERE name = ? AND phone_num = ? AND roles = ? AND created_by = ?');
            $stmt->bind_param('ssss', $name, $phone_num, $roles, $created_by);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $id = $row['id'];

            // Return JSON success response
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            // Return JSON error response if database insertion fails
            echo json_encode(['success' => false, 'message' => 'Gagal Mendaftarkan staff baru.']);
        }
    }
}

function handleEditStaff($conn)
{
    // Set header to ensure the response is in JSON format
    header('Content-Type: application/json');

    // Get the raw POST data (since it's JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        // If JSON is invalid, return an error message
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
        return;
    }

    $id = $data['id'];
    $name = $data['name'];
    $phone_num = $data['phone_num'];
    $roles = $data['roles'];

    $stmt = $conn->prepare('SELECT id FROM users WHERE phone_num = ? AND id != ?');
    $stmt->bind_param('ss', $phone_num, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'No Telepon sudah terdaftar!']);
    } else {
        $sql = "UPDATE users SET name = ?, phone_num = ?, roles = ? WHERE  id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $name, $phone_num, $roles, $id);

        if ($stmt->execute()) {
            if ($id === $_SESSION['users_id']) {
                $_SESSION['users_name'] = $name;
                $_SESSION['users_roles'] = $roles;
            }

            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal Mengedit Staff.']);
        }
    }
}


function handleDeleteStaff($conn)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $password = $data['password'];
    $id = $data['id'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bind_param('s', $_SESSION['users_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $stmt = $conn->prepare('UPDATE users SET created_by = NULL WHERE created_by = ?');
            $stmt->bind_param('s', $id);
            $stmt->execute();

            $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
            $stmt->bind_param('s', $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal untuk menghapus staff']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Password Salah']);
        }
    }
}

function handleResetPassword($conn)
{
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];
    $password = password_hash(DEFAULT_PASS, PASSWORD_DEFAULT);

    $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
    $stmt->bind_param('ss', $password, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mereset password']);
    }
}

function handleAddMember($conn)
{
    // Set header to ensure the response is in JSON format
    header('Content-Type: application/json');

    // Get the raw POST data (since it's JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        // If JSON is invalid, return an error message
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
        return;
    }

    $name = $data['name'];
    $phone_num = $data['phone_num'];
    $street = $data['street'];
    $home_num = $data['home_num'];
    $province = $data['province'];
    $regency = $data['regency'];
    $district = $data['district'];
    $village = $data['village'];
    $postal_code = $data['postal_code'];
    $created_by = $_SESSION['users_id'];

    // Check if the phone number already exists
    $stmt = $conn->prepare('SELECT * FROM members WHERE phone_num = ?');
    $stmt->bind_param('s', $phone_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Return JSON error response
        echo json_encode(['success' => false, 'message' => 'No Telepon sudah terdaftar!']);
    } else {
        // Insert new staff into the database
        $sql = "
        INSERT INTO members (name, phone_num, street, home_num, province, regency, district, village, postal_code, created_by, expired_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL " . EXPIRED_DATE . "))
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ssssssssss',
            $name,
            $phone_num,
            $street,
            $home_num,
            $province,
            $regency,
            $district,
            $village,
            $postal_code,
            $created_by
        );

        if ($stmt->execute()) {
            $stmt = $conn->prepare('SELECT id FROM members WHERE name = ? AND phone_num = ?');
            $stmt->bind_param('ss', $name, $phone_num);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $id = $row['id'];

            // Return JSON success response
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            // Return JSON error response if database insertion fails
            echo json_encode(['success' => false, 'message' => 'Gagal Mendaftarkan staff baru.']);
        }
    }
}

function handleEditMember($conn)
{
    // Set header to ensure the response is in JSON format
    header('Content-Type: application/json');

    // Get the raw POST data (since it's JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        // If JSON is invalid, return an error message
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
        return;
    }

    $id = $data['id'];
    $name = $data['name'];
    $phone_num = $data['phone_num'];
    $street = $data['street'];
    $home_num = $data['home_num'];
    $province = $data['province'];
    $regency = $data['regency'];
    $district = $data['district'];
    $village = $data['village'];
    $postal_code = $data['postal_code'];

    $stmt = $conn->prepare('SELECT * FROM members WHERE phone_num = ? AND id != ?');
    $stmt->bind_param('ss', $phone_num, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'No Telepon sudah terdaftar!']);
    } else {
        $sql = "
        UPDATE members
        SET 
            name = ?, 
            phone_num = ?, 
            street = ?, 
            home_num = ?, 
            province = ?, 
            regency = ?, 
            district = ?, 
            village = ?, 
            postal_code = ?
        WHERE id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ssssssssss',
            $name,
            $phone_num,
            $street,
            $home_num,
            $province,
            $regency,
            $district,
            $village,
            $postal_code,
            $id
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal Mengedit Staff.']);
        }
    }
}

function handleDeleteMember($conn)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $password = $data['password'];
    $id = $data['id'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bind_param('s', $_SESSION['users_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $stmt = $conn->prepare('DELETE FROM members WHERE id = ?');
            $stmt->bind_param('s', $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal untuk menghapus anggota']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Password Salah']);
        }
    }
}

function handleExtendMember($conn)
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $sql = "UPDATE members SET expired_date = DATE_ADD(NOW(), INTERVAL " . EXPIRED_DATE . ") WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);

    if ($stmt->execute()) {
        $sql = "SELECT expired_date FROM members WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $expired_date = formatDate($row['expired_date']);

        echo json_encode(['success' => true, 'id' => $id, 'expired_date' => $expired_date]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal Memperpanjang Anggota.']);
    }
}

function handleAddBook($conn)
{
    if (count($_FILES) > 0) {
        if (is_uploaded_file($_FILES['cover']['tmp_name'])) {
            $cover = file_get_contents($_FILES['cover']['tmp_name']);
        }
    }

    $isbn = $_POST['isbn'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $year_published = $_POST['year_published'];
    $available_stock = $_POST['available_stock'];
    $created_by = $_SESSION['users_id'];

    $stmt = $conn->prepare('SELECT id FROM books WHERE isbn = ?');
    $stmt->bind_param('s', $isbn);
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: application/json');

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'ISBN sudah terdaftar!']);
    } else {
        $sql = "
        INSERT INTO books (title, isbn, cover, author, category, publisher, year_published, available_stock,  created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssssis',
            $title,
            $isbn,
            $cover,
            $author,
            $category,
            $publisher,
            $year_published,
            $available_stock,
            $created_by
        );

        if ($stmt->execute()) {
            $stmt = $conn->prepare('SELECT id FROM books WHERE isbn = ?');
            $stmt->bind_param('s', $isbn);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $id = $row['id'];

            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mendaftarkan buku baru.']);
        }
    }
}

function handleEditBook($conn)
{
    if (count($_FILES) > 0 && is_uploaded_file($_FILES['cover']['tmp_name'])) {
        $cover = file_get_contents($_FILES['cover']['tmp_name']);
    } else {
        $cover = null; // No new cover uploaded
    }

    $id = $_POST['id'];
    $isbn = $_POST['isbn'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $year_published = $_POST['year_published'];
    $available_stock = $_POST['available_stock'];

    $stmt = $conn->prepare('SELECT id FROM books WHERE isbn = ? AND id != ?');
    $stmt->bind_param('ss', $isbn, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: application/json');

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'ISBN sudah terdaftar!']);
    } else {
        if ($cover !== null) {
            $sql = "
            UPDATE books
            SET 
                title = ?,
                isbn = ?,
                cover = ?,
                author = ?,
                category = ?,
                publisher = ?,
                year_published = ?,
                available_stock = ?
            WHERE id = ?;
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                'sssssssis',
                $title,
                $isbn,
                $cover,
                $author,
                $category,
                $publisher,
                $year_published,
                $available_stock,
                $id
            );
        } else {
            $sql = "
            UPDATE books
            SET 
                title = ?,
                isbn = ?,
                author = ?,
                category = ?,
                publisher = ?,
                year_published = ?,
                available_stock = ?
            WHERE id = ?;
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                'ssssssis',
                $title,
                $isbn,
                $author,
                $category,
                $publisher,
                $year_published,
                $available_stock,
                $id
            );
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengedit buku.']);
        }
    }
}

function handleDeleteBook($conn)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $password = $data['password'];
    $id = $data['id'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bind_param('s', $_SESSION['users_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $stmt = $conn->prepare('DELETE FROM books WHERE id = ?');
            $stmt->bind_param('s', $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal untuk menghapus buku']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Password Salah']);
        }
    }
}

function handleAddLoan($conn)
{
    header('Content-Type: application/json');

    // Get the raw POST data (since it's JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        // If JSON is invalid, return an error message
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
        return;
    }

    $book_id_isbn = $data['book_id_isbn'];
    $member_id = $data['member_id'];
    $phone_num = $data['phone_num'];
    $created_by = $_SESSION['users_id'];

    $sql = "
    SELECT id, available_stock, isbn FROM books WHERE id = ? OR isbn = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $book_id_isbn, $book_id_isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan.']);
        return;
    }

    $book_id = $row['id'];
    $available_stock = $row['available_stock'];

    if ($available_stock > 0) {
        $sql = "
        SELECT
            CASE
                WHEN expired_date > NOW() THEN 'Aktif'
                ELSE 'Kadaluarsa'
            END status
        FROM members
        WHERE
            id = ? 
            AND phone_num = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $member_id, $phone_num);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $member_status = $row['status'];

            if ($member_status == 'Aktif') {
                $sql = "
                SELECT id, return_date
                FROM loans
                WHERE
                    member_id = ?
                ORDER BY id DESC
                LIMIT 1
                ";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $member_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if (!$row || $row['return_date'] !== NULL) {
                    $sql = "
                    INSERT INTO
                        loans (
                            member_id,
                            book_id,
                            expected_return_date,
                            created_by
                        )
                    VALUES (
                            ?,
                            ?,
                            DATE_ADD(NOW(), INTERVAL " . EXPECTED_RETURN_DATE . "),
                            ?
                        )
                    ";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sss', $member_id, $book_id, $created_by);

                    if ($stmt->execute()) {
                        $id = $conn->insert_id;

                        $sql = "
                        UPDATE books SET available_stock = (available_stock - 1) WHERE id = ?
                        ";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('s', $book_id);
                        $stmt->execute();

                        $sql = "SELECT expected_return_date FROM loans WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        $expected_return_date = formatDate($row['expected_return_date']);

                        echo json_encode(['success' => true, 'id' => $id, 'expected_return_date' => $expected_return_date]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Gagal mendaftarkan peminjaman baru.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Anggota belum mengembalikan pinjaman sebelumnya!']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Anggota sudah kadaluarsa, silahkan perpanjang terlebih dahulu!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID Anggota dan Nomor Telepon tidak cocok!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Stock buku habis!']);
    }
}

function handleDeleteLoan($conn)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $password = $data['password'];
    $id = $data['id'];

    $stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->bind_param('s', $_SESSION['users_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $stmt = $conn->prepare('SELECT book_id FROM loans WHERE return_date IS NULL AND id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stmt = $conn->prepare('UPDATE books SET available_stock = (available_stock + 1) WHERE id = ?');
                $stmt->bind_param('s', $row['book_id']);
                $stmt->execute();
            }

            $stmt = $conn->prepare('DELETE FROM loans WHERE id = ?');
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal untuk menghapus peminjaman']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Password Salah']);
        }
    }
}

function handleExtendLoan($conn)
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $sql = "
    SELECT
        id,
        CASE
            WHEN DATEDIFF(NOW(), expected_return_date) <= 0 THEN NULL
            ELSE DATEDIFF(NOW(), expected_return_date)
        END AS day_late
    FROM loans
    WHERE
        id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $fines = FINES * $row['day_late'];

    if ($fines !== 0) {
        $sql = "
        UPDATE loans SET fines = ? WHERE id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $fines, $id);
        $stmt->execute();
    }

    $sql = "SELECT fines, max_extend FROM loans WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['fines'] !== NULL) {
        echo json_encode(['success' => false, 'message' => 'Anda telat mengembalikan/memperpanjang. Silahkan bayar denda terlebih dahulu.']);
        return false;
    }

    if ($row['max_extend'] === 0) {
        $sql = "
        UPDATE loans 
            SET expected_return_date = DATE_ADD(expected_return_date, INTERVAL " . EXPECTED_RETURN_DATE . "), 
                max_extend = 1, fines = NULL 
            WHERE id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $id);

        if ($stmt->execute()) {
            $sql = "SELECT expected_return_date FROM loans WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $expected_return_date = formatDate($row['expected_return_date']);

            echo json_encode(['success' => true, 'id' => $id, 'expected_return_date' => $expected_return_date]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperpanjang peminjaman.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Peminjaman sudah pernah diperpanjang.']);
    }
}

function handleReturnLoan($conn)
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $sql = "
    SELECT
        book_id,
        CASE
            WHEN DATEDIFF(NOW(), expected_return_date) <= 0 THEN NULL
            ELSE DATEDIFF(NOW(), expected_return_date)
        END AS day_late
    FROM loans
    WHERE
        id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $day_late = $row['day_late'];
    $fines = FINES * $day_late;

    if ($fines === 0) {
        $sql = "
        UPDATE books SET available_stock = (available_stock + 1) WHERE id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $row['book_id']);
        if ($stmt->execute()) {
            $sql = "
            UPDATE loans SET return_date = NOW() WHERE id = ?
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'id' => $id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengakhiri pinjaman.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengakhiri pinjaman.']);
        }
    } else {
        $sql = "
        UPDATE loans SET fines = ? WHERE id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $fines, $id);
        $stmt->execute();

        echo json_encode(['fines_set' => true, 'id' => $id, 'day_late' => $day_late, 'fines' => $fines]);
    }
}

function handleFinesPaid($conn)
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $sql = "
    UPDATE loans SET return_date = NOW() WHERE id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengakhiri pinjaman.']);
    }
}
?>