<?php
// kiosco.php
session_start();
require_once 'config.php';
require_once 'DbHelper.php';

class KioscoController {
    private $conn;
    private $locales = [];

    public function __construct() {
        $this->conn = DbHelper::getConnection();
        $this->cargarLocales();
    }

    private function cargarLocales() {
        if ($this->conn) {
            $sql = "SELECT NumeroLocal, nombre, logo, descripcion, telefono, 
                    horarioApertura, horarioCierre, sitioWeb, 
                    facebook, instagram 
                    FROM negocios";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
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

    public function calcularGrid() {
        $totalLocales = count($this->locales);
        return [
            'columnas' => ceil(sqrt($totalLocales)),
            'total' => $totalLocales
        ];
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

$controller = new KioscoController();
$grid = $controller->calcularGrid();
$locales = $controller->getLocales();
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
                        <g id="local1">
                            <polygon class="cls-1" points="3.29 5.6 108.11 5.6 110.15 155.21 3.29 155.21 3.29 5.6"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 1): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local2">
                            <polygon class="cls-1" points="108.11 5.6 200.03 5.6 200.03 94.45 109.32 94.45 108.11 5.6"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 2): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local3">
                            <rect class="cls-1" x="200.03" y="5.6" width="84.77" height="88.85"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 3): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local4">
                            <rect class="cls-1" x="284.79" y="5.6" width="92.77" height="88.85"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 4): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local5">
                            <rect class="cls-1" x="377.56" y="5.6" width="83.91" height="124.94"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 5): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local6">
                            <rect class="cls-1" x="377.56" y="130.53" width="83.91" height="60.6"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 6): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local7">
                            <rect class="cls-1" x="377.56" y="191.13" width="83.91" height="122.89"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 7): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local8">
                            <rect class="cls-1" x="377.56" y="314.02" width="83.91" height="81.45"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 8): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local9">
                            <rect class="cls-1" x="377.56" y="395.47" width="83.91" height="171.83"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 9): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local10">
                            <rect class="cls-1" x="377.56" y="567.3" width="83.91" height="80.17"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 10): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local11">
                            <rect class="cls-1" x="377.56" y="647.47" width="83.91" height="71.74"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 11): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>">
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local12">
                            <polygon class="cls-1" points="85.13 203.21 132.11 252.57 189.05 196.32 140.79 149.34 85.13 203.21"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 12): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>"
                                        <?php if (isset($coordenadasLocales[$local['NumeroLocal']]['transform'])): ?>
                                            transform="<?= $coordenadasLocales[$local['NumeroLocal']]['transform'] ?>"
                                        <?php endif; ?>>
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
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </g>
                        <g id="local13">
                            <polygon class="cls-1" points="132.11 252.57 195.43 318.62 258.75 254.28 195.43 191.47 132.11 252.57"/>
                            <?php foreach ($locales as $local): ?>
                                <?php if ($local['NumeroLocal'] == 13): ?>
                                    <foreignObject 
                                        x="<?= $coordenadasLocales[$local['NumeroLocal']]['x'] ?>" 
                                        y="<?= $coordenadasLocales[$local['NumeroLocal']]['y'] ?>" 
                                        width="<?= $coordenadasLocales[$local['NumeroLocal']]['width'] ?>" 
                                        height="<?= $coordenadasLocales[$local['NumeroLocal']]['height'] ?>"
                                        <?php if (isset($coordenadasLocales[$local['NumeroLocal']]['transform'])): ?>
                                            transform="<?= $coordenadasLocales[$local['NumeroLocal']]['transform'] ?>"
                                        <?php endif; ?>>
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
                                <?php endif; ?>
                            <?php endforeach; ?>
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