<?php
session_start();
require_once 'config.php';
require_once 'DbHelper.php';

// Asegurar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

class KioscoController {
    private $conn;
    private $locales = [];
    private $plaza_id;

    public function __construct() {
        $this->conn = DbHelper::getConnection();
        $this->obtenerPlazaId();
        $this->cargarLocales();
    }

    private function obtenerPlazaId() {
        if ($this->conn) {
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT id FROM plazas_comerciales WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $plaza = $result->fetch_assoc();
                    $this->plaza_id = $plaza['id'];
                } else {
                    $this->plaza_id = null;
                }
                $stmt->close();
            }
        }
    }

    private function cargarLocales() {
        if ($this->conn && $this->plaza_id !== null) {
            $sql = "SELECT NumeroLocal, nombre, logo, descripcion, telefono,
                    horarioApertura, horarioCierre, sitioWeb, facebook, instagram
                    FROM negocios WHERE plaza_id = ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $this->plaza_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $this->locales = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            }
        }
    }

    public function getLocales() {
        return $this->locales;
    }

    public function getProfilePicture() {
        return isset($_SESSION['fotoPerfil']) && $_SESSION['fotoPerfil'] !== null 
               ? 'data:image/jpeg;base64,' . base64_encode($_SESSION['fotoPerfil'])
               : 'img/noUserPhoto.png';
    }
}

$coordenadasLocales = [
    1 => ['x' => 3.29, 'y' => 30, 'width' => 100, 'height' => 149],
    2 => ['x' => 108.11, 'y' => 5.6, 'width' => 91.92, 'height' => 88.85],
    3 => ['x' => 200.03, 'y' => 5.6, 'width' => 84.77, 'height' => 88.85],
    4 => ['x' => 284.79, 'y' => 5.6, 'width' => 92.77, 'height' => 88.85],
    5 => ['x' => 377.56, 'y' => 25, 'width' => 83.91, 'height' => 124.94],
    6 => ['x' => 377.56, 'y' => 125, 'width' => 80, 'height' => 60],
    7 => ['x' => 377.56, 'y' => 211.13, 'width' => 83.91, 'height' => 122.89],
    8 => ['x' => 377.56, 'y' => 314.02, 'width' => 83.91, 'height' => 81.45],
    9 => ['x' => 377.56, 'y' => 430.47, 'width' => 83.91, 'height' => 171.83],
    10 => ['x' => 377.56, 'y' => 567.3, 'width' => 83.91, 'height' => 80.17],
    11 => ['x' => 377.56, 'y' => 642, 'width' => 80, 'height' => 73],
    12 => ['x' => 79, 'y' => 215, 'width' => 70, 'height' => 90, 'transform' => 'rotate(45, 195, 260)'],
    13 => ['x' => 140, 'y' => 210, 'width' => 100, 'height' => 100, 'transform' => 'rotate(45, 195, 260)']
];

$svgShapes = [
    1 => '<polygon class="cls-1" points="3.29 5.6 108.11 5.6 110.15 155.21 3.29 155.21 3.29 5.6"/>',
    2 => '<polygon class="cls-1" points="108.11 5.6 200.03 5.6 200.03 94.45 109.32 94.45 108.11 5.6"/>',
    3 => '<rect class="cls-1" x="200.03" y="5.6" width="84.77" height="88.85"/>',
    4 => '<rect class="cls-1" x="284.79" y="5.6" width="92.77" height="88.85"/>',
    5 => '<rect class="cls-1" x="377.56" y="5.6" width="83.91" height="124.94"/>',
    6 => '<rect class="cls-1" x="377.56" y="130.53" width="83.91" height="60.6"/>',
    7 => '<rect class="cls-1" x="377.56" y="191.13" width="83.91" height="122.89"/>',
    8 => '<rect class="cls-1" x="377.56" y="314.02" width="83.91" height="81.45"/>',
    9 => '<rect class="cls-1" x="377.56" y="395.47" width="83.91" height="171.83"/>',
    10 => '<rect class="cls-1" x="377.56" y="567.3" width="83.91" height="80.17"/>',
    11 => '<rect class="cls-1" x="377.56" y="647.47" width="83.91" height="71.74"/>',
    12 => '<polygon class="cls-1" points="85.13 203.21 132.11 252.57 189.05 196.32 140.79 149.34 85.13 203.21"/>',
    13 => '<polygon class="cls-1" points="132.11 252.57 195.43 318.62 258.75 254.28 195.43 191.47 132.11 252.57"/>',
];

