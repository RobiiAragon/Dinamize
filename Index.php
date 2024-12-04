<?php
session_start();
include 'backend/db_connection.php';

if (isset($_SESSION['user_id'])) {
    $conn = OpenCon();
    if ($conn === false) {
        die("Error al conectar a la base de datos: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT fotoPerfil FROM infousuarios WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($fotoPerfil);
    $stmt->fetch();
    $_SESSION['fotoPerfil'] = $fotoPerfil;
    $stmt->close();
    CloseCon($conn);
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo (isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true') ? 'dark-mode' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinamize - Digital Kiosk</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
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
            <a href="index.php">
                <img src="img/logo.png" alt="Logo" class="logo">
            </a>
            <h1>Dinamize</h1>
        </div>
        <div class="auth-buttons">
        <?php if(isset($_SESSION['user_id'])): ?>
        <a href="userInfo.php" class="user-profile">
        <?php
        // Verificar si existe fotoPerfil en la sesión
        if (isset($_SESSION['fotoPerfil']) && $_SESSION['fotoPerfil'] !== null) {
            $fotoPerfilSrc = 'data:image/jpeg;base64,' . base64_encode($_SESSION['fotoPerfil']);
            // Depuración: Mostrar el tamaño de la imagen
            echo '<!-- Tamaño de la imagen: ' . strlen($_SESSION['fotoPerfil']) . ' bytes -->';
        } else {
            $fotoPerfilSrc = 'img/noUserPhoto.png';
        }
        ?>
        <img src="<?php echo $fotoPerfilSrc; ?>" 
             alt="Profile Picture" 
             class="header-profile-pic">
    </a>
    <button onclick="location.href='backend/logout.php'" class="logout-btn">Cerrar Sesión</button>
        <?php else: ?>
            <a href="https://linktr.ee/dinamizeenterprise" target="_blank" class="socialMedia">Nuestras Redes</a>
            <button onclick="location.href='login.php'">Login</button>
        <?php endif; ?>
        </div>
    </header>
    <main>
    <div class="anuncio-carrusel-container">
        <div class="anuncio">
            <div class="titGrande">
            <label>Queremos trabajar contigo</label>
        </div>
                <div class="texto">
                    <label>En Dinamize queremos ayudarte a transformar tus espacios y potenciar tu negocio con soluciones innovadoras como la revitalización de plazas y kioscos interactivos.</label><br>
                    <label><strong>¡Contactanos ahora y obtén un 15% de descuento en tu primer servicio!</strong></label>
                </div>
                <button onclick="window.open('https://www.facebook.com/profile.php?id=61567673518071', '_blank')" class="cta-button">Contactanos!</button>            </div>
            <div class="carrusel-division">
            <div class="titServicios">
                <h2>Nuestros Servicios</h2>
            </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <a href="https://dinamize-enterprise.rdi.store/products/c9ed751a-505f-4d0a-a421-1c67b57e56e7/Men-Digital" target="_blank">
                            <img src="img/1.png" alt="Servicio 1">
                        </a>
                    </div>
                    <div class="carousel-item">
                        <a href="https://dinamize-enterprise.rdi.store/products/06308d5b-5b5d-4433-9bb5-8ddeb2627238/Redes-sociales" target="_blank">
                        <img src="img/2.png" alt="Servicio 2">
                        </a>
                    </div>
                    <div class="carousel-item">
                        <a href="https://dinamize-enterprise.rdi.store/products/21dcc8d6-f24e-4505-acf3-d0806180fcc2/Tarjetas-de-presentacin" target="_blank">
                        <img src="img/3.png" alt="Servicio 3">
                        </a>
                    </div>
                    <div class="carousel-item">
                        <a href="https://dinamize-enterprise.rdi.store/products/bba8261a-f087-4515-a8be-c91487c8dd37/Kiosco-Digital" target="_blank">
                        <img src="img/8.png" alt="Servicio 4">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="js/scripts.js"></script>
    <script src="js/darkMode.js"></script>
    <button class="dark-mode-toggle" id="darkModeToggle" title="Cambiar modo oscuro">
    <i class="fas fa-moon"></i>
</button>
</body>
</html>