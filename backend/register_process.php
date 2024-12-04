<?php
session_start();
include 'db_connection.php';

// Función para generar una clave de activación única de 8 caracteres
function generateUniqueActivationKey($conn) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $key_length = 8;
    do {
        $activation_key = '';
        for ($i = 0; $i < $key_length; $i++) {
            $activation_key .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Verificar si la clave ya existe
        $sql_check = "SELECT id FROM Claves_de_activacion WHERE clave = ?";
        $stmt_check = $conn->prepare($sql_check);
        if (!$stmt_check) {
            $_SESSION['error'] = "Error en la preparación de la consulta de verificación de clave: " . $conn->error;
            header("Location: ../register.php");
            exit();
        }
        $stmt_check->bind_param("s", $activation_key);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $stmt_check->close();
    } while ($result_check->num_rows > 0);

    return $activation_key;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = $_POST['Nombres'];
    $apellidos = $_POST['apellidos'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $activation_key = $_POST['activation_key'];

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: ../register.php");
        exit();
    }

    // Encriptar la contraseña
    $hashed_password = sha1($password);

    $conn = OpenCon();

    // Verificar la clave de activación
    $sql_key = "SELECT id, user_id FROM Claves_de_activacion WHERE clave = ?";
    $stmt_key = $conn->prepare($sql_key);

    if (!$stmt_key) {
        $_SESSION['error'] = "Error en la preparación de la consulta de clave: " . $conn->error;
        header("Location: ../register.php");
        exit();
    }

    $stmt_key->bind_param("s", $activation_key);
    $stmt_key->execute();
    $result_key = $stmt_key->get_result();

    if ($result_key->num_rows === 0) {
        $_SESSION['error'] = "Clave de activación inválida.";
        header("Location: ../register.php");
        exit();
    }

    $row_key = $result_key->fetch_assoc();
    if (!is_null($row_key['user_id'])) {
        $_SESSION['error'] = "Esta clave de activación ya está asignada.";
        header("Location: ../register.php");
        exit();
    }

    $stmt_key->close();

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

        // Asignar el user_id a la clave de activación
        $sql_update_key = "UPDATE Claves_de_activacion SET user_id = ? WHERE id = ?";
        $stmt_update_key = $conn->prepare($sql_update_key);

        if ($stmt_update_key) {
            $stmt_update_key->bind_param("ii", $user_id, $row_key['id']);
            $stmt_update_key->execute();
            $stmt_update_key->close();
        }

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
            // Generar una nueva clave de activación única
            $new_activation_key = generateUniqueActivationKey($conn);

            // Insertar la nueva clave en la base de datos
            $sql_insert_key = "INSERT INTO Claves_de_activacion (clave) VALUES (?)";
            $stmt_insert_key = $conn->prepare($sql_insert_key);

            if ($stmt_insert_key) {
                $stmt_insert_key->bind_param("s", $new_activation_key);
                if (!$stmt_insert_key->execute()) {
                    $_SESSION['error'] = "Error al insertar la nueva clave de activación.";
                }
                $stmt_insert_key->close();
            } else {
                $_SESSION['error'] = "Error en la preparación de la inserción de la nueva clave: " . $conn->error;
            }

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