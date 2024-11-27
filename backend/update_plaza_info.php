<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $success = true;
    $message = "";

    $conn = OpenCon();

    // Obtener el ID del usuario desde la sesión
    $user_id = $_SESSION['user_id'];

    // Verificar si el usuario ya tiene una plaza asignada
    $sql = "SELECT id FROM plazas_comerciales WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $plaza_id = null;

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $plaza_id = $row['id'];
    }

    $stmt->close();

    // Procesar la imagen del logo recortado
    if (isset($_POST['croppedLogo']) && !empty($_POST['croppedLogo'])) {
        $logoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['croppedLogo']));
    }

    // Procesar otros campos
    $nombrePlaza = $_POST['nombrePlaza'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $horarioApertura = $_POST['horarioApertura'] ?? '';
    $horarioCierre = $_POST['horarioCierre'] ?? '';
    $sitioWeb = $_POST['sitioWeb'] ?? '';
    $facebook = $_POST['facebook'] ?? '';
    $instagram = $_POST['instagram'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if ($plaza_id) {
        // Realizar actualización
        $sql = "UPDATE plazas_comerciales SET nombre = ?, categoria = ?, direccion = ?, telefono = ?, horarioApertura = ?, horarioCierre = ?, sitioWeb = ?, facebook = ?, instagram = ?, descripcion = ?, logo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->send_long_data(10, $logoData); // Enviar datos largos
        $stmt->bind_param("ssssssssssbi", $nombrePlaza, $categoria, $direccion, $telefono, $horarioApertura, $horarioCierre, $sitioWeb, $facebook, $instagram, $descripcion, $logoData, $plaza_id);
    } else {
        // Realizar inserción
        $sql = "INSERT INTO plazas_comerciales (user_id, nombre, categoria, direccion, telefono, horarioApertura, horarioCierre, sitioWeb, facebook, instagram, descripcion, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->send_long_data(11, $logoData); // Enviar datos largos
        $stmt->bind_param("isssssssssss", $user_id, $nombrePlaza, $categoria, $direccion, $telefono, $horarioApertura, $horarioCierre, $sitioWeb, $facebook, $instagram, $descripcion, $logoData);
    }

    if (!$stmt->execute()) {
        $success = false;
    }

    $stmt->close();
    CloseCon($conn);

    if ($success) {
        $_SESSION['message'] = "Datos actualizados correctamente";
    } else {
        $_SESSION['error'] = "Error al actualizar los datos";
    }

    // Redireccionar de vuelta a managePLaza.php
    header("Location: ../managePLaza.php");
    exit();
}
?>