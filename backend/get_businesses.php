<?php
include 'db_connection.php';

$conn = OpenCon();

$sql = "SELECT id, nombre FROM negocios";
$result = $conn->query($sql);

$businesses = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $businesses[] = $row;
    }
}

echo json_encode($businesses);

CloseCon($conn);
?>