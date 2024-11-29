<?php
session_start();
include 'backend/db_connection.php';

// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = OpenCon();

// Procesar la imagen recortada
if (isset($_POST['croppedImage']) && !empty($_POST['croppedImage'])) {
    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['croppedImage']));
    
    $sql = "UPDATE infousuarios SET fotoPerfil = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $imageData, $user_id);
    
    if ($stmt->execute()) {
        $message = "Foto de perfil actualizada correctamente";
    } else {
        $success = false;
        $message = "Error al actualizar la foto de perfil";
    }
    
    $stmt->close();
    
    // Responder con JSON para la actualización de imagen
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit();
}

// Después de obtener el user_id
$sqlPlaza = "SELECT * FROM plazas_comerciales WHERE user_id = ?";
$stmt = $conn->prepare($sqlPlaza);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultPlaza = $stmt->get_result();
$plazaData = $resultPlaza->fetch_assoc();

// Obtener información del usuario desde la tabla infousuarios
$sql = "SELECT infousuarios.*, usuarios.email, usuarios.nombreUsuario  FROM infousuarios JOIN usuarios ON infousuarios.user_id = usuarios.id WHERE infousuarios.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fotoPerfilSrc = isset($row['fotoPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($row['fotoPerfil']) : 'img/noUserPhoto.png';
    $email = isset($row['email']) ? $row['email'] : '';
    $numeroTelefono = isset($row['numeroTelefono']) ? $row['numeroTelefono'] : '';
    $nombreUsuario = isset($row['nombreUsuario']) ? $row['nombreUsuario'] : ''; 
    $nombre = isset($row['nombre']) ? $row['nombre'] : '';
    $apellidos = isset($row['apellidos']) ? $row['apellidos'] : '';
    $fechaNacimiento = isset($row['fechaNacimiento']) ? $row['fechaNacimiento'] : '';
    $genero = isset($row['genero']) ? $row['genero'] : '';
} else {
    // Si no se encuentran datos del usuario, inicializa las variables con valores predeterminados
    $fotoPerfilSrc = 'img/noUserPhoto.png';
    $email = '';
    $numeroTelefono = '';
    $nombre = '';
    $apellidos = '';
    $fechaNacimiento = '';
    $genero = '';
}

$stmt->close();
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fontAwesome/all.min.css">
    <link rel="stylesheet" href="libs/cropperjs/cropper.min.css">
</head>
<body>
<?php if(isset($_SESSION['message'])): ?>
    <div class="tooltip-container success">
        <div class="tooltip-content">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
            ?>
        </div>
    </div>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <div class="tooltip-container error">
        <div class="tooltip-content">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    </div>
<?php endif; ?>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="index.php">
                <img src="img/logo.png" alt="Logo">
                </a>
                <h2>Dashboard</h2>
            </div>
            <ul>
                <li><a href="userInfo.php" data-section="user-info">User Info</a></li>
                <li><a href="managePlaza.php" data-section="manage-plaza">Manage Plaza</a></li>
                <li><a href="manageLocals.php" data-section="manage-locals" class="active">Manage Locals</a></li>
                <li><a href="kioskUI.php" data-section="kiosk-ui">Kiosk UI</a></li>
            </ul>
            <div class="logout-container">
            <a href="backend/logout.php" class="logout-button">
                <i class="fas fa-sign-out-alt logout-icon"></i>
                Logout
            </a> 
        </div>
        </aside>
        <main class="main-content">
    <h1>Manage Local</h1>
    <form id="local-info-form" action="backend/update_local_info.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="logoLocal">Logo del Local:</label>
            <img src="<?php echo $logoLocalSrc ?? 'img/noPlazaLogo.png'; ?>" alt="Logo del local" class="profile-pic" id="profile-pic">
            <input type="file" id="logoLocal" name="logoLocal" style="display: none;" accept="image/*" onchange="loadImage(event)">
            <i class="fas fa-edit edit-icon" onclick="document.getElementById('logoLocal').click();"></i>
        </div>
        <div class="form-group" id="crop-container" style="display: none;">
            <label for="cropper">Redimensionar Logo:</label>
            <div class="cropper-wrapper">
                <img id="cropper" style="max-width: 100%;">
            </div>
            <button type="button" class="btn-crop" onclick="cropImage()">Recortar y Guardar</button>
        </div>
        <input type="hidden" id="croppedImage" name="croppedImage">
        
        <div class="form-group">
            <label for="nombreLocal">Nombre del Local:</label>
            <input type="text" id="nombreLocal" name="nombreLocal" value="<?php echo $nombreLocal ?? ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoría:</label>
            <input type="text" id="categoria" name="categoria" value="<?php echo $categoria ?? ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" value="<?php echo $telefono ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="horarioApertura">Horario de Apertura:</label>
            <input type="time" id="horarioApertura" name="horarioApertura" value="<?php echo $horarioApertura ?? ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="horarioCierre">Horario de Cierre:</label>
            <input type="time" id="horarioCierre" name="horarioCierre" value="<?php echo $horarioCierre ?? ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="sitioWeb">Sitio Web:</label>
            <input type="url" id="sitioWeb" name="sitioWeb" value="<?php echo $sitioWeb ?? ''; ?>" placeholder="https://">
        </div>

        <div class="form-group">
            <label for="facebook">Facebook:</label>
            <input type="url" id="facebook" name="facebook" value="<?php echo $facebook ?? ''; ?>" placeholder="https://">
        </div>

        <div class="form-group">
            <label for="instagram">Instagram:</label>
            <input type="url" id="instagram" name="instagram" value="<?php echo $instagram ?? ''; ?>" placeholder="https://">
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4"><?php echo $descripcion ?? ''; ?></textarea>
        </div>

        <div class="form-group">
            <label for="imagen1">Imagen 1:</label>
            <input type="file" id="imagen1" name="imagen1" accept="image/*">
            <?php if (isset($imagen1Src)): ?>
                <img src="<?php echo $imagen1Src; ?>" alt="Imagen 1" class="preview-image">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="imagen2">Imagen 2:</label>
            <input type="file" id="imagen2" name="imagen2" accept="image/*">
            <?php if (isset($imagen2Src)): ?>
                <img src="<?php echo $imagen2Src; ?>" alt="Imagen 2" class="preview-image">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="imagen3">Imagen 3:</label>
            <input type="file" id="imagen3" name="imagen3" accept="image/*">
            <?php if (isset($imagen3Src)): ?>
                <img src="<?php echo $imagen3Src; ?>" alt="Imagen 3" class="preview-image">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-save">Guardar</button>
    </form>
</main>
    </div>
    <script src="libs/cropperjs/cropper.min.js"></script>
    <script src="js/userInfo.js"></script>
</body>
</html>

