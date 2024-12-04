<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dinamize</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/loginRegister.css">
    <link rel="stylesheet" href="css/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="css/fontAwesome/all.min.css">
    <script>
        // Aplicar la clase dark-mode al cargar la página si está en localStorage
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>

<body>
    <?php session_start(); ?>
    <header>
        <div class="logo-container">
            <a href="index.php">
                <img src="img/logo.png" alt="Logo" class="logo">
            </a>
            <h1>Dinamize</h1>
        </div>
        <div class="auth-buttons">
            <button onclick="location.href='login.php'">Login</button>
        </div>
    </header>
    <div class="container">
        <div class="register-container">
            <h2>Registro</h2>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error-message">'.htmlspecialchars($_SESSION['error']).'</p>';
                unset($_SESSION['error']);
            }

            if (isset($_SESSION['success'])) {
                echo '<p class="success-message">'.htmlspecialchars($_SESSION['success']).'</p>';
                unset($_SESSION['success']);
            }
            ?>

            <form action="backend/register_process.php" method="POST">
                <div class="form-group">
                    <label for="Nombres">Nombres</label>
                    <input type="text" id="Nombres" name="Nombres" required>
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" required>
                </div>
                <div class="form-group">
                    <label for="birthdate">Fecha de<br>Nacimiento</label>
                    <input type="text" id="birthdate" name="birthdate" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo<br>Electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="activation_key">Clave de activación
                    </label>
                    <input type="text" id="activation_key" name="activation_key" required maxlength="8">
                        <button type="button" class="pasteClipboard" onclick="pasteClipboard('activation_key')" title="Pegar desde el portapapeles">
                            📋
                        </button>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password" onclick="togglePassword('password')">👀</span>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">👀</span>
                </div>
                <button type="submit">Registrarse</button>
            </form>
        </div>
    </div>
    <script>
        function togglePassword(id) {
            var input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }

        async function pasteClipboard(id) {
            try {
                const text = await navigator.clipboard.readText();
                document.getElementById(id).value = text.slice(0, 8);
            } catch (err) {
                alert('No se pudo pegar el contenido del portapapeles.');
            }
        }
    </script>
    <script src="libs/flatpickr/flatpickr.min.js"></script>
    <script>
        flatpickr("#birthdate", {
            dateFormat: "Y-m-d"
        });
    </script>
    <script src="js/darkMode.js"></script>
    <button class="dark-mode-toggle" id="darkModeToggle" title="Cambiar modo oscuro">
        <i class="fas fa-moon"></i>
    </button>
</body>
</html>