<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $local_id = $_POST['local_id'];
    $negocio_id = $_POST['negocio_id'];

    $conn = OpenCon();

    // Actualizar el NumeroLocal del negocio seleccionado
    $sql = "UPDATE negocios SET NumeroLocal = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $local_id, $negocio_id);

    if ($stmt->execute()) {
        echo "Asignación exitosa";
    } else {
        echo "Error al asignar: " . $conn->error;
    }

    $stmt->close();
    CloseCon($conn);
}
?>