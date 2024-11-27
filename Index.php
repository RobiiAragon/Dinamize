<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinamize - Digital Kiosk</title>
    <link rel="stylesheet" href="css/styles.css">
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
        <a href="home.php" class="user-profile">
        <?php
        // Verificar si existe fotoPerfil en la sesión
        if (isset($_SESSION['fotoPerfil']) && $_SESSION['fotoPerfil'] !== null) {
            $fotoPerfilSrc = 'data:image/jpeg;base64,' . base64_encode($_SESSION['fotoPerfil']);
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
    <button onclick="location.href='login.php'">Login</button>
    <button onclick="location.href='register.php'">Register</button>
<?php endif; ?>
        </div>
    </header>
    <div class="container">
        <section class="hero">
            <h1>Welcome to Dinamize</h1>
            <p>Your digital kiosk solution for plazas and malls</p>
        </section>
        <section class="features">
            <div class="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <h2>Feature 1</h2>
                        <p>Description of feature 1.</p>
                    </div>
                    <div class="carousel-item">
                        <h2>Feature 2</h2>
                        <p>Description of feature 2.</p>
                    </div>
                    <div class="carousel-item">
                        <h2>Feature 3</h2>
                        <p>Description of feature 3.</p>
                    </div>
                </div>
                <button class="carousel-control prev" onclick="prevSlide()">&#10094;</button>
                <button class="carousel-control next" onclick="nextSlide()">&#10095;</button>
            </div>
        </section>
    </div>
    <footer>
        <p>&copy; 2024 Dinamize. All rights reserved.</p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>