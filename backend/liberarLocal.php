<?php
// Esto pertenece al kioskUI.php
include 'db_connection.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$local_id = $data['local_id'];

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

    // Actualizar NumeroLocal solo si pertenece a la plaza del usuario actual
    $sql = "UPDATE negocios SET NumeroLocal = 0 WHERE NumeroLocal = ? AND plaza_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $local_id, $plaza_id);
    $success = $stmt->execute();

    $response = ['success' => $success];
} else {
    // Si el usuario no tiene una plaza asociada
    $response = ['success' => false, 'message' => 'El usuario actual no tiene una plaza asociada.'];
}

$stmt->close();
CloseCon($conn);

header('Content-Type: application/json');
echo json_encode($response);
?>