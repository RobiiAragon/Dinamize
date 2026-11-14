<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = OpenCon();

    if ($conn->connect_error) {
        $_SESSION['error'] = "Error de conexión a la base de datos: " . $conn->connect_error;
        header("Location: ../login.php");
        exit();
    }

    $sql = "SELECT u.id, u.nombreUsuario, u.email, u.password AS password_hash, i.* 
            FROM usuarios u 
            LEFT JOIN infousuarios i ON u.id = i.user_id 
            WHERE u.email = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['error'] = "Error en la preparación de la consulta: " . $conn->error;
        header("Location: ../login.php");
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->num_rows > 0 ? $result->fetch_assoc() : null;

    // Modern hashes (password_hash) plus legacy SHA-1 accounts, which get upgraded on login.
    $valid = false;
    if ($row) {
        $stored = $row['password_hash'];
        if (password_verify($password, $stored)) {
            $valid = true;
        } elseif (hash_equals($stored, sha1($password))) {
            $valid = true;
            $upgrade = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $upgrade->bind_param("si", $newHash, $row['id']);
            $upgrade->execute();
        }
    }

    if ($valid) {
        session_regenerate_id(true);
        
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
        header("Location: ../login.php");
    }

    $stmt->close();
    CloseCon($conn);
}
?>