<?php
session_start();
session_destroy(); // Destruye la sesión
header("Location: ../index.php"); // Redirecciona al inicio
exit();
?>