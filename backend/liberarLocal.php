<?php
//esto pertenece al kioskUI.php
include 'db_connection.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$local_id = $data['local_id'];

$conn = OpenCon();

$sql = "UPDATE negocios SET NumeroLocal = 0 WHERE NumeroLocal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $local_id);
$success = $stmt->execute();

$response = ['success' => $success];

$stmt->close();
CloseCon($conn);

header('Content-Type: application/json');
echo json_encode($response);