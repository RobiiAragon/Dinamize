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

    // Procesar la imagen recortada
    if (isset($_POST['croppedImage']) && !empty($_POST['croppedImage'])) {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['croppedImage']));
        
        $sql = "UPDATE infousuarios SET fotoPerfil = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->send_long_data(0, $imageData); // Enviar datos largos
        $stmt->bind_param("si", $imageData, $user_id);
        
        if ($stmt->execute()) {
            $message = "Foto de perfil actualizada correctamente";
        } else {
            $success = false;
            $message = "Error al actualizar la foto de perfil";
        }
        
        $stmt->close();
    }
    
    // Procesar otros campos
    if (isset($_POST['numeroTelefono']) || isset($_POST['genero']) || isset($_POST['nombreUsuario'])) {
        if (isset($_POST['nombreUsuario'])) {
            $nombreUsuario = $_POST['nombreUsuario'];
            $sql = "UPDATE usuarios SET nombreUsuario = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nombreUsuario, $user_id);
            if (!$stmt->execute()) {
                $success = false;
            }
            $stmt->close();
        }

        if (isset($_POST['numeroTelefono']) || isset($_POST['genero'])) {
            $numeroTelefono = $_POST['numeroTelefono'] ?? '';
            $genero = $_POST['genero'] ?? '';
            
            $sql = "UPDATE infousuarios SET numeroTelefono = ?, genero = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $numeroTelefono, $genero, $user_id);
            if (!$stmt->execute()) {
                $success = false;
            }
            $stmt->close();
        }
    }
    
    CloseCon($conn);

    $response['success'] = $success;
    $response['message'] = $success ? "Datos actualizados correctamente" : "Error al actualizar los datos";
}

echo json_encode($response);
exit();
?>