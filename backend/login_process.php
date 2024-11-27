<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = sha1($_POST['password']);

    $conn = OpenCon();

    $sql = "SELECT u.id, u.nombreUsuario, u.email, i.* 
            FROM usuarios u 
            LEFT JOIN infousuarios i ON u.id = i.user_id 
            WHERE u.email = ? AND u.password = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Guardar toda la información necesaria en la sesión
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['email'] = $row['email'];
        
        // Guardar la foto de perfil si existe
        if ($row['fotoPerfil'] !== null) {
            $_SESSION['fotoPerfil'] = $row['fotoPerfil'];
        }
        
        header("Location: ../userInfo.php");
    } else {
        $_SESSION['error'] = "Correo electrónico o contraseña incorrectos.";
        header("Location: login.php");
    }

    $stmt->close();
    CloseCon($conn);
}
?>