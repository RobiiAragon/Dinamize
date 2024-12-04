<?php
// Esto pertenece al kioskUI.php
include 'db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $local_id = $_POST['local_id'];
    $negocio_id = $_POST['negocio_id'];

    $conn = OpenCon();

    // Obtener el user_id de la sesión
    $user_id = $_SESSION['user_id'];

    // Obtener el plaza_id asociado al user_id actual
    $sql = "SELECT id FROM plazas_comerciales WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $plaza = $result->fetch_assoc();
        $plaza_id = $plaza['id'];

        // Actualizar el NumeroLocal del negocio seleccionado solo si pertenece a la plaza del usuario actual
        $sql = "UPDATE negocios SET NumeroLocal = ? WHERE id = ? AND plaza_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $local_id, $negocio_id, $plaza_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "Asignación exitosa";
            } else {
                echo "No se pudo asignar el local. Verifica que el negocio pertenece a tu plaza.";
            }
        } else {
            echo "Error al asignar: " . $conn->error;
        }
    } else {
        // Si el usuario no tiene una plaza asociada
        echo "Error: El usuario actual no tiene una plaza asociada.";
    }

    $stmt->close();
    CloseCon($conn);
}
?>