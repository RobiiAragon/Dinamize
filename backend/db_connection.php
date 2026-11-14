<?php
function OpenCon() {
    $dbhost = getenv("DB_HOST") ?: "localhost";
    $dbuser = getenv("DB_USER") ?: "root";
    $dbpass = getenv("DB_PASS") ?: "";
    $dbname = getenv("DB_NAME") ?: "Dinamize";

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function CloseCon($conn) {
    $conn->close();
}
?>