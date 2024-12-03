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

// Obtener la lista de negocios
$sql = "SELECT id, nombre FROM negocios WHERE plaza_id = (SELECT id FROM plazas_comerciales WHERE user_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$negocios = [];
while ($row = $result->fetch_assoc()) {
    $negocios[] = $row;
}

$stmt->close();
CloseCon($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Local</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fontAwesome/all.min.css">
    <link rel="stylesheet" href="libs/cropperjs/cropper.min.css">
    <script>
        // Aplicar la clase dark-mode al cargar la página si está en localStorage
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>

<body>
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
                <li><a href="kiosco.php" data-section="kiosco">Digital Kiosk</a></li>
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
            <form id="local-info-form" action="editLocal.php" method="GET">
                <div class="form-group">
                    <label for="negocio">Selecciona un negocio:</label>
                    <select id="negocio" name="id" required>
                        <option value="">Selecciona un negocio</option>
                        <?php foreach ($negocios as $negocio): ?>
                            <option value="<?php echo $negocio['id']; ?>"><?php echo $negocio['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-save">Editar</button>
            </form>

            <h2>Añadir Nuevo Negocio</h2>
            <form id="add-local-form" action="addLocal.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nuevoNombre">Nombre del Local:</label>
                    <input type="text" id="nuevoNombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="nuevoCategoria">Categoría:</label>
                    <input type="text" id="nuevoCategoria" name="categoria" required>
                </div>
                <div class="form-group">
                    <label for="nuevoTelefono">Teléfono:</label>
                    <input type="text" id="nuevoTelefono" name="telefono" required>
                </div>
                <div class="form-group">
                    <label for="nuevoHorarioApertura">Horario de Apertura:</label>
                    <input type="time" id="nuevoHorarioApertura" name="horarioApertura" required>
                </div>
                <div class="form-group">
                    <label for="nuevoHorarioCierre">Horario de Cierre:</label>
                    <input type="time" id="nuevoHorarioCierre" name="horarioCierre" required>
                </div>
                <div class="form-group">
                    <label for="nuevoSitioWeb">Sitio Web:</label>
                    <input type="text" id="nuevoSitioWeb" name="sitioWeb">
                </div>
                <div class="form-group">
                    <label for="nuevoFacebook">Facebook:</label>
                    <input type="text" id="nuevoFacebook" name="facebook">
                </div>
                <div class="form-group">
                    <label for="nuevoInstagram">Instagram:</label>
                    <input type="text" id="nuevoInstagram" name="instagram">
                </div>
                <div class="form-group">
                    <label for="nuevoDescripcion">Descripción:</label>
                    <textarea id="nuevoDescripcion" name="descripcion"></textarea>
                </div>
                <div class="form-group">
                    <label for="DiasLaborales">Días Laborales:</label>
                    <input type="text" id="DiasLaborales" name="DiasLaborales">
                </div>
                <div class="form-group">
                    <label for="nuevoLogo">Logo del Local:</label>
                    <label for="nuevoLogo" class="custom-file-upload">
                        Seleccionar archivo
                    </label>
                    <input type="file" id="nuevoLogo" name="logo" accept="image/*">
                    <span id="file-name"></span>
                </div>
                <input type="hidden" id="croppedImage" name="croppedImage">
                <button type="submit" class="btn-save">Añadir</button>
            </form>
            <div class="form-group" id="crop-container" style="display: none;">
                <label for="cropper">Redimensionar Logo:</label>
                <div class="cropper-wrapper">
                    <img id="cropper" style="max-width: 100%;">
                </div>
                <button type="button" class="btn-crop">Recortar y Guardar</button>
            </div>
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