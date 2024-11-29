<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plaza_id = $_POST['plaza_id'];
    $nombreLocal = $_POST['nombreLocal'];
    $categoria = $_POST['categoria'];
    $telefono = $_POST['telefono'];
    $horarioApertura = $_POST['horarioApertura'];
    $horarioCierre = $_POST['horarioCierre'];
    $sitioWeb = $_POST['sitioWeb'];
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $descripcion = $_POST['descripcion'];

    $logoLocal = file_get_contents($_FILES['logoLocal']['tmp_name']);
    $imagen1 = file_get_contents($_FILES['imagen1']['tmp_name']);
    $imagen2 = file_get_contents($_FILES['imagen2']['tmp_name']);
    $imagen3 = file_get_contents($_FILES['imagen3']['tmp_name']);

    $conn = OpenCon();

    $sql = "INSERT INTO locales (plaza_id, logo, nombre, categoria, telefono, horarioApertura, horarioCierre, sitioWeb, facebook, instagram, descripcion, imagen1, imagen2, imagen3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssssss", $plaza_id, $logoLocal, $nombreLocal, $categoria, $telefono, $horarioApertura, $horarioCierre, $sitioWeb, $facebook, $instagram, $descripcion, $imagen1, $imagen2, $imagen3);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Local agregado exitosamente.";
    } else {
        $_SESSION['error'] = "Error al agregar el local.";
    }

    $stmt->close();
    CloseCon($conn);

    header("Location: ../manageLocals.php");
    exit();
}
?>