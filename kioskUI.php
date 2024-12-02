<?php
session_start();
include 'backend/db_connection.php';

// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = OpenCon();

// Obtener la lista de negocios con sus locales asignados
$sql = "SELECT * FROM negocios WHERE plaza_id = (SELECT id FROM plazas_comerciales WHERE user_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$negocios = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $negocios[] = $row;
    }
} else {
    echo "Error en la consulta SQL: " . $conn->error;
}

$coordenadasLocales = [
  1 => ['x' => 10, 'y' => 20, 'width' => 80, 'height' => 120],
];

$stmt->close();
CloseCon($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Kiosk</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/kiosk.css">
    <script src="js/kiosco.js"></script>
    <link rel="stylesheet" href="css/fontAwesome/all.min.css">
    <script src="js/assignLocal.js"></script>
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
                <li><a href="manageLocals.php" data-section="manage-locals">Manage Locals</a></li>
                <li><a href="kioskUI.php" data-section="kiosk-ui" class="active">Kiosk UI</a></li>
            </ul>
            <div class="logout-container">
                <a href="backend/logout.php" class="logout-button">
                    <i class="fas fa-sign-out-alt logout-icon"></i>
                    Logout
                </a> 
            </div>
        </aside>
        <main class="main-content">
          <div class="content-header">
              <h1>Digital Kiosk</h1>
          </div>
          <div class="kiosk-container">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500">
            <g id="local1" class="local-clickable" style="cursor: pointer">
                <polygon class="cls-1" points="3.29 5.6 108.11 5.6 110.15 155.21 3.29 155.21 3.29 5.6"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 1";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[1]['x'] ?>" 
                      y="<?= $coordenadasLocales[1]['y'] ?>" 
                      width="<?= $coordenadasLocales[1]['width'] ?>" 
                      height="<?= $coordenadasLocales[1]['height'] ?>">
                      <div xmlns="http://www.w3.org/1999/xhtml" class="local">
                          <img src="data:image/jpeg;base64,<?= base64_encode($negocio['logo']) ?>" 
                              alt="Logo de <?= htmlspecialchars($negocio['nombre']) ?>"
                              class="local-logo">
                          <p class="local-name"><?= htmlspecialchars($negocio['nombre']) ?></p>
                      </div>
                    </foreignObject>
                    <?php
                }
                CloseCon($conn);
                ?>
            </g>
        </svg>
          </div>
        </main>
    </div>
    <!-- Popup para asignar negocio -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span id="close-popup" class="close-button">&times;</span>
            <h2>Asignar negocio al local</h2>
            <form id="assign-form">
                <input type="hidden" name="local_id" id="local_id">
                <label for="negocio_id">Seleccione un negocio:</label>
                <select name="negocio_id" id="negocio_id" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach($negocios as $negocio): ?>
                        <option value="<?php echo $negocio['id']; ?>"><?php echo $negocio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Asignar</button>
            </form>
        </div>
    </div>
    <button class="dark-mode-toggle" id="darkModeToggle" title="Cambiar modo oscuro">
    <i class="fas fa-moon"></i>
</button>
<script src="js/darkMode.js"></script>
</body>
</html>