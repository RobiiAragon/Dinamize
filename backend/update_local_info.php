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
    $sql_check = "SELECT * FROM negocios WHERE plaza_id = (SELECT id FROM plazas_comerciales WHERE user_id = ?)";
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
            $sql = "UPDATE negocios SET logo = ? WHERE plaza_id = (SELECT id FROM plazas_comerciales WHERE user_id = ?)";
        } else {
            $sql = "INSERT INTO negocios (logo, plaza_id) VALUES (?, (SELECT id FROM plazas_comerciales WHERE user_id = ?))";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->send_long_data(0, $imageData); // Enviar datos largos
        $stmt->bind_param("si", $imageData, $user_id);
        
        if ($stmt->execute()) {
            $message = "Logo del local actualizado correctamente";
        } else {
            $success = false;
            $message = "Error al actualizar el logo del local: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    // Procesar otros campos
    $fields = ['NumeroLocal', 'nombre', 'categoria', 'telefono', 'DiasLaborales', 'horarioApertura', 'horarioCierre', 'sitioWeb', 'facebook', 'instagram', 'descripcion', 'imagen1', 'imagen2', 'imagen3'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            if (strpos($field, 'imagen') !== false) {
                $value = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $value));
            }
            if ($exists) {
                $sql = "UPDATE negocios SET $field = ? WHERE plaza_id = (SELECT id FROM plazas_comerciales WHERE user_id = ?)";
            } else {
                $sql = "INSERT INTO negocios ($field, plaza_id) VALUES (?, (SELECT id FROM plazas_comerciales WHERE user_id = ?))";
            }
            $stmt = $conn->prepare($sql);
            if (strpos($field, 'imagen') !== false) {
                $stmt->send_long_data(0, $value); // Enviar datos largos
            }
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
    $response['message'] = $success ? "Datos del local actualizados correctamente" : $message;
}

echo json_encode($response);
exit();
?>