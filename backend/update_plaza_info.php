<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Error desconocido'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $success = true;
    $message = "";
    
    $conn = OpenCon();

    // Verificar si ya existe un registro para el user_id
    $sql_check = "SELECT * FROM plazas_comerciales WHERE user_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $exists = $result_check->num_rows > 0;
    $stmt_check->close();

    // Procesar la imagen recortada
    if (isset($_POST['croppedImage']) && !empty($_POST['croppedImage'])) {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['croppedImage']));
        
        if ($exists) {
            $sql = "UPDATE plazas_comerciales SET logo = ? WHERE user_id = ?";
        } else {
            $sql = "INSERT INTO plazas_comerciales (logo, user_id) VALUES (?, ?)";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->send_long_data(0, $imageData); // Enviar datos largos
        $stmt->bind_param("si", $imageData, $user_id);
        
        if ($stmt->execute()) {
            $message = "Logo de la plaza actualizado correctamente";
        } else {
            $success = false;
            $message = "Error al actualizar el logo de la plaza";
        }
        
        $stmt->close();
    }
    
    // Procesar otros campos
    if (isset($_POST['nombre']) || isset($_POST['categoria']) || isset($_POST['direccion']) || isset($_POST['telefono']) || isset($_POST['horarioApertura']) || isset($_POST['horarioCierre']) || isset($_POST['sitioWeb']) || isset($_POST['facebook']) || isset($_POST['instagram']) || isset($_POST['descripcion'])) {
        $nombre = $_POST['nombre'] ?? '';
        $categoria = $_POST['categoria'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $horarioApertura = $_POST['horarioApertura'] ?? '';
        $horarioCierre = $_POST['horarioCierre'] ?? '';
        $sitioWeb = $_POST['sitioWeb'] ?? '';
        $facebook = $_POST['facebook'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        
        if ($exists) {
            $sql = "UPDATE plazas_comerciales SET nombre = ?, categoria = ?, direccion = ?, telefono = ?, horarioApertura = ?, horarioCierre = ?, sitioWeb = ?, facebook = ?, instagram = ?, descripcion = ? WHERE user_id = ?";
        } else {
            $sql = "INSERT INTO plazas_comerciales (nombre, categoria, direccion, telefono, horarioApertura, horarioCierre, sitioWeb, facebook, instagram, descripcion, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssi", $nombre, $categoria, $direccion, $telefono, $horarioApertura, $horarioCierre, $sitioWeb, $facebook, $instagram, $descripcion, $user_id);
        if (!$stmt->execute()) {
            $success = false;
        }
        $stmt->close();
    }
    
    CloseCon($conn);

    $response['success'] = $success;
    $response['message'] = $success ? "Datos de la plaza actualizados correctamente" : "Error al actualizar los datos de la plaza";
}

echo json_encode($response);
exit();
?>