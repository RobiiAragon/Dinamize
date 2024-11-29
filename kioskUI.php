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

// Aquí puedes agregar cualquier lógica adicional que necesites

CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk SVG Editor</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fontAwesome/all.min.css">
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
            <li><a href="userInfo.php" data-section="user-info" class="active">User Info</a></li>
                <li><a href="managePlaza.php" data-section="manage-plaza">Manage Plaza</a></li>
                <li><a href="manageLocals.php" data-section="manage-locals">Manage Locals</a></li>
                <li><a href="kioskUI.php" data-section="kiosk-ui">Kiosk UI</a></li>
            </ul>
            <div class="logout-container">
            <a href="backend/logout.php" class="logout-button">
                <i class="fas fa-sign-out-alt logout-icon"></i>
                Logout
            </a> 
        </div>
        </aside>
        <main class="main-content">
            <h1>SVG Editor</h1>
            <div id="svg-editor-container">
                <!-- Aquí se puede agregar el editor SVG -->
                <svg id="svg-editor" width="100%" height="600px" style="border: 1px solid #ccc;"></svg>
            </div>
        </main>
    </div>
    <script src="js/svg-editor.js"></script>
</body>
</html>