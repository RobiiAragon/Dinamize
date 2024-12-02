<?php
//esto pertenece al kioskUI.php
include 'db_connection.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$local_id = $data['local_id'];

$conn = OpenCon();

$sql = "SELECT id FROM negocios WHERE NumeroLocal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $local_id);
$stmt->execute();
$result = $stmt->get_result();

$response = ['hasNegocio' => $result->num_rows > 0];

$stmt->close();
CloseCon($conn);

header('Content-Type: application/json');
echo json_encode($response);