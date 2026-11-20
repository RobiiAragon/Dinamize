<?php
function OpenCon() {
    $dbhost = getenv("DB_HOST") ?: "localhost";
    $dbuser = getenv("DB_USER") ?: "root";
    $dbpass = getenv("DB_PASS") ?: "";
    $dbname = getenv("DB_NAME") ?: "Dinamize";

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        // Log the details for the admin, but don't show host/user info to visitors.
        error_log("Dinamize DB connection failed: " . $conn->connect_error);
        http_response_code(500);
        die("No se pudo conectar con la base de datos.");
    }

    return $conn;
}

function CloseCon($conn) {
    $conn->close();
}
?>