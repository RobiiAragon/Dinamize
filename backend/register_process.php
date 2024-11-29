<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = $_POST['Nombres'];
    $apellidos = $_POST['apellidos'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: ../register.php");
        exit();
    }

    // Encriptar la contraseña
    $hashed_password = sha1($password);

    $conn = OpenCon();

    // Insertar en la tabla usuarios
    $sql = "INSERT INTO usuarios (nombreUsuario, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $_SESSION['error'] = "Error en la preparación de la consulta: " . $conn->error;
        header("Location: ../register.php");
        exit();
    }

    $stmt->bind_param("sss", $nombres, $email, $hashed_password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Insertar en la tabla infousuarios
        $sql_info = "INSERT INTO infousuarios (user_id, nombres, apellidos, fechaNacimiento) VALUES (?, ?, ?, ?)";
        $stmt_info = $conn->prepare($sql_info);

        if (!$stmt_info) {
            $_SESSION['error'] = "Error en la preparación de la consulta de información del usuario: " . $conn->error;
            header("Location: ../register.php");
            exit();
        }

        $stmt_info->bind_param("isss", $user_id, $nombres, $apellidos, $birthdate);

        if ($stmt_info->execute()) {
            $_SESSION['success'] = "Registro exitoso. Por favor, inicia sesión.";
            header("Location: ../login.php");
            exit();
        } else {
            $_SESSION['error'] = "Error al registrar la información del usuario.";
        }

        $stmt_info->close();
    } else {
        $_SESSION['error'] = "Error al registrar el usuario.";
    }

    $stmt->close();
    CloseCon($conn);

    header("Location: ../register.php");
    exit();
}
?>