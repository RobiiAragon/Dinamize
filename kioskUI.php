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
    1 => ['x' => 3.29, 'y' => 5, 'width' => 100, 'height' => 149],
    2 => ['x' => 108.11, 'y' => 5.6, 'width' => 91.92, 'height' => 88.85],
    3 => ['x' => 200.03, 'y' => 5.6, 'width' => 84.77, 'height' => 88.85],
    4 => ['x' => 284.79, 'y' => 5, 'width' => 92.77, 'height' => 88.85],
    5 => ['x' => 374, 'y' => 5, 'width' => 83.91, 'height' => 124.94],
    6 => ['x' => 377.56, 'y' => 125, 'width' => 80, 'height' => 60],
    7 => ['x' => 377.56, 'y' => 211.13, 'width' => 83.91, 'height' => 122.89],
    8 => ['x' => 377.56, 'y' => 314.02, 'width' => 83.91, 'height' => 81.45],
    9 => ['x' => 377.56, 'y' => 430.47, 'width' => 83.91, 'height' => 171.83],
    10 => ['x' => 377.56, 'y' => 567.3, 'width' => 83.91, 'height' => 80.17],
    11 => ['x' => 377.56, 'y' => 642, 'width' => 80, 'height' => 73],
    12 => ['x' => 79, 'y' => 215, 'width' => 70, 'height' => 90, 'transform' => 'rotate(45, 195, 260)'],
    13 => ['x' => 140, 'y' => 210, 'width' => 100, 'height' => 100, 'transform' => 'rotate(45, 195, 260)']
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
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 700">
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
            <g id="local2" class="local-clickable" style="cursor: pointer">
                <polygon class="cls-1" points="108.11 5.6 200.03 5.6 200.03 94.45 109.32 94.45 108.11 5.6"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 2";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[2]['x'] ?>" 
                      y="<?= $coordenadasLocales[2]['y'] ?>" 
                      width="<?= $coordenadasLocales[2]['width'] ?>" 
                      height="<?= $coordenadasLocales[2]['height'] ?>">
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
            <g id="local3" class="local-clickable" style="cursor: pointer">
                <rect class="cls-1" x="200.03" y="5.6" width="84.77" height="88.85"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 3";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[3]['x'] ?>" 
                      y="<?= $coordenadasLocales[3]['y'] ?>" 
                      width="<?= $coordenadasLocales[3]['width'] ?>" 
                      height="<?= $coordenadasLocales[3]['height'] ?>">
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
            <g id="local4" class="local-clickable" style="cursor: pointer">
                <rect class="cls-1" x="284.79" y="5.6" width="92.77" height="88.85"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 4";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[4]['x'] ?>" 
                      y="<?= $coordenadasLocales[4]['y'] ?>" 
                      width="<?= $coordenadasLocales[4]['width'] ?>" 
                      height="<?= $coordenadasLocales[4]['height'] ?>">
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
            <g id="local5" class="local-clickable" style="cursor: pointer">
                <rect class="cls-1" x="377.56" y="5.6" width="83.91" height="124.94"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 5";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[5]['x'] ?>" 
                      y="<?= $coordenadasLocales[5]['y'] ?>" 
                      width="<?= $coordenadasLocales[5]['width'] ?>" 
                      height="<?= $coordenadasLocales[5]['height'] ?>">
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
            <g id="local6" class="local-clickable" style="cursor: pointer">
                <rect class="cls-1" x="377.56" y="130.53" width="83.91" height="60.6"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 6";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[6]['x'] ?>" 
                      y="<?= $coordenadasLocales[6]['y'] ?>" 
                      width="<?= $coordenadasLocales[6]['width'] ?>" 
                      height="<?= $coordenadasLocales[6]['height'] ?>">
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
            <g id="local7" class="local-clickable" style="cursor: pointer">
            <rect class="cls-1" x="377.56" y="191.13" width="83.91" height="122.89"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 7";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[7]['x'] ?>" 
                      y="<?= $coordenadasLocales[7]['y'] ?>" 
                      width="<?= $coordenadasLocales[7]['width'] ?>" 
                      height="<?= $coordenadasLocales[7]['height'] ?>">
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
            <g id="local8" class="local-clickable" style="cursor: pointer">
                <rect class="cls-1" x="377.56" y="314.02" width="83.91" height="81.45"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 8";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[8]['x'] ?>" 
                      y="<?= $coordenadasLocales[8]['y'] ?>" 
                      width="<?= $coordenadasLocales[8]['width'] ?>" 
                      height="<?= $coordenadasLocales[8]['height'] ?>">
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
            <g id="local9" class="local-clickable" style="cursor: pointer">
                <rect class="cls-1" x="377.56" y="395.47" width="83.91" height="171.83"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 9";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[9]['x'] ?>" 
                      y="<?= $coordenadasLocales[9]['y'] ?>" 
                      width="<?= $coordenadasLocales[9]['width'] ?>" 
                      height="<?= $coordenadasLocales[9]['height'] ?>">
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
            <g id="local10" class="local-clickable" style="cursor: pointer">
                <rect class="cls-1" x="377.56" y="567.3" width="83.91" height="80.17"/>
                <?php
                $conn = OpenCon();
                $sql = "SELECT n.* FROM negocios n WHERE n.NumeroLocal = 10";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $negocio = $result->fetch_assoc();
                    ?>
                    <foreignObject 
                      x="<?= $coordenadasLocales[10]['x'] ?>" 
                      y="<?= $coordenadasLocales[10]['y'] ?>" 
                      width="<?= $coordenadasLocales[10]['width'] ?>" 
                      height="<?= $coordenadasLocales[10]['height'] ?>">
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