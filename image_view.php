<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/db.php";

if (isset($_GET['id'])) {
    $sql = "SELECT cover FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $img = $row['cover'];
    $extension = pathinfo($img, PATHINFO_EXTENSION);

    if ($extension === 'png') {
        header("Content-type: image/png");
    } else {
        header("Content-type: image/jpeg");
    }

    echo $img;
}
?>