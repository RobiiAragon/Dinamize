<?php
session_start();
include 'backend/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $telefono = $_POST['telefono'];
    $horarioApertura = $_POST['horarioApertura'];
    $horarioCierre = $_POST['horarioCierre'];
    $sitioWeb = $_POST['sitioWeb'];
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $descripcion = $_POST['descripcion'];
    $DiasLaborales = $_POST['DiasLaborales'];
    $logo = null;

    if (isset($_POST['croppedImage']) && !empty($_POST['croppedImage'])) {
        $logo = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['croppedImage']));
    }

    $conn = OpenCon();

    $sql = "INSERT INTO negocios (plaza_id, nombre, categoria, telefono, horarioApertura, horarioCierre, sitioWeb, facebook, instagram, descripcion, DiasLaborales, logo) 
            VALUES ((SELECT id FROM plazas_comerciales WHERE user_id = ?), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Error en la consulta: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("isssssssssss", $user_id, $nombre, $categoria, $telefono, $horarioApertura, $horarioCierre, $sitioWeb, $facebook, $instagram, $descripcion, $DiasLaborales, $logo);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Negocio añadido correctamente.";
    } else {
        $_SESSION['error'] = "Error al añadir el negocio: " . $stmt->error;
    }

    $stmt->close();
    CloseCon($conn);

    header("Location: manageLocals.php");
    exit();
}
?>