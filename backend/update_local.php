<?php
include 'db_connection.php';

$conn = OpenCon();

$localId = $_POST['localId'];
$negocioId = $_POST['negocioId'];

$response = array();

if (isset($localId) && isset($negocioId)) {
    $sql = "UPDATE negocios SET NumeroLocal = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $localId, $negocioId);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $stmt->error;
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['error'] = 'Datos no válidos.';
}

echo json_encode($response);

CloseCon($conn);
?>