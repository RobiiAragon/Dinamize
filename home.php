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
                <li><a href="#" data-section="user-info" class="active">User Info</a></li>
                <li><a href="#" data-section="manage-plaza">Manage Plaza</a></li>
                <li><a href="#" data-section="kiosk-react">Kiosk React</a></li>
                <li><a href="#" data-section="kiosk-ui">Kiosk UI</a></li>
                <li><a href="#" data-section="oak-labs">Oak Labs</a></li>
                <li><a href="#" data-section="digital-signage">Digital Signage</a></li>
            </ul>
            <div class="logout-container">
            <a href="backend/logout.php" class="logout-button">
                <i class="fas fa-sign-out-alt logout-icon"></i>
                Logout
            </a>
        </div>
        </aside>
        <main class="main-content">
            <section id="user-info">
                <h1>User Info</h1>
                <form id="user-info-form" action="backend/update_user_info.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="fotoPerfil">Foto de perfil:</label>
                        <img src="<?php echo $fotoPerfilSrc; ?>" alt="Foto de perfil" class="profile-pic" id="profile-pic">
                        <input type="file" id="fotoPerfil" name="fotoPerfil" style="display: none;" accept="image/*" onchange="loadImage(event)">
                        <i class="fas fa-edit edit-icon" onclick="document.getElementById('fotoPerfil').click();"></i>
                    </div>
                    <div class="form-group" id="crop-container" style="display: none;">
                        <label for="cropper">Redimensionar Foto:</label>
                        <div class="cropper-wrapper">
                            <img id="cropper" style="max-width: 100%;">
                        </div>
                        <button type="button" class="btn-crop" onclick="cropImage()">Recortar y Guardar</button>
                    </div>
                    <input type="hidden" id="croppedImage" name="croppedImage">
                
                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nombreUsuario">Nombre de Usuario:</label>
                        <input type="text" id="nombreUsuario" name="nombreUsuario" value="<?php echo $nombreUsuario; ?>" readonly>
                        <i class="fas fa-edit edit-icon" onclick="enableEdit('nombreUsuario');"></i>
                    </div>
                    <div class="form-group">
                        <label for="numeroTelefono">Número de Teléfono:</label>
                        <input type="text" id="numeroTelefono" name="numeroTelefono" value="<?php echo $numeroTelefono; ?>" readonly>
                        <i class="fas fa-edit edit-icon" onclick="enableEdit('numeroTelefono');"></i>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombres:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" value="<?php echo $apellidos; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="fechaNacimiento">Fecha de Nacimiento:</label>
                        <input type="date" id="fechaNacimiento" name="fechaNacimiento" value="<?php echo $fechaNacimiento; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="genero">Género:</label>
                        <select id="genero" name="genero" disabled>
                            <option value="Masculino" <?php echo ($genero == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Femenino" <?php echo ($genero == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                            <option value="No binario" <?php echo ($genero == 'No binario') ? 'selected' : ''; ?>>No binario</option>
                        </select>
                        <i class="fas fa-edit edit-icon" onclick="enableEdit('genero');"></i>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <button type="button" class="btn-change-password">Cambiar Contraseña</button>
                    </div>
                    <button type="submit" class="btn-save" style="display: none;">Guardar</button>
                </form>
            </section>
            <section id="manage-plaza">
                <h1>Administrar Plaza Comercial</h1>
                <form id="plaza-form" action="backend/update_plaza_info.php" method="POST" enctype="multipart/form-data">
                    <div class="plaza-info">
                        <div class="form-group">
                            <label for="logoPlaza">Logo de la Plaza:</label>
                            <img src="<?php 
                                echo isset($plazaData['logo']) ? 
                                    'data:image/jpeg;base64,' . base64_encode($plazaData['logo']) : 
                                    'img/noPlazaLogo.png'; 
                            ?>" alt="Logo Plaza" class="profile-pic" id="plaza-logo">
                            <input type="file" id="logoInput" name="logoPlaza" style="display: none;" 
                                accept="image/*" onchange="loadLogo(event)">
                            <i class="fas fa-edit edit-icon" onclick="document.getElementById('logoInput').click();"></i>
                        </div>
                        <div class="form-group" id="crop-container-logo" style="display: none;">
                            <label for="cropper-logo">Redimensionar Logo:</label>
                            <div class="cropper-wrapper">
                                <img id="cropper-logo" style="max-width: 100%;">
                            </div>
                            <button type="button" class="btn-crop" onclick="cropLogo()">Recortar y Guardar</button>
                        </div>
                        <input type="hidden" id="croppedLogo" name="croppedLogo">
                        <div class="form-group">
                            <label for="nombrePlaza">Nombre de la Plaza:</label>
                            <input type="text" id="nombrePlaza" name="nombrePlaza" 
                                value="<?php echo isset($plazaData['nombre']) ? $plazaData['nombre'] : ''; ?>" readonly>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('nombrePlaza');"></i>
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoría:</label>
                            <select id="categoria" name="categoria" disabled>
                                <option value="shopping">Centro Comercial</option>
                                <option value="strip">Plaza Strip</option>
                                <option value="lifestyle">Centro de Estilo de Vida</option>
                                <option value="outlet">Outlets</option>
                            </select>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('categoria');"></i>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección:</label>
                            <textarea id="direccion" name="direccion" readonly rows="3"><?php echo isset($plazaData['direccion']) ? $plazaData['direccion'] : ''; ?></textarea>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('direccion');"></i>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="tel" id="telefono" name="telefono" 
                                value="<?php echo isset($plazaData['telefono']) ? $plazaData['telefono'] : ''; ?>" readonly>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('telefono');"></i>
                        </div>
                        <div class="form-group">
                            <label for="horarioApertura">Horario de Apertura:</label>
                            <input type="time" id="horarioApertura" name="horarioApertura" 
                                value="<?php echo isset($plazaData['horarioApertura']) ? $plazaData['horarioApertura'] : ''; ?>" readonly>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('horarioApertura');"></i>
                        </div>
                        <div class="form-group">
                            <label for="horarioCierre">Horario de Cierre:</label>
                            <input type="time" id="horarioCierre" name="horarioCierre" 
                                value="<?php echo isset($plazaData['horarioCierre']) ? $plazaData['horarioCierre'] : ''; ?>" readonly>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('horarioCierre');"></i>
                        </div>
                        <div class="form-group">
                            <label for="sitioWeb">Sitio Web:</label>
                            <input type="url" id="sitioWeb" name="sitioWeb" 
                                value="<?php echo isset($plazaData['sitioWeb']) ? $plazaData['sitioWeb'] : ''; ?>" readonly>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('sitioWeb');"></i>
                        </div>
                        <div class="form-group">
                            <label for="facebook">Facebook:</label>
                            <input type="url" id="facebook" name="facebook" 
                                value="<?php echo isset($plazaData['facebook']) ? $plazaData['facebook'] : ''; ?>" readonly>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('facebook');"></i>
                        </div>
                        <div class="form-group">
                            <label for="instagram">Instagram:</label>
                            <input type="url" id="instagram" name="instagram" 
                                value="<?php echo isset($plazaData['instagram']) ? $plazaData['instagram'] : ''; ?>" readonly>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('instagram');"></i>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" readonly rows="4"><?php echo isset($plazaData['descripcion']) ? $plazaData['descripcion'] : ''; ?></textarea>
                            <i class="fas fa-edit edit-icon" onclick="enableEdit('descripcion');"></i>
                        </div>
                        <button type="submit" class="btn-save" style="display: none;">Guardar Cambios</button>
                    </div>
                </form>
            </section>
            <section id="kiosk-react">
                <h1>Kiosk React</h1>
                <!-- Contenido de Kiosk React aquí -->
            </section>
            <section id="kiosk-ui">
                <h1>Kiosk UI</h1>
                <!-- Contenido de Kiosk UI aquí -->
            </section>
            <section id="oak-labs">
                <h1>Oak Labs</h1>
                <!-- Contenido de Oak Labs aquí -->
            </section>
            <section id="digital-signage">
                <h1>Digital Signage</h1>
                <!-- Contenido de Digital Signage aquí -->
            </section>
        </main>
    </div>
    <script src="libs/cropperjs/cropper.min.js"></script>
    <script src="js/home.js"></script>
</body>
</html>