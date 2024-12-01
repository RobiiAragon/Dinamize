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
    $stmt->bind_param("si", $imageData, $user_id);

    if ($stmt->execute()) {
        $success = true;
        $message = "Foto de perfil actualizada correctamente";
    } else {
        $success = false;
        $message = "Error al actualizar la foto de perfil";
    }

    $stmt->close();

    // Actualizar el response
    $response['success'] = $success;
    $response['message'] = $message;

    // Cerrar la conexión
    CloseCon($conn);

    // Enviar la respuesta y terminar el script
    echo json_encode($response);
    exit();
}

    // ... Código anterior permanece igual ...
    
    // Procesar otros campos
    if (isset($_POST['nombreUsuario']) || isset($_POST['numeroTelefono']) || isset($_POST['genero'])) {
        $success = true;
        
        // Conectar a la base de datos
        $conn = OpenCon();
    
        // Actualizar tabla 'usuarios' si 'nombreUsuario' está presente
        if (isset($_POST['nombreUsuario'])) {
            $nombreUsuario = $_POST['nombreUsuario'];
            $sql = "UPDATE usuarios SET nombreUsuario = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nombreUsuario, $user_id);
            if (!$stmt->execute()) {
                $success = false;
                $message .= " Error al actualizar nombre de usuario.";
            }
            $stmt->close();
        }
    
        // Actualizar tabla 'infousuarios' si 'numeroTelefono' o 'genero' están presentes
        if (isset($_POST['numeroTelefono']) || isset($_POST['genero'])) {
            // Obtener valores actuales de los campos
            $sql = "SELECT numeroTelefono, genero FROM infousuarios WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($currentTelefono, $currentGenero);
            $stmt->fetch();
            $stmt->close();
    
            // Si el campo no está en $_POST, usar el valor actual
            $numeroTelefono = isset($_POST['numeroTelefono']) ? $_POST['numeroTelefono'] : $currentTelefono;
            $genero = isset($_POST['genero']) ? $_POST['genero'] : $currentGenero;
    
            $sql = "UPDATE infousuarios SET numeroTelefono = ?, genero = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $numeroTelefono, $genero, $user_id);
            if (!$stmt->execute()) {
                $success = false;
                $message .= " Error al actualizar información del usuario.";
            }
            $stmt->close();
        }
    
        CloseCon($conn);
    
        $response['success'] = $success;
        $response['message'] = $success ? "Datos actualizados correctamente." : $message;
    }
    
    // ... El resto del código permanece igual ...

echo json_encode($response);
exit();
}
?>