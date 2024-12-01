<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dinamize</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/loginRegister.css">
    <link rel="stylesheet" href="css/fontAwesome/all.min.css">
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
            <button onclick="location.href='register.php'">Registrarse</button>
        </div>
    </header>
    <div class="container">
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']);
            }
            ?>
            <form action="backend/login_process.php" method="POST">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password" onclick="togglePassword('password')">👀</span>
                </div>
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Dinamize. All rights reserved.</p>
    </footer>
    <script>
        function togglePassword(id) {
            var input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }
    </script>
    <script src="js/darkMode.js"></script>
    <button class="dark-mode-toggle" id="darkModeToggle" title="Cambiar modo oscuro">
    <i class="fas fa-moon"></i>
</button>
</body>
</html>