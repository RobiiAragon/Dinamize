<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Error desconocido',
    'local' => null
];

if (isset($_GET['id'])) {
    $local_id = $_GET['id'];
    
    $conn = OpenCon();
    
    $sql = "SELECT * FROM negocios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $local_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $local = $result->fetch_assoc();
        $response['success'] = true;
        $response['message'] = 'Datos del local obtenidos correctamente';
        $response['local'] = $local;
    } else {
        $response['message'] = 'No se encontró el local';
    }
    
    $stmt->close();
    CloseCon($conn);
}

echo json_encode($response);
exit();
?>