<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dinamize</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/loginRegister.css">
    <link rel="stylesheet" href="css/flatpickr/flatpickr.min.css">
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
            <button onclick="location.href='login.php'">Login</button>
        </div>
    </header>
    <div class="container">
        <div class="register-container">
            <h2>Registro</h2>
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
    <script src="libs/flatpickr/flatpickr.min.js"></script>
    <script>
        flatpickr("#birthdate", {
            dateFormat: "Y-m-d"
        });
    </script>
</body>
</html>