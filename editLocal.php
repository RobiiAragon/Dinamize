<?php
session_start();
include 'backend/db_connection.php';

// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$local_id = $_GET['id'];

$conn = OpenCon();

// Obtener información del local
$sql = "SELECT * FROM negocios WHERE id = ? AND plaza_id = (SELECT id FROM plazas_comerciales WHERE user_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $local_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $logoLocalSrc = isset($row['logo']) ? 'data:image/jpeg;base64,' . base64_encode($row['logo']) : 'img/noPlazaLogo.png';
    $nombre = isset($row['nombre']) ? $row['nombre'] : '';
    $categoria = isset($row['categoria']) ? $row['categoria'] : '';
    $telefono = isset($row['telefono']) ? $row['telefono'] : '';
    $horarioApertura = isset($row['horarioApertura']) ? $row['horarioApertura'] : '';
    $horarioCierre = isset($row['horarioCierre']) ? $row['horarioCierre'] : '';
    $sitioWeb = isset($row['sitioWeb']) ? $row['sitioWeb'] : '';
    $facebook = isset($row['facebook']) ? $row['facebook'] : '';
    $instagram = isset($row['instagram']) ? $row['instagram'] : '';
    $descripcion = isset($row['descripcion']) ? $row['descripcion'] : '';
    $numeroLocal = isset($row['NumeroLocal']) ? $row['NumeroLocal'] : '';
    $diasLaborales = isset($row['DiasLaborales']) ? $row['DiasLaborales'] : '';
} else {
    // Redirigir si no se encuentra el local
    $_SESSION['error'] = "Local no encontrado.";
    header("Location: manageLocals.php");
    exit();
}

$stmt->close();
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Local</title>
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
            <h1>Edit Local</h1>
            <form id="local-info-form" action="backend/update_local_info.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="local_id" value="<?php echo $local_id; ?>">
                <div class="form-group">
                    <label for="logoLocal">Logo del Local:</label>
                    <img src="<?php echo $logoLocalSrc; ?>" alt="Logo del local" class="profile-pic" id="logo-local">
                    <input type="file" id="logoLocal" name="logoLocal" style="display: none;" accept="image/*" onchange="loadImage(event, 'logo-local')">
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
                    <label for="nombre">Nombre del Local:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                </div>
                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <input type="text" id="categoria" name="categoria" value="<?php echo $categoria; ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo $telefono; ?>" required>
                </div>
                <div class="form-group">
                    <label for="horarioApertura">Horario de Apertura:</label>
                    <input type="time" id="horarioApertura" name="horarioApertura" value="<?php echo $horarioApertura; ?>" required>
                </div>
                <div class="form-group">
                    <label for="horarioCierre">Horario de Cierre:</label>
                    <input type="time" id="horarioCierre" name="horarioCierre" value="<?php echo $horarioCierre; ?>" required>
                </div>
                <div class="form-group">
                    <label for="sitioWeb">Sitio Web:</label>
                    <input type="text" id="sitioWeb" name="sitioWeb" value="<?php echo $sitioWeb; ?>">
                </div>
                <div class="form-group">
                    <label for="facebook">Facebook:</label>
                    <input type="text" id="facebook" name="facebook" value="<?php echo $facebook; ?>">
                </div>
                <div class="form-group">
                    <label for="instagram">Instagram:</label>
                    <input type="text" id="instagram" name="instagram" value="<?php echo $instagram; ?>">
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="diasLaborales">Días Laborales:</label>
                    <input type="text" id="diasLaborales" name="diasLaborales" value="<?php echo $diasLaborales; ?>">
                </div>
                <button type="submit" class="btn-save">Guardar</button>
            </form>
        </main>
    </div>
    <script src="js/darkMode.js"></script>
    <script src="libs/cropperjs/cropper.min.js"></script>
    <script src="js/manageLocals.js"></script>
    <button class="dark-mode-toggle" id="darkModeToggle" title="Cambiar modo oscuro">
    <i class="fas fa-moon"></i>
</button>
</body>
</html>