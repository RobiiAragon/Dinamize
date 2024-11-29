<?php
include 'backend/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $localId = $_POST['localId'];
    $negocioId = $_POST['negocioId'];

    $conn = OpenCon();

    $sql = "UPDATE negocios SET NumeroLocal = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $localId, $negocioId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    CloseCon($conn);
}
?>