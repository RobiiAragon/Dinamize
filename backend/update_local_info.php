<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'path/to/your/error.log'); // Cambia 'path/to/your/error.log' por la ruta de tu archivo de log

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $local_id = $_POST['local_id'];
    $success = true;
    $message = "";
    
    $conn = OpenCon();

    // Verificar si ya existe un registro para el local_id
    $sql_check = "SELECT * FROM negocios WHERE id = ? AND plaza_id = (SELECT id FROM plazas_comerciales WHERE user_id = ?)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $local_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $exists = $result_check->num_rows > 0;
    $stmt_check->close();

    if (!$exists) {
        $_SESSION['error'] = "Local no encontrado o no autorizado.";
        header("Location: ../manageLocals.php");
        exit();
    }

    // Procesar la imagen recortada
    if (isset($_POST['croppedImage']) && !empty($_POST['croppedImage'])) {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['croppedImage']));
        
        $sql = "UPDATE negocios SET logo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->send_long_data(0, $imageData); // Enviar datos largos
        $stmt->bind_param("si", $imageData, $local_id);
        
        if ($stmt->execute()) {
            $message = "Logo del local actualizado correctamente";
        } else {
            $success = false;
            $message = "Error al actualizar el logo del local: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    // Procesar otros campos
    $fields = ['NumeroLocal', 'nombre', 'categoria', 'telefono', 'DiasLaborales', 'horarioApertura', 'horarioCierre', 'sitioWeb', 'facebook', 'instagram', 'descripcion'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            $sql = "UPDATE negocios SET $field = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $value, $local_id);
            if (!$stmt->execute()) {
                $success = false;
                $message = "Error al actualizar el campo $field: " . $stmt->error;
                break;
            }
            $stmt->close();
        }
    }
    
    CloseCon($conn);

    if ($success) {
        $_SESSION['message'] = "Datos del local actualizados correctamente";
    } else {
        $_SESSION['error'] = $message;
    }

    header("Location: ../manageLocals.php");
    exit();
}
?>