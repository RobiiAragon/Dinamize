<?php
include 'db_connection.php';

$conn = OpenCon();

$query = "SELECT l.id as local_id, n.nombre, n.logo 
          FROM locales l 
          LEFT JOIN negocios n ON l.negocio_id = n.id";
$result = $conn->query($query);

$assigned_businesses = array();
while($row = $result->fetch_assoc()) {
    $assigned_businesses[] = $row;
}

CloseCon($conn);
echo json_encode($assigned_businesses);
?>