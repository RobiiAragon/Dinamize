<?php
// No debe haber espacios ni líneas en blanco antes de <?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

header('Content-Type: application/json');

$response = array();

if (isset($_GET['localId'])) {
    $localId = intval($_GET['localId']);
    $conn = OpenCon();

    if ($conn->connect_error) {
        $response['error'] = "Connection failed: " . $conn->connect_error;
        echo json_encode($response);
        exit();
    }

    $sql = "SELECT nombre, logo FROM negocios WHERE NumeroLocal = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $response['error'] = "Prepare failed: " . $conn->error;
        echo json_encode($response);
        CloseCon($conn);
        exit();
    }

    $stmt->bind_param("i", $localId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $business = $result->fetch_assoc();
        echo json_encode($business);
    } else {
        echo json_encode(array("nombre" => "Sin asignar", "logo" => null));
    }

    $stmt->close();
    CloseCon($conn);
} else {
    $response['error'] = "Invalid request";
    echo json_encode($response);
}
?>
<!-- No debe haber espacios ni líneas en blanco después de ?> -->