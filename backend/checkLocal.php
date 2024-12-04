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

    // Ahora buscamos en negocios donde NumeroLocal y plaza_id coinciden
    $sql = "SELECT id FROM negocios WHERE NumeroLocal = ? AND plaza_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $local_id, $plaza_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = ['hasNegocio' => $result->num_rows > 0];
} else {
    // Si no se encuentra una plaza para el usuario actual
    $response = ['hasNegocio' => false];
}

$stmt->close();
CloseCon($conn);

header('Content-Type: application/json');
echo json_encode($response);