<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'path/to/your/error.log'); // Cambia 'path/to/your/error.log' por la ruta de tu archivo de log

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
            $message = "Error al actualizar el logo de la plaza: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    // Procesar otros campos
    $fields = ['nombre', 'categoria', 'direccion', 'telefono', 'horarioApertura', 'horarioCierre', 'sitioWeb', 'facebook', 'instagram', 'descripcion'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            if ($exists) {
                $sql = "UPDATE plazas_comerciales SET $field = ? WHERE user_id = ?";
            } else {
                $sql = "INSERT INTO plazas_comerciales ($field, user_id) VALUES (?, ?)";
            }
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $value, $user_id);
            if (!$stmt->execute()) {
                $success = false;
                $message = "Error al actualizar el campo $field: " . $stmt->error;
                break;
            }
            $stmt->close();
        }
    }
    
    CloseCon($conn);

    $response['success'] = $success;
    $response['message'] = $success ? "Datos de la plaza actualizados correctamente" : $message;
}

echo json_encode($response);
exit();
?>