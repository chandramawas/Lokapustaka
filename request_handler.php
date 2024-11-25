<?php
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
            echo "Password salah!";
        }
    } else {
        echo "Staff ID tidak ditemukan!";
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
    $stmt = $conn->prepare('SELECT * FROM users WHERE phone_num = ?');
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

    $stmt = $conn->prepare('SELECT * FROM users WHERE phone_num = ? AND id != ?');
    $stmt->bind_param('ss', $phone_num, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'No Telepon sudah terdaftar!']);
    } else {
        $stmt = $conn->prepare('UPDATE users SET name = ?, phone_num = ?, roles = ? WHERE id = ?');
        $stmt->bind_param('ssss', $name, $phone_num, $roles, $id);

        if ($stmt->execute()) {
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
        INSERT INTO members (name, phone_num, street, home_num, province, regency, district, village, postal_code, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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

?>