$controller = new KioscoController();
$locales = $controller->getLocales();
$localesIndexados = [];
foreach ($locales as $local) {
    $localesIndexados[$local['NumeroLocal']] = $local;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kiosco digital interactivo de Dinamize">
    <title>Dinamize - Digital Kiosk</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/kiosco.css">
    <link rel="stylesheet" href="css/fontAwesome/all.min.css">
    <script>
        // Aplicar la clase dark-mode al cargar la página si está en localStorage
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>
<body>
    <header>
        <div class="logo-container">
            <a href="index.php" aria-label="Ir a inicio">
                <img src="img/logo.png" alt="Logo Dinamize" class="logo">
            </a>
            <h1>Dinamize</h1>
        </div>
        <div class="auth-buttons">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="userInfo.php" class="user-profile" aria-label="Perfil de usuario">
                    <img src="<?= htmlspecialchars($controller->getProfilePicture()) ?>" 
                         alt="Foto de perfil" 
                         class="header-profile-pic">
                </a>
                <button type="button" 
                        onclick="location.href='backend/logout.php'" 
                        class="logout-btn">
                    Cerrar Sesión
                </button>
            <?php else: ?>
                <button type="button" onclick="location.href='login.php'">Login</button>
                <button type="button" onclick="location.href='register.php'">Register</button>
            <?php endif; ?>
        </div>
    </header>

    <main class="main-content">
        <div class="map-container">
            <div class="map" role="grid">
                <div class="svg-container">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" preserveAspectRatio="xMidYMin meet">
                    <g id="piso">
                        <path class="cls-2" d="M.87.5h465.19l.51,725.33h-154.21l-1.02-99.81s-40.43-17.02-52.6-25.02c-11.7-7.69-31.68-27.72-40.85-38.3-10.41-12.02-27.43-39.23-34.21-53.62-5.32-11.29-11.57-35.71-13.79-48-2.39-13.21-3.15-40.21-2.55-53.62.5-11.24,4.59-33.52,7.66-44.34,2.71-9.55,14.81-36.85,14.81-36.85l-45.96-48-42.89-48.51s-32.34-37.21-46.47-46.98c-11.84-8.19-53.62-20.94-53.62-20.94V.5Z"/>
                    </g>
                    <?php
                        foreach ($svgShapes as $i => $shape) {
                            echo '<g id="local' . $i . '" class="local-clickable" style="cursor: pointer">';
                            echo $shape;
                            if (isset($localesIndexados[$i])) {
                                $local = $localesIndexados[$i];
                                ?>
                                <foreignObject 
                                    x="<?= $coordenadasLocales[$i]['x'] ?>" 
                                    y="<?= $coordenadasLocales[$i]['y'] ?>" 
                                    width="<?= $coordenadasLocales[$i]['width'] ?>" 
                                    height="<?= $coordenadasLocales[$i]['height'] ?>"
                                    <?= isset($coordenadasLocales[$i]['transform']) ? 'transform="'.$coordenadasLocales[$i]['transform'].'"' : '' ?>>
                                    <div xmlns="http://www.w3.org/1999/xhtml" class="local" 
                                        id="localDiv<?= htmlspecialchars($local['NumeroLocal']) ?>"
                                        data-nombre="<?= htmlspecialchars($local['nombre']) ?>"
                                        data-logo="data:image/jpeg;base64,<?= base64_encode($local['logo']) ?>"
                                        data-descripcion="<?= htmlspecialchars($local['descripcion']) ?>"
                                        data-telefono="<?= htmlspecialchars($local['telefono']) ?>"
                                        data-horario-apertura="<?= htmlspecialchars($local['horarioApertura']) ?>"
                                        data-horario-cierre="<?= htmlspecialchars($local['horarioCierre']) ?>"
                                        data-sitio-web="<?= htmlspecialchars($local['sitioWeb']) ?>"
                                        data-facebook="<?= htmlspecialchars($local['facebook']) ?>"
                                        data-instagram="<?= htmlspecialchars($local['instagram']) ?>"
                                        role="gridcell"
                                        tabindex="0">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($local['logo']) ?>" 
                                            alt="Logo de <?= htmlspecialchars($local['nombre']) ?>"
                                            loading="lazy">
                                        <p><?= htmlspecialchars($local['nombre']) ?></p>
                                    </div>
                                </foreignObject>
                                <?php
                            }
                            echo '</g>';
                        }
                        ?>
                        <g id="vistas">
    <path class="cls-2" d="M188.67,341.54l113.93,108.92,2.66,162.12s-18.31-10.46-23.96-14.56c-15.22-11.03-42.8-36.71-54.3-51.2-9.53-12-22.87-39.49-29.28-53.2-5.79-12.39-14.88-38.32-17.04-51.7-2.06-12.78-1.39-38.84,0-51.7,1.33-12.25,7.99-48.69,7.99-48.69Z"/>
    <path class="cls-2" d="M.56,164.58s13.37,4.36,17.68,6.2c4.96,2.12,21.52,11.05,26.17,13.79,5.41,3.19,19.28,15.05,19.28,15.05l119.82,127.33s-10.75,18.92-13.62,27.15c-1.98,5.68-5.83,21.12-7.15,26.98-1.39,6.18-1.84,19.85-2.55,26.38-.88,8.04.13,23.94.85,32.51.92,10.93,1.09,22.38,3.74,33.02,2.31,9.25,6.9,25.21,11.23,33.7,5.19,10.18,16.32,31.95,23.49,40.85,7.82,9.7,24.89,31.23,34.21,39.49,6.81,6.03,31.12,26.07,38.98,30.64s33.02,13.28,33.02,13.28v94.87l-182.81.51-46.64-189.68L4.79,217.85.56,164.58Z"/>
  </g>
  <g id="calles">
    <path class="cls-2" d="M82.28,561.05l49.83-24.91h-53.62l-30.81-145.53L8.66,222.49l-5.38-51.68s13.85,4.5,16.74,5.85c5.35,2.51,16.6,9.96,16.6,9.96l16.85,12.26,122.04,127.4s-14.33,24.71-16.6,34.21c-7.12,29.87-5.48,92.09,1.28,122.04,1.99,8.8,6.65,25.07,11.49,32.68,4.65,7.32,13.1,22.33,18.3,29.28s14.67,19.65,20.43,26.21c7.89,9,26.57,27.27,35.57,35.15,6.52,5.71,20.19,16.59,27.83,20.68,5.61,3,23.74,9.19,23.74,9.19v83.49h-163.15v-128.17s-2.1-12.33-4.09-13.79-10.21-2.55-10.21-2.55l-31,14.14-6.83-27.78Z"/>
    <path class="cls-2" d="M85.35,576.15l79.27-38.13-79.27,38.13Z"/>
    <polyline class="cls-2" points="88.13 565.7 94.83 562.38 92.22 560.47 98.98 561.55 95.92 565.7 95.22 562.51"/>
    <polyline class="cls-2" points="105.69 574.35 100.29 576.94 102.65 578.4 97.07 578.15 100.2 573.81 99.65 576.81"/>
  </g>
  <g id="mesas">
    <circle class="cls-2" cx="338.92" cy="150.11" r="11.74"/>
    <circle class="cls-2" cx="337.19" cy="178.78" r="7.97"/>
    <circle class="cls-2" cx="316.57" cy="170.31" r="5.73"/>
    <ellipse class="cls-2" cx="346.06" cy="666.49" rx="11.1" ry="10.78"/>
    <ellipse class="cls-2" cx="334.18" cy="683.34" rx="6.66" ry="6.47"/>
    <ellipse class="cls-2" cx="328.86" cy="659.39" rx="3.79" ry="3.68"/>
    <polygon class="cls-2" points="305.04 445.38 191.65 338.48 200.68 329.96 318.03 440.59 305.04 445.38"/>
    <polygon class="cls-2" points="320.32 439.51 201.74 327.64 210.77 319.12 335.62 432.15 320.32 439.51"/>
    <polygon class="cls-2" points="309.32 614.78 306.02 450.74 318.69 445.66 322.09 614.55 309.32 614.78"/>
    <polygon class="cls-2" points="325.35 614.06 321.33 444.94 336.8 436.65 338.12 613.83 325.35 614.06"/>
    <ellipse class="cls-2" cx="294.3" cy="240.49" rx="12.92" ry="13.26"/>
    <ellipse class="cls-2" cx="317.68" cy="246.31" rx="5.33" ry="5.47"/>
    <ellipse class="cls-2" cx="301.82" cy="265.51" rx="4.72" ry="4.84"/>
    <ellipse class="cls-2" cx="323" cy="338.84" rx="3.86" ry="3.94"/>
    <ellipse class="cls-2" cx="215.07" cy="146.91" rx="12.92" ry="13.26"/>
    <ellipse class="cls-2" cx="190.63" cy="158.2" rx="5.33" ry="5.47"/>
    <ellipse class="cls-2" cx="222.59" cy="171.93" rx="4.72" ry="4.84"/>
  </g>
  <g id="fuente">
    <ellipse class="cls-2" cx="323" cy="338.63" rx="24.54" ry="25.02"/>
    <ellipse class="cls-2" cx="323" cy="338.84" rx="22.04" ry="22.47"/>
    <ellipse class="cls-2" cx="323" cy="338.84" rx="9.99" ry="10.19"/>
  </g>
  <g id="estacionamiento">
    <rect class="cls-2" x="85.61" y="270.95" width="21.19" height="8.17" transform="translate(-58.02 27.83) rotate(-12.68)"/>
    <rect class="cls-2" x="88.01" y="280.86" width="21.19" height="8.17" transform="translate(-60.14 28.59) rotate(-12.68)"/>
    <g>
      <rect class="cls-2" x="90.17" y="290.45" width="21.19" height="8.17" transform="translate(-62.19 29.3) rotate(-12.68)"/>
      <rect class="cls-2" x="92.4" y="300.37" width="21.19" height="8.17" transform="translate(-64.32 30.03) rotate(-12.68)"/>
      <rect class="cls-2" x="94.7" y="310.6" width="21.19" height="8.17" transform="translate(-66.5 30.79) rotate(-12.68)"/>
      <rect class="cls-2" x="96.92" y="320.46" width="21.19" height="8.17" transform="translate(-68.61 31.51) rotate(-12.68)"/>
      <rect class="cls-2" x="98.99" y="330.38" width="21.19" height="8.17" transform="translate(-70.74 32.21) rotate(-12.68)"/>
      <rect class="cls-2" x="101.38" y="340.3" width="21.19" height="8.17" transform="translate(-72.86 32.98) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="103.55" y="349.95" width="21.19" height="8.17" transform="translate(-74.93 33.69) rotate(-12.68)"/>
      <rect class="cls-2" x="105.78" y="359.87" width="21.19" height="8.17" transform="translate(-77.05 34.42) rotate(-12.68)"/>
      <rect class="cls-2" x="108.08" y="370.09" width="21.19" height="8.17" transform="translate(-79.24 35.17) rotate(-12.68)"/>
      <rect class="cls-2" x="110.3" y="379.95" width="21.19" height="8.17" transform="translate(-81.35 35.9) rotate(-12.68)"/>
      <rect class="cls-2" x="112.37" y="389.88" width="21.19" height="8.17" transform="translate(-83.47 36.6) rotate(-12.68)"/>
      <rect class="cls-2" x="114.76" y="399.79" width="21.19" height="8.17" transform="translate(-85.59 37.37) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="103.77" y="349.96" width="21.19" height="8.17" transform="translate(-74.92 33.74) rotate(-12.68)"/>
      <rect class="cls-2" x="106.01" y="359.88" width="21.19" height="8.17" transform="translate(-77.05 34.47) rotate(-12.68)"/>
      <rect class="cls-2" x="108.31" y="370.11" width="21.19" height="8.17" transform="translate(-79.23 35.22) rotate(-12.68)"/>
      <rect class="cls-2" x="110.52" y="379.97" width="21.19" height="8.17" transform="translate(-81.35 35.95) rotate(-12.68)"/>
      <rect class="cls-2" x="112.59" y="389.89" width="21.19" height="8.17" transform="translate(-83.47 36.65) rotate(-12.68)"/>
      <rect class="cls-2" x="114.99" y="399.81" width="21.19" height="8.17" transform="translate(-85.59 37.42) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="117.2" y="409.66" width="21.19" height="8.17" transform="translate(-87.7 38.14) rotate(-12.68)"/>
      <rect class="cls-2" x="119.44" y="419.58" width="21.19" height="8.17" transform="translate(-89.82 38.87) rotate(-12.68)"/>
      <rect class="cls-2" x="121.74" y="429.8" width="21.19" height="8.17" transform="translate(-92.01 39.63) rotate(-12.68)"/>
      <rect class="cls-2" x="123.95" y="439.67" width="21.19" height="8.17" transform="translate(-94.12 40.36) rotate(-12.68)"/>
      <rect class="cls-2" x="126.03" y="449.59" width="21.19" height="8.17" transform="translate(-96.25 41.05) rotate(-12.68)"/>
      <rect class="cls-2" x="128.42" y="459.5" width="21.19" height="8.17" transform="translate(-98.37 41.82) rotate(-12.68)"/>
    </g>
    <rect class="cls-2" x="130.6" y="469.22" width="21.19" height="8.17" transform="translate(-100.44 42.54) rotate(-12.68)"/>
    <rect class="cls-2" x="132.83" y="479.13" width="21.19" height="8.17" transform="translate(-102.57 43.27) rotate(-12.68)"/>
    <rect class="cls-2" x="135.14" y="489.36" width="21.19" height="8.17" transform="translate(-104.76 44.02) rotate(-12.68)"/>
    <rect class="cls-2" x="137.35" y="499.22" width="21.19" height="8.17" transform="translate(-106.87 44.75) rotate(-12.68)"/>
    <g>
      <rect class="cls-2" x="45.44" y="225.98" width="21.19" height="8.17" transform="translate(-49.13 17.91) rotate(-12.68)"/>
      <rect class="cls-2" x="47.67" y="235.9" width="21.19" height="8.17" transform="translate(-51.25 18.64) rotate(-12.68)"/>
      <rect class="cls-2" x="49.97" y="246.12" width="21.19" height="8.17" transform="translate(-53.44 19.4) rotate(-12.68)"/>
      <rect class="cls-2" x="52.19" y="255.98" width="21.19" height="8.17" transform="translate(-55.55 20.12) rotate(-12.68)"/>
      <rect class="cls-2" x="54.26" y="265.91" width="21.19" height="8.17" transform="translate(-57.68 20.82) rotate(-12.68)"/>
      <rect class="cls-2" x="56.65" y="275.82" width="21.19" height="8.17" transform="translate(-59.8 21.59) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="58.81" y="285.41" width="21.19" height="8.17" transform="translate(-61.85 22.29) rotate(-12.68)"/>
      <rect class="cls-2" x="61.04" y="295.33" width="21.19" height="8.17" transform="translate(-63.97 23.03) rotate(-12.68)"/>
      <rect class="cls-2" x="63.34" y="305.55" width="21.19" height="8.17" transform="translate(-66.16 23.78) rotate(-12.68)"/>
      <rect class="cls-2" x="65.56" y="315.42" width="21.19" height="8.17" transform="translate(-68.27 24.51) rotate(-12.68)"/>
      <rect class="cls-2" x="67.63" y="325.34" width="21.19" height="8.17" transform="translate(-70.4 25.2) rotate(-12.68)"/>
      <rect class="cls-2" x="70.02" y="335.26" width="21.19" height="8.17" transform="translate(-72.52 25.97) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="72.19" y="344.91" width="21.19" height="8.17" transform="translate(-74.58 26.68) rotate(-12.68)"/>
      <rect class="cls-2" x="74.43" y="354.82" width="21.19" height="8.17" transform="translate(-76.71 27.41) rotate(-12.68)"/>
      <rect class="cls-2" x="76.73" y="365.05" width="21.19" height="8.17" transform="translate(-78.89 28.17) rotate(-12.68)"/>
      <rect class="cls-2" x="78.95" y="374.91" width="21.19" height="8.17" transform="translate(-81.01 28.9) rotate(-12.68)"/>
      <rect class="cls-2" x="81.02" y="384.83" width="21.19" height="8.17" transform="translate(-83.13 29.59) rotate(-12.68)"/>
      <rect class="cls-2" x="83.41" y="394.75" width="21.19" height="8.17" transform="translate(-85.25 30.36) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="72.42" y="344.92" width="21.19" height="8.17" transform="translate(-74.58 26.73) rotate(-12.68)"/>
      <rect class="cls-2" x="74.65" y="354.84" width="21.19" height="8.17" transform="translate(-76.7 27.46) rotate(-12.68)"/>
      <rect class="cls-2" x="76.95" y="365.06" width="21.19" height="8.17" transform="translate(-78.89 28.22) rotate(-12.68)"/>
      <rect class="cls-2" x="79.17" y="374.93" width="21.19" height="8.17" transform="translate(-81 28.95) rotate(-12.68)"/>
      <rect class="cls-2" x="81.24" y="384.85" width="21.19" height="8.17" transform="translate(-83.13 29.64) rotate(-12.68)"/>
      <rect class="cls-2" x="83.63" y="394.77" width="21.19" height="8.17" transform="translate(-85.25 30.41) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="85.85" y="404.62" width="21.19" height="8.17" transform="translate(-87.36 31.14) rotate(-12.68)"/>
      <rect class="cls-2" x="88.08" y="414.54" width="21.19" height="8.17" transform="translate(-89.48 31.87) rotate(-12.68)"/>
      <rect class="cls-2" x="90.38" y="424.76" width="21.19" height="8.17" transform="translate(-91.67 32.62) rotate(-12.68)"/>
      <rect class="cls-2" x="92.6" y="434.62" width="21.19" height="8.17" transform="translate(-93.78 33.35) rotate(-12.68)"/>
      <rect class="cls-2" x="94.67" y="444.55" width="21.19" height="8.17" transform="translate(-95.91 34.05) rotate(-12.68)"/>
      <rect class="cls-2" x="97.06" y="454.46" width="21.19" height="8.17" transform="translate(-98.02 34.81) rotate(-12.68)"/>
    </g>
    <rect class="cls-2" x="99.25" y="464.17" width="21.19" height="8.17" transform="translate(-100.1 35.53) rotate(-12.68)"/>
    <rect class="cls-2" x="101.48" y="474.09" width="21.19" height="8.17" transform="translate(-102.23 36.26) rotate(-12.68)"/>
    <rect class="cls-2" x="103.78" y="484.32" width="21.19" height="8.17" transform="translate(-104.41 37.02) rotate(-12.68)"/>
    <rect class="cls-2" x="106" y="494.18" width="21.19" height="8.17" transform="translate(-106.52 37.74) rotate(-12.68)"/>
    <rect class="cls-2" x="108.07" y="504.1" width="21.19" height="8.17" transform="translate(-108.65 38.44) rotate(-12.68)"/>
    <g>
      <rect class="cls-2" x="23.13" y="255.62" width="21.19" height="8.17" transform="translate(-56.18 13.74) rotate(-12.68)"/>
      <rect class="cls-2" x="25.36" y="265.54" width="21.19" height="8.17" transform="translate(-58.3 14.47) rotate(-12.68)"/>
      <rect class="cls-2" x="27.66" y="275.76" width="21.19" height="8.17" transform="translate(-60.49 15.22) rotate(-12.68)"/>
      <rect class="cls-2" x="29.88" y="285.62" width="21.19" height="8.17" transform="translate(-62.6 15.95) rotate(-12.68)"/>
      <rect class="cls-2" x="31.95" y="295.55" width="21.19" height="8.17" transform="translate(-64.73 16.65) rotate(-12.68)"/>
      <rect class="cls-2" x="34.35" y="305.46" width="21.19" height="8.17" transform="translate(-66.85 17.41) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="36.5" y="315.05" width="21.19" height="8.17" transform="translate(-68.9 18.12) rotate(-12.68)"/>
      <rect class="cls-2" x="38.73" y="324.97" width="21.19" height="8.17" transform="translate(-71.02 18.85) rotate(-12.68)"/>
      <rect class="cls-2" x="41.03" y="335.19" width="21.19" height="8.17" transform="translate(-73.21 19.61) rotate(-12.68)"/>
      <rect class="cls-2" x="43.25" y="345.06" width="21.19" height="8.17" transform="translate(-75.32 20.33) rotate(-12.68)"/>
      <rect class="cls-2" x="45.32" y="354.98" width="21.19" height="8.17" transform="translate(-77.45 21.03) rotate(-12.68)"/>
      <rect class="cls-2" x="47.72" y="364.9" width="21.19" height="8.17" transform="translate(-79.57 21.8) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="49.89" y="374.55" width="21.19" height="8.17" transform="translate(-81.63 22.51) rotate(-12.68)"/>
      <rect class="cls-2" x="52.12" y="384.46" width="21.19" height="8.17" transform="translate(-83.76 23.24) rotate(-12.68)"/>
      <rect class="cls-2" x="54.42" y="394.69" width="21.19" height="8.17" transform="translate(-85.94 24) rotate(-12.68)"/>
      <rect class="cls-2" x="56.64" y="404.55" width="21.19" height="8.17" transform="translate(-88.05 24.72) rotate(-12.68)"/>
      <rect class="cls-2" x="58.71" y="414.47" width="21.19" height="8.17" transform="translate(-90.18 25.42) rotate(-12.68)"/>
      <rect class="cls-2" x="61.1" y="424.39" width="21.19" height="8.17" transform="translate(-92.3 26.19) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="50.11" y="374.56" width="21.19" height="8.17" transform="translate(-81.63 22.56) rotate(-12.68)"/>
      <rect class="cls-2" x="52.34" y="384.48" width="21.19" height="8.17" transform="translate(-83.75 23.29) rotate(-12.68)"/>
      <rect class="cls-2" x="54.64" y="394.7" width="21.19" height="8.17" transform="translate(-85.94 24.05) rotate(-12.68)"/>
      <rect class="cls-2" x="56.86" y="404.57" width="21.19" height="8.17" transform="translate(-88.05 24.77) rotate(-12.68)"/>
      <rect class="cls-2" x="58.93" y="414.49" width="21.19" height="8.17" transform="translate(-90.18 25.47) rotate(-12.68)"/>
      <rect class="cls-2" x="61.33" y="424.41" width="21.19" height="8.17" transform="translate(-92.3 26.24) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="63.54" y="434.26" width="21.19" height="8.17" transform="translate(-94.41 26.96) rotate(-12.68)"/>
      <rect class="cls-2" x="65.77" y="444.18" width="21.19" height="8.17" transform="translate(-96.53 27.69) rotate(-12.68)"/>
      <rect class="cls-2" x="68.07" y="454.4" width="21.19" height="8.17" transform="translate(-98.72 28.45) rotate(-12.68)"/>
      <rect class="cls-2" x="70.29" y="464.26" width="21.19" height="8.17" transform="translate(-100.83 29.18) rotate(-12.68)"/>
      <rect class="cls-2" x="72.36" y="474.19" width="21.19" height="8.17" transform="translate(-102.96 29.87) rotate(-12.68)"/>
      <rect class="cls-2" x="74.76" y="484.1" width="21.19" height="8.17" transform="translate(-105.07 30.64) rotate(-12.68)"/>
    </g>
    <rect class="cls-2" x="76.94" y="493.81" width="21.19" height="8.17" transform="translate(-107.15 31.36) rotate(-12.68)"/>
    <rect class="cls-2" x="79.17" y="503.73" width="21.19" height="8.17" transform="translate(-109.28 32.09) rotate(-12.68)"/>
    <rect class="cls-2" x="81.47" y="513.96" width="21.19" height="8.17" transform="translate(-111.46 32.84) rotate(-12.68)"/>
    <g>
      <rect class="cls-2" x="9.61" y="196.22" width="21.19" height="8.17" transform="translate(-43.47 9.32) rotate(-12.68)"/>
      <rect class="cls-2" x="11.84" y="206.14" width="21.19" height="8.17" transform="translate(-45.6 10.05) rotate(-12.68)"/>
      <rect class="cls-2" x="14.14" y="216.37" width="21.19" height="8.17" transform="translate(-47.79 10.81) rotate(-12.68)"/>
      <rect class="cls-2" x="16.36" y="226.23" width="21.19" height="8.17" transform="translate(-49.9 11.53) rotate(-12.68)"/>
      <rect class="cls-2" x="18.43" y="236.15" width="21.19" height="8.17" transform="translate(-52.02 12.23) rotate(-12.68)"/>
      <rect class="cls-2" x="20.82" y="246.07" width="21.19" height="8.17" transform="translate(-54.14 13) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-2" x="142.48" y="695.87" width="21.19" height="8.17" transform="translate(-545.18 860.62) rotate(-90.62)"/>
      <rect class="cls-2" x="152.96" y="695.75" width="21.19" height="8.17" transform="translate(-534.47 870.99) rotate(-90.62)"/>
      <rect class="cls-2" x="163.06" y="695.64" width="21.19" height="8.17" transform="translate(-524.14 880.99) rotate(-90.62)"/>
      <rect class="cls-2" x="173.2" y="695.69" width="21.19" height="8.17" transform="translate(-513.94 891.17) rotate(-90.62)"/>
      <rect class="cls-2" x="183.4" y="695.42" width="21.19" height="8.17" transform="translate(-503.36 901.09) rotate(-90.62)"/>
      <g>
        <rect class="cls-2" x="193.92" y="695.31" width="21.19" height="8.17" transform="translate(-492.62 911.5) rotate(-90.62)"/>
        <rect class="cls-2" x="204.08" y="695.2" width="21.19" height="8.17" transform="translate(-482.23 921.55) rotate(-90.62)"/>
        <rect class="cls-2" x="214.56" y="695.08" width="21.19" height="8.17" transform="translate(-471.52 931.91) rotate(-90.62)"/>
        <rect class="cls-2" x="224.67" y="694.97" width="21.19" height="8.17" transform="translate(-461.2 941.91) rotate(-90.62)"/>
        <rect class="cls-2" x="234.81" y="695.02" width="21.19" height="8.17" transform="translate(-451 952.09) rotate(-90.62)"/>
        <rect class="cls-2" x="245" y="694.75" width="21.19" height="8.17" transform="translate(-440.42 962.02) rotate(-90.62)"/>
      </g>
      <rect class="cls-2" x="255.39" y="694.8" width="21.19" height="8.17" transform="translate(-429.97 972.45) rotate(-90.62)"/>
      <rect class="cls-2" x="265.55" y="694.69" width="21.19" height="8.17" transform="translate(-419.58 982.5) rotate(-90.62)"/>
    </g>
    <g>
      <rect class="cls-2" x="142.21" y="664.87" width="21.19" height="8.17" transform="translate(-514.45 829.02) rotate(-90.62)"/>
      <rect class="cls-2" x="152.69" y="664.76" width="21.19" height="8.17" transform="translate(-503.75 839.39) rotate(-90.62)"/>
      <rect class="cls-2" x="162.8" y="664.65" width="21.19" height="8.17" transform="translate(-493.42 849.38) rotate(-90.62)"/>
      <rect class="cls-2" x="172.93" y="664.7" width="21.19" height="8.17" transform="translate(-483.22 859.57) rotate(-90.62)"/>
      <rect class="cls-2" x="183.13" y="664.43" width="21.19" height="8.17" transform="translate(-472.64 869.49) rotate(-90.62)"/>
      <g>
        <rect class="cls-2" x="193.65" y="664.31" width="21.19" height="8.17" transform="translate(-461.9 879.89) rotate(-90.62)"/>
        <rect class="cls-2" x="203.81" y="664.2" width="21.19" height="8.17" transform="translate(-451.51 889.95) rotate(-90.62)"/>
        <rect class="cls-2" x="214.29" y="664.09" width="21.19" height="8.17" transform="translate(-440.8 900.31) rotate(-90.62)"/>
        <rect class="cls-2" x="224.4" y="663.98" width="21.19" height="8.17" transform="translate(-430.47 910.31) rotate(-90.62)"/>
        <rect class="cls-2" x="234.54" y="664.03" width="21.19" height="8.17" transform="translate(-420.28 920.49) rotate(-90.62)"/>
        <rect class="cls-2" x="244.73" y="663.76" width="21.19" height="8.17" transform="translate(-409.7 930.42) rotate(-90.62)"/>
      </g>
      <rect class="cls-2" x="255.12" y="663.8" width="21.19" height="8.17" transform="translate(-399.25 940.85) rotate(-90.62)"/>
      <rect class="cls-2" x="265.28" y="663.69" width="21.19" height="8.17" transform="translate(-388.86 950.9) rotate(-90.62)"/>
    </g>
    <rect class="cls-2" x="162.35" y="636.8" width="21.19" height="8.17" transform="translate(-466.03 820.79) rotate(-90.62)"/>
    <rect class="cls-2" x="172.48" y="636.85" width="21.19" height="8.17" transform="translate(-455.83 830.97) rotate(-90.62)"/>
    <rect class="cls-2" x="182.68" y="636.58" width="21.19" height="8.17" transform="translate(-445.25 840.9) rotate(-90.62)"/>
    <g>
      <rect class="cls-2" x="193.2" y="636.47" width="21.19" height="8.17" transform="translate(-434.51 851.3) rotate(-90.62)"/>
      <rect class="cls-2" x="203.37" y="636.36" width="21.19" height="8.17" transform="translate(-424.12 861.35) rotate(-90.62)"/>
      <rect class="cls-2" x="213.84" y="636.24" width="21.19" height="8.17" transform="translate(-413.41 871.72) rotate(-90.62)"/>
      <rect class="cls-2" x="223.95" y="636.13" width="21.19" height="8.17" transform="translate(-403.08 881.71) rotate(-90.62)"/>
      <rect class="cls-2" x="234.09" y="636.18" width="21.19" height="8.17" transform="translate(-392.88 891.9) rotate(-90.62)"/>
      <rect class="cls-2" x="244.29" y="635.91" width="21.19" height="8.17" transform="translate(-382.31 901.82) rotate(-90.62)"/>
    </g>
    <rect class="cls-2" x="162.82" y="608.69" width="21.19" height="8.17" transform="translate(-437.44 792.84) rotate(-90.62)"/>
    <rect class="cls-2" x="172.98" y="608.58" width="21.19" height="8.17" transform="translate(-427.05 802.89) rotate(-90.62)"/>
    <rect class="cls-2" x="183.46" y="608.46" width="21.19" height="8.17" transform="translate(-416.35 813.25) rotate(-90.62)"/>
    <rect class="cls-2" x="193.57" y="608.35" width="21.19" height="8.17" transform="translate(-406.02 823.25) rotate(-90.62)"/>
    <rect class="cls-2" x="203.71" y="608.4" width="21.19" height="8.17" transform="translate(-395.82 833.44) rotate(-90.62)"/>
    <rect class="cls-2" x="213.91" y="608.13" width="21.19" height="8.17" transform="translate(-385.24 843.36) rotate(-90.62)"/>
    <rect class="cls-2" x="161.43" y="582.53" width="21.19" height="8.17" transform="translate(-412.69 765.01) rotate(-90.62)"/>
    <rect class="cls-2" x="171.54" y="582.42" width="21.19" height="8.17" transform="translate(-402.36 775.01) rotate(-90.62)"/>
    <rect class="cls-2" x="181.67" y="582.47" width="21.19" height="8.17" transform="translate(-392.16 785.19) rotate(-90.62)"/>
    <rect class="cls-2" x="191.87" y="582.2" width="21.19" height="8.17" transform="translate(-381.59 795.12) rotate(-90.62)"/>
    <rect class="cls-2" x="151.71" y="637.39" width="21.19" height="8.17" transform="translate(-477.37 810.75) rotate(-90.62)"/>
    <rect class="cls-2" x="152.18" y="609.28" width="21.19" height="8.17" transform="translate(-448.78 782.79) rotate(-90.62)"/>
    <rect class="cls-2" x="150.79" y="582.81" width="21.19" height="8.17" transform="translate(-423.72 754.65) rotate(-90.62)"/>
  </g>
                    </svg>
                </div>
            </div>
        </div>
    </main>

    <button class="dark-mode-toggle" 
            id="darkModeToggle" 
            type="button"
            aria-label="Cambiar modo oscuro">
        <i class="fas fa-moon" aria-hidden="true"></i>
    </button>

    <script src="js/scripts.js" defer></script>
    <script src="js/darkMode.js" defer></script>
    <script src="js/kiosco.js" defer></script>
</body>
</html>
<?php DbHelper::closeConnection(); ?>