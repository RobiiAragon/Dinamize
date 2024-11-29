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
    <script src="js/jquery.min.js"></script>
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
    <h1>SVG Editor</h1>
    <div id="svg-editor-container">
        <svg id="svg-editor" viewBox="0 0 553.1 727.11" preserveAspectRatio="xMidYMid meet">
            <defs>
    <style>
      .cls-1 {
        fill: none;
        stroke: #1d1d1b;
        stroke-miterlimit: 10;
      }
    </style>
  </defs>
  <g id="piso">
    <path class="cls-1" d="M18.68.77h465.19l.51,725.33h-154.21l-1.02-99.81s-40.43-17.02-52.6-25.02-31.68-27.72-40.85-38.3c-10.41-12.02-27.43-39.23-34.21-53.62-5.32-11.29-11.57-35.71-13.79-48-2.39-13.21-3.15-40.21-2.55-53.62.5-11.24,4.59-33.52,7.66-44.34,2.71-9.55,14.81-36.85,14.81-36.85l-45.96-48-42.89-48.51s-32.34-37.21-46.47-46.98c-11.84-8.19-53.62-20.94-53.62-20.94V.77Z"/>
  </g>
  <g id="local1">
    <polygon class="cls-1" points="21.09 5.86 125.91 5.86 127.96 155.48 21.09 155.48 21.09 5.86"/>
  </g>
  <g id="local2">
    <polygon class="cls-1" points="125.91 5.86 217.83 5.86 217.83 94.71 127.13 94.71 125.91 5.86"/>
  </g>
  <g id="local3">
    <rect class="cls-1" x="217.83" y="5.86" width="84.77" height="88.85"/>
  </g>
  <g id="local4">
    <rect class="cls-1" x="302.6" y="5.86" width="92.77" height="88.85"/>
  </g>
  <g id="local5">
    <rect class="cls-1" x="395.36" y="5.86" width="83.91" height="124.94"/>
  </g>
  <g id="local6">
    <rect class="cls-1" x="395.36" y="130.8" width="83.91" height="60.6"/>
  </g>
  <g id="local7">
    <rect class="cls-1" x="395.36" y="191.39" width="83.91" height="122.89"/>
  </g>
  <g id="local8">
    <rect class="cls-1" x="395.36" y="314.29" width="83.91" height="81.45"/>
  </g>
  <g id="local9">
    <rect class="cls-1" x="395.36" y="395.74" width="83.91" height="171.83"/>
  </g>
  <g id="local10">
    <rect class="cls-1" x="395.36" y="567.57" width="83.91" height="80.17"/>
  </g>
  <g id="local11">
    <rect class="cls-1" x="395.36" y="647.74" width="83.91" height="71.74"/>
  </g>
  <g id="local12">
    <polygon class="cls-1" points="102.94 203.48 149.91 252.84 206.85 196.59 158.6 149.61 102.94 203.48"/>
  </g>
  <g id="local13">
    <polygon class="cls-1" points="149.91 252.84 213.23 318.88 276.55 254.54 213.23 191.74 149.91 252.84"/>
  </g>
  <g id="vistas">
    <path class="cls-1" d="M206.47,341.81l113.93,108.92,2.66,162.12s-18.31-10.46-23.96-14.56c-15.22-11.03-42.8-36.71-54.3-51.2-9.53-12-22.87-39.49-29.28-53.2-5.79-12.39-14.88-38.32-17.04-51.7-2.06-12.78-1.39-38.84,0-51.7,1.33-12.25,7.99-48.69,7.99-48.69Z"/>
    <path class="cls-1" d="M18.36,164.85s13.37,4.36,17.68,6.2c4.96,2.12,21.52,11.05,26.17,13.79,5.41,3.19,19.28,15.05,19.28,15.05l119.82,127.33s-10.75,18.92-13.62,27.15c-1.98,5.68-5.83,21.12-7.15,26.98-1.39,6.18-1.84,19.85-2.55,26.38-.88,8.04.13,23.94.85,32.51.92,10.93,1.09,22.38,3.74,33.02,2.31,9.25,6.9,25.21,11.23,33.7,5.19,10.18,16.32,31.95,23.49,40.85,7.82,9.7,24.89,31.23,34.21,39.49,6.81,6.03,31.12,26.07,38.98,30.64s33.02,13.28,33.02,13.28v94.87l-182.81.51-46.64-189.68L22.6,218.12l-4.23-53.27Z"/>
  </g>
  <g id="calles">
    <path class="cls-1" d="M100.08,561.32l49.83-24.91h-53.62l-30.81-145.53L26.47,222.76l-5.38-51.68s13.85,4.5,16.74,5.85c5.35,2.51,16.6,9.96,16.6,9.96l16.85,12.26,122.04,127.4s-14.33,24.71-16.6,34.21c-7.12,29.87-5.48,92.09,1.28,122.04,1.99,8.8,6.65,25.07,11.49,32.68,4.65,7.32,13.1,22.33,18.3,29.28s14.67,19.65,20.43,26.21c7.89,9,26.57,27.27,35.57,35.15,6.52,5.71,20.19,16.59,27.83,20.68,5.61,3,23.74,9.19,23.74,9.19v83.49h-163.15v-128.17s-2.1-12.33-4.09-13.79-10.21-2.55-10.21-2.55l-31,14.14-6.83-27.78Z"/>
    <path class="cls-1" d="M103.16,576.42l79.27-38.13-79.27,38.13Z"/>
    <polyline class="cls-1" points="105.94 565.97 112.64 562.65 110.02 560.74 116.79 561.82 113.72 565.97 113.02 562.78"/>
    <polyline class="cls-1" points="123.49 574.62 118.1 577.2 120.46 578.67 114.87 578.42 118 574.08 117.46 577.08"/>
  </g>
  <g id="mesas">
    <circle class="cls-1" cx="356.72" cy="150.37" r="11.74"/>
    <circle class="cls-1" cx="354.99" cy="179.05" r="7.97"/>
    <circle class="cls-1" cx="334.37" cy="170.58" r="5.73"/>
    <ellipse class="cls-1" cx="363.86" cy="666.76" rx="11.1" ry="10.78"/>
    <ellipse class="cls-1" cx="351.98" cy="683.61" rx="6.66" ry="6.47"/>
    <ellipse class="cls-1" cx="346.66" cy="659.66" rx="3.79" ry="3.68"/>
    <polygon class="cls-1" points="322.85 445.65 209.45 338.74 218.49 330.23 335.84 440.86 322.85 445.65"/>
    <polygon class="cls-1" points="338.13 439.78 219.54 327.9 228.58 319.39 353.42 432.42 338.13 439.78"/>
    <polygon class="cls-1" points="327.12 615.05 323.82 451.01 336.5 445.93 339.9 614.82 327.12 615.05"/>
    <polygon class="cls-1" points="343.15 614.32 339.13 445.2 354.6 436.92 355.92 614.09 343.15 614.32"/>
    <ellipse class="cls-1" cx="312.1" cy="240.76" rx="12.92" ry="13.26"/>
    <ellipse class="cls-1" cx="335.48" cy="246.58" rx="5.33" ry="5.47"/>
    <ellipse class="cls-1" cx="319.62" cy="265.77" rx="4.72" ry="4.84"/>
    <ellipse class="cls-1" cx="340.81" cy="339.11" rx="3.86" ry="3.94"/>
    <ellipse class="cls-1" cx="232.87" cy="147.18" rx="12.92" ry="13.26"/>
    <ellipse class="cls-1" cx="208.43" cy="158.47" rx="5.33" ry="5.47"/>
    <ellipse class="cls-1" cx="240.39" cy="172.2" rx="4.72" ry="4.84"/>
  </g>
  <g id="fuente">
    <ellipse class="cls-1" cx="340.81" cy="338.9" rx="24.54" ry="25.02"/>
    <ellipse class="cls-1" cx="340.81" cy="339.11" rx="22.04" ry="22.47"/>
    <ellipse class="cls-1" cx="340.81" cy="339.11" rx="9.99" ry="10.19"/>
  </g>
  <g id="estacionamiento">
    <rect class="cls-1" x="103.42" y="271.22" width="21.19" height="8.17" transform="translate(-57.65 31.74) rotate(-12.68)"/>
    <rect class="cls-1" x="105.81" y="281.13" width="21.19" height="8.17" transform="translate(-59.77 32.51) rotate(-12.68)"/>
    <g>
      <rect class="cls-1" x="107.97" y="290.72" width="21.19" height="8.17" transform="translate(-61.82 33.21) rotate(-12.68)"/>
      <rect class="cls-1" x="110.2" y="300.64" width="21.19" height="8.17" transform="translate(-63.94 33.95) rotate(-12.68)"/>
      <rect class="cls-1" x="112.5" y="310.86" width="21.19" height="8.17" transform="translate(-66.13 34.7) rotate(-12.68)"/>
      <rect class="cls-1" x="114.72" y="320.73" width="21.19" height="8.17" transform="translate(-68.24 35.43) rotate(-12.68)"/>
      <rect class="cls-1" x="116.79" y="330.65" width="21.19" height="8.17" transform="translate(-70.37 36.12) rotate(-12.68)"/>
      <rect class="cls-1" x="119.18" y="340.56" width="21.19" height="8.17" transform="translate(-72.48 36.89) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="121.35" y="350.21" width="21.19" height="8.17" transform="translate(-74.55 37.6) rotate(-12.68)"/>
      <rect class="cls-1" x="123.58" y="360.13" width="21.19" height="8.17" transform="translate(-76.67 38.33) rotate(-12.68)"/>
      <rect class="cls-1" x="125.89" y="370.36" width="21.19" height="8.17" transform="translate(-78.86 39.09) rotate(-12.68)"/>
      <rect class="cls-1" x="128.1" y="380.22" width="21.19" height="8.17" transform="translate(-80.97 39.82) rotate(-12.68)"/>
      <rect class="cls-1" x="130.17" y="390.14" width="21.19" height="8.17" transform="translate(-83.1 40.51) rotate(-12.68)"/>
      <rect class="cls-1" x="132.57" y="400.06" width="21.19" height="8.17" transform="translate(-85.22 41.28) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="121.58" y="350.23" width="21.19" height="8.17" transform="translate(-74.55 37.65) rotate(-12.68)"/>
      <rect class="cls-1" x="123.81" y="360.15" width="21.19" height="8.17" transform="translate(-76.67 38.38) rotate(-12.68)"/>
      <rect class="cls-1" x="126.11" y="370.37" width="21.19" height="8.17" transform="translate(-78.86 39.14) rotate(-12.68)"/>
      <rect class="cls-1" x="128.33" y="380.24" width="21.19" height="8.17" transform="translate(-80.97 39.87) rotate(-12.68)"/>
      <rect class="cls-1" x="130.4" y="390.16" width="21.19" height="8.17" transform="translate(-83.1 40.56) rotate(-12.68)"/>
      <rect class="cls-1" x="132.79" y="400.08" width="21.19" height="8.17" transform="translate(-85.22 41.33) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="135.01" y="409.93" width="21.19" height="8.17" transform="translate(-87.32 42.06) rotate(-12.68)"/>
      <rect class="cls-1" x="137.24" y="419.85" width="21.19" height="8.17" transform="translate(-89.45 42.79) rotate(-12.68)"/>
      <rect class="cls-1" x="139.54" y="430.07" width="21.19" height="8.17" transform="translate(-91.63 43.54) rotate(-12.68)"/>
      <rect class="cls-1" x="141.76" y="439.93" width="21.19" height="8.17" transform="translate(-93.75 44.27) rotate(-12.68)"/>
      <rect class="cls-1" x="143.83" y="449.86" width="21.19" height="8.17" transform="translate(-95.87 44.97) rotate(-12.68)"/>
      <rect class="cls-1" x="146.22" y="459.77" width="21.19" height="8.17" transform="translate(-97.99 45.73) rotate(-12.68)"/>
    </g>
    <rect class="cls-1" x="148.41" y="469.48" width="21.19" height="8.17" transform="translate(-100.07 46.45) rotate(-12.68)"/>
    <rect class="cls-1" x="150.64" y="479.4" width="21.19" height="8.17" transform="translate(-102.19 47.18) rotate(-12.68)"/>
    <rect class="cls-1" x="152.94" y="489.63" width="21.19" height="8.17" transform="translate(-104.38 47.94) rotate(-12.68)"/>
    <rect class="cls-1" x="155.16" y="499.49" width="21.19" height="8.17" transform="translate(-106.49 48.66) rotate(-12.68)"/>
    <g>
      <rect class="cls-1" x="63.24" y="226.25" width="21.19" height="8.17" transform="translate(-48.76 21.82) rotate(-12.68)"/>
      <rect class="cls-1" x="65.47" y="236.16" width="21.19" height="8.17" transform="translate(-50.88 22.56) rotate(-12.68)"/>
      <rect class="cls-1" x="67.77" y="246.39" width="21.19" height="8.17" transform="translate(-53.07 23.31) rotate(-12.68)"/>
      <rect class="cls-1" x="69.99" y="256.25" width="21.19" height="8.17" transform="translate(-55.18 24.04) rotate(-12.68)"/>
      <rect class="cls-1" x="72.06" y="266.17" width="21.19" height="8.17" transform="translate(-57.31 24.73) rotate(-12.68)"/>
      <rect class="cls-1" x="74.45" y="276.09" width="21.19" height="8.17" transform="translate(-59.42 25.5) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="76.61" y="285.68" width="21.19" height="8.17" transform="translate(-61.48 26.21) rotate(-12.68)"/>
      <rect class="cls-1" x="78.84" y="295.6" width="21.19" height="8.17" transform="translate(-63.6 26.94) rotate(-12.68)"/>
      <rect class="cls-1" x="81.14" y="305.82" width="21.19" height="8.17" transform="translate(-65.79 27.69) rotate(-12.68)"/>
      <rect class="cls-1" x="83.36" y="315.68" width="21.19" height="8.17" transform="translate(-67.9 28.42) rotate(-12.68)"/>
      <rect class="cls-1" x="85.43" y="325.61" width="21.19" height="8.17" transform="translate(-70.02 29.12) rotate(-12.68)"/>
      <rect class="cls-1" x="87.83" y="335.52" width="21.19" height="8.17" transform="translate(-72.14 29.89) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="90" y="345.17" width="21.19" height="8.17" transform="translate(-74.21 30.6) rotate(-12.68)"/>
      <rect class="cls-1" x="92.23" y="355.09" width="21.19" height="8.17" transform="translate(-76.33 31.33) rotate(-12.68)"/>
      <rect class="cls-1" x="94.53" y="365.32" width="21.19" height="8.17" transform="translate(-78.52 32.08) rotate(-12.68)"/>
      <rect class="cls-1" x="96.75" y="375.18" width="21.19" height="8.17" transform="translate(-80.63 32.81) rotate(-12.68)"/>
      <rect class="cls-1" x="98.82" y="385.1" width="21.19" height="8.17" transform="translate(-82.76 33.51) rotate(-12.68)"/>
      <rect class="cls-1" x="101.21" y="395.02" width="21.19" height="8.17" transform="translate(-84.88 34.27) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="90.22" y="345.19" width="21.19" height="8.17" transform="translate(-74.21 30.65) rotate(-12.68)"/>
      <rect class="cls-1" x="92.45" y="355.11" width="21.19" height="8.17" transform="translate(-76.33 31.38) rotate(-12.68)"/>
      <rect class="cls-1" x="94.75" y="365.33" width="21.19" height="8.17" transform="translate(-78.52 32.13) rotate(-12.68)"/>
      <rect class="cls-1" x="96.97" y="375.19" width="21.19" height="8.17" transform="translate(-80.63 32.86) rotate(-12.68)"/>
      <rect class="cls-1" x="99.04" y="385.12" width="21.19" height="8.17" transform="translate(-82.76 33.56) rotate(-12.68)"/>
      <rect class="cls-1" x="101.43" y="395.03" width="21.19" height="8.17" transform="translate(-84.87 34.32) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="103.65" y="404.89" width="21.19" height="8.17" transform="translate(-86.98 35.05) rotate(-12.68)"/>
      <rect class="cls-1" x="105.88" y="414.8" width="21.19" height="8.17" transform="translate(-89.1 35.78) rotate(-12.68)"/>
      <rect class="cls-1" x="108.18" y="425.03" width="21.19" height="8.17" transform="translate(-91.29 36.54) rotate(-12.68)"/>
      <rect class="cls-1" x="110.4" y="434.89" width="21.19" height="8.17" transform="translate(-93.4 37.26) rotate(-12.68)"/>
      <rect class="cls-1" x="112.47" y="444.81" width="21.19" height="8.17" transform="translate(-95.53 37.96) rotate(-12.68)"/>
      <rect class="cls-1" x="114.87" y="454.73" width="21.19" height="8.17" transform="translate(-97.65 38.73) rotate(-12.68)"/>
    </g>
    <rect class="cls-1" x="117.05" y="464.44" width="21.19" height="8.17" transform="translate(-99.73 39.44) rotate(-12.68)"/>
    <rect class="cls-1" x="119.28" y="474.36" width="21.19" height="8.17" transform="translate(-101.85 40.18) rotate(-12.68)"/>
    <rect class="cls-1" x="121.58" y="484.58" width="21.19" height="8.17" transform="translate(-104.04 40.93) rotate(-12.68)"/>
    <rect class="cls-1" x="123.8" y="494.45" width="21.19" height="8.17" transform="translate(-106.15 41.66) rotate(-12.68)"/>
    <rect class="cls-1" x="125.87" y="504.37" width="21.19" height="8.17" transform="translate(-108.28 42.35) rotate(-12.68)"/>
    <g>
      <rect class="cls-1" x="40.93" y="255.88" width="21.19" height="8.17" transform="translate(-55.81 17.65) rotate(-12.68)"/>
      <rect class="cls-1" x="43.17" y="265.8" width="21.19" height="8.17" transform="translate(-57.93 18.38) rotate(-12.68)"/>
      <rect class="cls-1" x="45.47" y="276.03" width="21.19" height="8.17" transform="translate(-60.12 19.14) rotate(-12.68)"/>
      <rect class="cls-1" x="47.69" y="285.89" width="21.19" height="8.17" transform="translate(-62.23 19.86) rotate(-12.68)"/>
      <rect class="cls-1" x="49.76" y="295.81" width="21.19" height="8.17" transform="translate(-64.36 20.56) rotate(-12.68)"/>
      <rect class="cls-1" x="52.15" y="305.73" width="21.19" height="8.17" transform="translate(-66.47 21.33) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="54.31" y="315.32" width="21.19" height="8.17" transform="translate(-68.53 22.03) rotate(-12.68)"/>
      <rect class="cls-1" x="56.54" y="325.24" width="21.19" height="8.17" transform="translate(-70.65 22.77) rotate(-12.68)"/>
      <rect class="cls-1" x="58.84" y="335.46" width="21.19" height="8.17" transform="translate(-72.84 23.52) rotate(-12.68)"/>
      <rect class="cls-1" x="61.06" y="345.32" width="21.19" height="8.17" transform="translate(-74.95 24.25) rotate(-12.68)"/>
      <rect class="cls-1" x="63.13" y="355.25" width="21.19" height="8.17" transform="translate(-77.07 24.94) rotate(-12.68)"/>
      <rect class="cls-1" x="65.52" y="365.16" width="21.19" height="8.17" transform="translate(-79.19 25.71) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="67.69" y="374.81" width="21.19" height="8.17" transform="translate(-81.26 26.42) rotate(-12.68)"/>
      <rect class="cls-1" x="69.92" y="384.73" width="21.19" height="8.17" transform="translate(-83.38 27.16) rotate(-12.68)"/>
      <rect class="cls-1" x="72.22" y="394.96" width="21.19" height="8.17" transform="translate(-85.57 27.91) rotate(-12.68)"/>
      <rect class="cls-1" x="74.44" y="404.82" width="21.19" height="8.17" transform="translate(-87.68 28.64) rotate(-12.68)"/>
      <rect class="cls-1" x="76.51" y="414.74" width="21.19" height="8.17" transform="translate(-89.81 29.33) rotate(-12.68)"/>
      <rect class="cls-1" x="78.91" y="424.66" width="21.19" height="8.17" transform="translate(-91.93 30.1) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="67.91" y="374.83" width="21.19" height="8.17" transform="translate(-81.26 26.47) rotate(-12.68)"/>
      <rect class="cls-1" x="70.15" y="384.75" width="21.19" height="8.17" transform="translate(-83.38 27.21) rotate(-12.68)"/>
      <rect class="cls-1" x="72.45" y="394.97" width="21.19" height="8.17" transform="translate(-85.57 27.96) rotate(-12.68)"/>
      <rect class="cls-1" x="74.67" y="404.83" width="21.19" height="8.17" transform="translate(-87.68 28.69) rotate(-12.68)"/>
      <rect class="cls-1" x="76.74" y="414.76" width="21.19" height="8.17" transform="translate(-89.81 29.38) rotate(-12.68)"/>
      <rect class="cls-1" x="79.13" y="424.67" width="21.19" height="8.17" transform="translate(-91.92 30.15) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="81.34" y="434.53" width="21.19" height="8.17" transform="translate(-94.03 30.88) rotate(-12.68)"/>
      <rect class="cls-1" x="83.58" y="444.44" width="21.19" height="8.17" transform="translate(-96.15 31.61) rotate(-12.68)"/>
      <rect class="cls-1" x="85.88" y="454.67" width="21.19" height="8.17" transform="translate(-98.34 32.36) rotate(-12.68)"/>
      <rect class="cls-1" x="88.1" y="464.53" width="21.19" height="8.17" transform="translate(-100.45 33.09) rotate(-12.68)"/>
      <rect class="cls-1" x="90.17" y="474.45" width="21.19" height="8.17" transform="translate(-102.58 33.79) rotate(-12.68)"/>
      <rect class="cls-1" x="92.56" y="484.37" width="21.19" height="8.17" transform="translate(-104.7 34.55) rotate(-12.68)"/>
    </g>
    <rect class="cls-1" x="94.74" y="494.08" width="21.19" height="8.17" transform="translate(-106.78 35.27) rotate(-12.68)"/>
    <rect class="cls-1" x="96.98" y="504" width="21.19" height="8.17" transform="translate(-108.9 36) rotate(-12.68)"/>
    <rect class="cls-1" x="99.28" y="514.22" width="21.19" height="8.17" transform="translate(-111.09 36.76) rotate(-12.68)"/>
    <g>
      <rect class="cls-1" x="27.41" y="196.49" width="21.19" height="8.17" transform="translate(-43.1 13.23) rotate(-12.68)"/>
      <rect class="cls-1" x="29.64" y="206.41" width="21.19" height="8.17" transform="translate(-45.22 13.97) rotate(-12.68)"/>
      <rect class="cls-1" x="31.94" y="216.63" width="21.19" height="8.17" transform="translate(-47.41 14.72) rotate(-12.68)"/>
      <rect class="cls-1" x="34.16" y="226.5" width="21.19" height="8.17" transform="translate(-49.52 15.45) rotate(-12.68)"/>
      <rect class="cls-1" x="36.23" y="236.42" width="21.19" height="8.17" transform="translate(-51.65 16.14) rotate(-12.68)"/>
      <rect class="cls-1" x="38.62" y="246.33" width="21.19" height="8.17" transform="translate(-53.77 16.91) rotate(-12.68)"/>
    </g>
    <g>
      <rect class="cls-1" x="160.28" y="696.14" width="21.19" height="8.17" transform="translate(-527.45 878.7) rotate(-90.62)"/>
      <rect class="cls-1" x="170.76" y="696.02" width="21.19" height="8.17" transform="translate(-516.74 889.06) rotate(-90.62)"/>
      <rect class="cls-1" x="180.87" y="695.91" width="21.19" height="8.17" transform="translate(-506.41 899.06) rotate(-90.62)"/>
      <rect class="cls-1" x="191" y="695.96" width="21.19" height="8.17" transform="translate(-496.21 909.24) rotate(-90.62)"/>
      <rect class="cls-1" x="201.2" y="695.69" width="21.19" height="8.17" transform="translate(-485.63 919.17) rotate(-90.62)"/>
      <g>
        <rect class="cls-1" x="211.72" y="695.58" width="21.19" height="8.17" transform="translate(-474.89 929.57) rotate(-90.62)"/>
        <rect class="cls-1" x="221.88" y="695.47" width="21.19" height="8.17" transform="translate(-464.5 939.62) rotate(-90.62)"/>
        <rect class="cls-1" x="232.36" y="695.35" width="21.19" height="8.17" transform="translate(-453.8 949.98) rotate(-90.62)"/>
        <rect class="cls-1" x="242.47" y="695.24" width="21.19" height="8.17" transform="translate(-443.47 959.98) rotate(-90.62)"/>
        <rect class="cls-1" x="252.61" y="695.29" width="21.19" height="8.17" transform="translate(-433.27 970.17) rotate(-90.62)"/>
        <rect class="cls-1" x="262.81" y="695.02" width="21.19" height="8.17" transform="translate(-422.69 980.09) rotate(-90.62)"/>
      </g>
      <rect class="cls-1" x="273.19" y="695.07" width="21.19" height="8.17" transform="translate(-412.24 990.52) rotate(-90.62)"/>
      <rect class="cls-1" x="283.35" y="694.96" width="21.19" height="8.17" transform="translate(-401.85 1000.57) rotate(-90.62)"/>
    </g>
    <g>
      <rect class="cls-1" x="160.01" y="665.14" width="21.19" height="8.17" transform="translate(-496.72 847.1) rotate(-90.62)"/>
      <rect class="cls-1" x="170.49" y="665.03" width="21.19" height="8.17" transform="translate(-486.02 857.46) rotate(-90.62)"/>
      <rect class="cls-1" x="180.6" y="664.92" width="21.19" height="8.17" transform="translate(-475.69 867.46) rotate(-90.62)"/>
      <rect class="cls-1" x="190.74" y="664.96" width="21.19" height="8.17" transform="translate(-465.49 877.64) rotate(-90.62)"/>
      <rect class="cls-1" x="200.93" y="664.7" width="21.19" height="8.17" transform="translate(-454.91 887.57) rotate(-90.62)"/>
      <g>
        <rect class="cls-1" x="211.45" y="664.58" width="21.19" height="8.17" transform="translate(-444.17 897.97) rotate(-90.62)"/>
        <rect class="cls-1" x="221.62" y="664.47" width="21.19" height="8.17" transform="translate(-433.78 908.02) rotate(-90.62)"/>
        <rect class="cls-1" x="232.1" y="664.36" width="21.19" height="8.17" transform="translate(-423.07 918.38) rotate(-90.62)"/>
        <rect class="cls-1" x="242.2" y="664.25" width="21.19" height="8.17" transform="translate(-412.74 928.38) rotate(-90.62)"/>
        <rect class="cls-1" x="252.34" y="664.29" width="21.19" height="8.17" transform="translate(-402.55 938.57) rotate(-90.62)"/>
        <rect class="cls-1" x="262.54" y="664.03" width="21.19" height="8.17" transform="translate(-391.97 948.49) rotate(-90.62)"/>
      </g>
      <rect class="cls-1" x="272.92" y="664.07" width="21.19" height="8.17" transform="translate(-381.52 958.92) rotate(-90.62)"/>
      <rect class="cls-1" x="283.09" y="663.96" width="21.19" height="8.17" transform="translate(-371.13 968.97) rotate(-90.62)"/>
    </g>
    <rect class="cls-1" x="180.15" y="637.07" width="21.19" height="8.17" transform="translate(-448.3 838.86) rotate(-90.62)"/>
    <rect class="cls-1" x="190.29" y="637.12" width="21.19" height="8.17" transform="translate(-438.1 849.05) rotate(-90.62)"/>
    <rect class="cls-1" x="200.49" y="636.85" width="21.19" height="8.17" transform="translate(-427.52 858.97) rotate(-90.62)"/>
    <g>
      <rect class="cls-1" x="211" y="636.74" width="21.19" height="8.17" transform="translate(-416.78 869.37) rotate(-90.62)"/>
      <rect class="cls-1" x="221.17" y="636.63" width="21.19" height="8.17" transform="translate(-406.39 879.43) rotate(-90.62)"/>
      <rect class="cls-1" x="231.65" y="636.51" width="21.19" height="8.17" transform="translate(-395.68 889.79) rotate(-90.62)"/>
      <rect class="cls-1" x="241.76" y="636.4" width="21.19" height="8.17" transform="translate(-385.35 899.79) rotate(-90.62)"/>
      <rect class="cls-1" x="251.89" y="636.45" width="21.19" height="8.17" transform="translate(-375.16 909.97) rotate(-90.62)"/>
      <rect class="cls-1" x="262.09" y="636.18" width="21.19" height="8.17" transform="translate(-364.58 919.9) rotate(-90.62)"/>
    </g>
    <rect class="cls-1" x="180.62" y="608.96" width="21.19" height="8.17" transform="translate(-419.71 810.91) rotate(-90.62)"/>
    <rect class="cls-1" x="190.79" y="608.85" width="21.19" height="8.17" transform="translate(-409.32 820.96) rotate(-90.62)"/>
    <rect class="cls-1" x="201.27" y="608.73" width="21.19" height="8.17" transform="translate(-398.62 831.33) rotate(-90.62)"/>
    <rect class="cls-1" x="211.37" y="608.62" width="21.19" height="8.17" transform="translate(-388.29 841.32) rotate(-90.62)"/>
    <rect class="cls-1" x="221.51" y="608.67" width="21.19" height="8.17" transform="translate(-378.09 851.51) rotate(-90.62)"/>
    <rect class="cls-1" x="231.71" y="608.4" width="21.19" height="8.17" transform="translate(-367.51 861.43) rotate(-90.62)"/>
    <rect class="cls-1" x="179.23" y="582.8" width="21.19" height="8.17" transform="translate(-394.96 783.08) rotate(-90.62)"/>
    <rect class="cls-1" x="189.34" y="582.69" width="21.19" height="8.17" transform="translate(-384.63 793.08) rotate(-90.62)"/>
    <rect class="cls-1" x="199.48" y="582.74" width="21.19" height="8.17" transform="translate(-374.43 803.26) rotate(-90.62)"/>
    <rect class="cls-1" x="209.67" y="582.47" width="21.19" height="8.17" transform="translate(-363.86 813.19) rotate(-90.62)"/>
    <rect class="cls-1" x="169.51" y="637.66" width="21.19" height="8.17" transform="translate(-459.64 828.82) rotate(-90.62)"/>
    <rect class="cls-1" x="169.98" y="609.54" width="21.19" height="8.17" transform="translate(-431.05 800.87) rotate(-90.62)"/>
    <rect class="cls-1" x="168.59" y="583.08" width="21.19" height="8.17" transform="translate(-405.99 772.72) rotate(-90.62)"/>
  </g>
                </svg>
            </div>
        </main>
    </div>
    <script src="js/svg-editor.js"></script>
</body>
</html>