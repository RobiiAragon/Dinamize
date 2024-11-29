<?php
include 'db_connection.php';

header('Content-Type: application/json');

$response = array();

if (isset($_POST['localId']) && isset($_POST['negocioId'])) {
    $localId = intval($_POST['localId']);
    $negocioId = intval($_POST['negocioId']);
    $conn = OpenCon();

    if ($conn->connect_error) {
        $response['error'] = "Connection failed: " . $conn->connect_error;
        echo json_encode($response);
        exit();
    }

    // Obtener el negocio actual asignado al local
    $sql = "SELECT id FROM negocios WHERE NumeroLocal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $localId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $currentBusiness = $result->fetch_assoc();
        $currentBusinessId = $currentBusiness['id'];

        // Actualizar el negocio actual a null
        $sql = "UPDATE negocios SET NumeroLocal = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $currentBusinessId);
        $stmt->execute();
    }

    // Asignar el nuevo negocio al local
    $sql = "UPDATE negocios SET NumeroLocal = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $localId, $negocioId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
    } else {
        $response['error'] = "Error al asignar el negocio.";
    }

    $stmt->close();
    CloseCon($conn);
} else {
    $response['error'] = "Invalid request";
}

echo json_encode($response);
?>