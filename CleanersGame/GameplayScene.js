// Agregar al inicio de GameplayScene.js
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
            .then(registration => {
                console.log('ServiceWorker registrado con éxito:', registration);
            })
            .catch(error => {
                console.log('Error en el registro del ServiceWorker:', error);
            });
    });
}

window.addEventListener('gamepadconnected', (e) => {
    console.log('Control conectado:', e.gamepad);
    gamepadActive = true;
});

window.addEventListener('gamepaddisconnected', (e) => {
    console.log('Control desconectado:', e.gamepad);
    gamepadActive = false;
});

document.addEventListener('keydown', () => {
    if (gamepadActive) {
        gamepadActive = false;
    }
});

const PERSONAL_BESTS_KEY = 'personalBests';

const SCORES_KEYS = {
    easy: 'highScores_easy',
    normal: 'highScores_normal',
    hard: 'highScores_hard'
};

let highScores = [];
let playerName = '';
let playerNameText;
let player;
let npcs = [];
let cursors;
let pauseKey;
let isPaused = false;
let pauseText;
let mallObjects = [];
let trashGroup;
let score = 0;
let scoreText;
let trashTimers = [];
let lastTrashCount = 0;
let inventory = 0;
let inventoryText;
let inventoryFullText;
let inventoryCleanText;
let trashBin;
let timerText;
let gameOverText;
let timeLimit = 120; // 2 minutos en segundos
let gamepadActive = false;

const config = {
    type: Phaser.AUTO,
    width: 800,
    height: 600,
    parent: 'gameContainer',
    scene: {
        preload: preload,
        create: create,
        update: update
    },
    physics: {
        default: 'arcade',
        arcade: {
            gravity: { y: 0 },
            debug: false
        }
    }
};
let game = null;

const DIFFICULTY_SETTINGS = {
    easy: {
        minDelay: 7000,    // 7 segundos
        maxDelay: 12000,   // 12 segundos
        penalty: 5,        // Penalización de 5 puntos
        inventorySize: 15  // Inventario más grande en fácil
    },
    normal: {
        minDelay: 5000,    // 5 segundos
        maxDelay: 9000,    // 9 segundos
        penalty: 10,       // Penalización de 10 puntos
        inventorySize: 10  // Inventario normal
    },
    hard: {
        minDelay: 3000,    // 3 segundos
        maxDelay: 6000,    // 6 segundos
        penalty: 20,       // Penalización de 20 puntos
        inventorySize: 5   // Inventario pequeño en difícil
    }
};

let currentDifficulty = 'normal';

let gameStarted = false;

document.addEventListener('DOMContentLoaded', () => {
    // Cargar y mostrar los puntajes guardados al inicio
    updateScoreBoard('normal'); // Mostrar tabla inicial en modo normal

    const settingsBtn = document.getElementById('settingsButton');
    const popup = document.getElementById('settingsPopup');
    const closeBtn = document.querySelector('.close');
    const clearScoresBtn = document.getElementById('clearScores');
    const difficultySelect = document.getElementById('gameDifficulty');
    const nameInput = document.getElementById('nameInput');
    const startButton = document.getElementById('startButton');
    const nameInputContainer = document.getElementById('nameInputContainer');
    const touchModeCheckbox = document.getElementById('touchMode');

    // Cargar el estado del modo táctil desde localStorage
    const touchMode = localStorage.getItem('touchMode') === 'true';
    touchModeCheckbox.checked = touchMode;

    // Event listeners para las pestañas de dificultad
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Remover clase active de todos los botones
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            // Agregar clase active al botón clickeado
            btn.classList.add('active');
            // Actualizar la tabla con la dificultad seleccionada
            updateScoreBoard(btn.dataset.difficulty);
        });
    });

    startButton.addEventListener('click', () => {
        const name = nameInput.value.trim();
        if (name !== '') {
            playerName = name;
            nameInputContainer.style.display = 'none';
            if (!gameStarted) {
                gameStarted = true;
                game = new Phaser.Game(config);
            }
        } else {
            alert('Por favor ingresa un nombre');
        }
    });

    // También permitir iniciar con Enter
    nameInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            startButton.click();
        }
    });

    settingsBtn.onclick = () => popup.style.display = "block";
    closeBtn.onclick = () => popup.style.display = "none";

    window.onclick = (e) => {
        if (e.target == popup) {
            popup.style.display = "none";
        }
    }

    // Limpiar puntajes
    clearScoresBtn.onclick = () => {
        // Eliminar solo las entradas de puntajes del localStorage
        localStorage.removeItem(SCORES_KEYS.easy);
        localStorage.removeItem(SCORES_KEYS.normal);
        localStorage.removeItem(SCORES_KEYS.hard);
        localStorage.removeItem(PERSONAL_BESTS_KEY);
        // Actualizar la tabla de puntajes
        updateScoreBoard();
        alert('Los puntajes han sido borrados.');
    };

    // Cambio de dificultad
    difficultySelect.onchange = (e) => {
        currentDifficulty = e.target.value;
        updateNPCTimers();
        // Actualizar el texto del inventario con el nuevo tamaño máximo
        if (inventoryText) {
            const maxInventory = DIFFICULTY_SETTINGS[currentDifficulty].inventorySize;
            inventoryText.setText('Inventario: ' + inventory + '/' + maxInventory);
        }
    };
});

// Actualiza los temporizadores de los NPCs según la dificultad seleccionada
function updateNPCTimers() {
    const settings = DIFFICULTY_SETTINGS[currentDifficulty];
    trashTimers.forEach(timer => {
        timer.delay = Phaser.Math.Between(settings.minDelay, settings.maxDelay);
        timer.reset(timer.delay);
    });
}

// Carga los activos del juego
function preload() {
    this.load.image('background', 'assets/Background.png');
    this.load.image('trashBin', 'assets/Basurero.png');
    this.load.image('bench', 'assets/bench.png');
    this.load.image('kiosk', 'assets/kiosk.png');
    this.load.image('plant', 'assets/plant.png');
    this.load.image('trashTexture1', 'assets/trash/trash (1).png');
    this.load.image('trashTexture2', 'assets/trash/trash (2).png');
    this.load.image('trashTexture3', 'assets/trash/trash (3).png');
    this.load.image('trashTexture4', 'assets/trash/trash (4).png');
    this.load.image('trashTexture5', 'assets/trash/trash (5).png');
    this.load.image('trashTexture6', 'assets/trash/trash (6).png');
    this.load.image('trashTexture7', 'assets/trash/trash (7).png');
    this.load.image('trashTexture8', 'assets/trash/trash (8).png');
    this.load.image('trashTexture9', 'assets/trash/trash (9).png');
    this.load.image('trashTexture10', 'assets/trash/trash (10).png');
    this.load.image('trashTexture11', 'assets/trash/trash (11).png');
    this.load.image('trashTexture12', 'assets/trash/trash (12).png');
    this.load.image('trashTexture13', 'assets/trash/trash (13).png');
    this.load.image('trashTexture14', 'assets/trash/trash (14).png');

    // Cargar sprites idle
    this.load.image('idle-up', 'assets/player/idle/arriba1.png');
    this.load.image('idle-down', 'assets/player/idle/abajo1.png');
    this.load.image('idle-left', 'assets/player/idle/izquierda1.png');
    this.load.image('idle-right', 'assets/player/idle/derecha1.png');

    // Cargar sprites de animación
    this.load.image('walk-up-1', 'assets/player/animation_loop_UP/arriba1.png');
    this.load.image('walk-up-2', 'assets/player/animation_loop_UP/arriba2.png');
    this.load.image('walk-up-3', 'assets/player/animation_loop_UP/arriba3.png');

    this.load.image('walk-down-1', 'assets/player/animation_loop_down/abajo1.png');
    this.load.image('walk-down-2', 'assets/player/animation_loop_down/abajo2.png');
    this.load.image('walk-down-3', 'assets/player/animation_loop_down/abajo3.png');

    this.load.image('walk-left-1', 'assets/player/animation_loop_left/izquierda1.png');
    this.load.image('walk-left-2', 'assets/player/animation_loop_left/izquierda2.png');
    this.load.image('walk-left-3', 'assets/player/animation_loop_left/izquierda3.png');

    this.load.image('walk-right-1', 'assets/player/animation_loop_right/derecha1.png');
    this.load.image('walk-right-2', 'assets/player/animation_loop_right/derecha2.png');
    this.load.image('walk-right-3', 'assets/player/animation_loop_right/derecha3.png');

    // Cargar sprites de los multiples NPCs

    // Cargar sprites idle NPC1
    this.load.image('npc1-idle-up', 'assets/NPCs/npc1/idle/arriba1.png');
    this.load.image('npc1-idle-down', 'assets/NPCs/npc1/idle/abajo1.png');
    this.load.image('npc1-idle-left', 'assets/NPCs/npc1/idle/izquierda1.png');
    this.load.image('npc1-idle-right', 'assets/NPCs/npc1/idle/derecha1.png');

    this.load.image('npc1-up-1', 'assets/NPCs/npc1/animation_loop_UP/arriba1.png');
    this.load.image('npc1-up-2', 'assets/NPCs/npc1/animation_loop_UP/arriba2.png');
    this.load.image('npc1-up-3', 'assets/NPCs/npc1/animation_loop_UP/arriba3.png');

    this.load.image('npc1-down-1', 'assets/NPCs/npc1/animation_loop_down/abajo1.png');
    this.load.image('npc1-down-2', 'assets/NPCs/npc1/animation_loop_down/abajo2.png');
    this.load.image('npc1-down-3', 'assets/NPCs/npc1/animation_loop_down/abajo3.png');

    this.load.image('npc1-left-1', 'assets/NPCs/npc1/animation_loop_left/izquierda1.png');
    this.load.image('npc1-left-2', 'assets/NPCs/npc1/animation_loop_left/izquierda2.png');
    this.load.image('npc1-left-3', 'assets/NPCs/npc1/animation_loop_left/izquierda3.png');

    this.load.image('npc1-right-1', 'assets/NPCs/npc1/animation_loop_right/derecha1.png');
    this.load.image('npc1-right-2', 'assets/NPCs/npc1/animation_loop_right/derecha2.png');
    this.load.image('npc1-right-3', 'assets/NPCs/npc1/animation_loop_right/derecha3.png');

    // Cargar sprites idle NPC2
    this.load.image('npc2-idle-up', 'assets/NPCs/npc2/idle/arriba1.png');
    this.load.image('npc2-idle-down', 'assets/NPCs/npc2/idle/abajo1.png');
    this.load.image('npc2-idle-left', 'assets/NPCs/npc2/idle/izquierda1.png');
    this.load.image('npc2-idle-right', 'assets/NPCs/npc2/idle/derecha1.png');

    this.load.image('npc2-up-1', 'assets/NPCs/npc2/animation_loop_UP/arriba1.png');
    this.load.image('npc2-up-2', 'assets/NPCs/npc2/animation_loop_UP/arriba2.png');
    this.load.image('npc2-up-3', 'assets/NPCs/npc2/animation_loop_UP/arriba3.png');

    this.load.image('npc2-down-1', 'assets/NPCs/npc2/animation_loop_down/abajo1.png');
    this.load.image('npc2-down-2', 'assets/NPCs/npc2/animation_loop_down/abajo2.png');
    this.load.image('npc2-down-3', 'assets/NPCs/npc2/animation_loop_down/abajo3.png');

    this.load.image('npc2-left-1', 'assets/NPCs/npc2/animation_loop_left/izquierda1.png');
    this.load.image('npc2-left-2', 'assets/NPCs/npc2/animation_loop_left/izquierda2.png');
    this.load.image('npc2-left-3', 'assets/NPCs/npc2/animation_loop_left/izquierda3.png');

    this.load.image('npc2-right-1', 'assets/NPCs/npc2/animation_loop_right/derecha1.png');
    this.load.image('npc2-right-2', 'assets/NPCs/npc2/animation_loop_right/derecha2.png');
    this.load.image('npc2-right-3', 'assets/NPCs/npc2/animation_loop_right/derecha3.png');

    // Cargar sprites idle NPC3
    this.load.image('npc3-idle-up', 'assets/NPCs/npc3/idle/arriba1.png');
    this.load.image('npc3-idle-down', 'assets/NPCs/npc3/idle/abajo1.png');
    this.load.image('npc3-idle-left', 'assets/NPCs/npc3/idle/izquierda1.png');
    this.load.image('npc3-idle-right', 'assets/NPCs/npc3/idle/derecha1.png');

    this.load.image('npc3-up-1', 'assets/NPCs/npc3/animation_loop_UP/arriba1.png');
    this.load.image('npc3-up-2', 'assets/NPCs/npc3/animation_loop_UP/arriba2.png');
    this.load.image('npc3-up-3', 'assets/NPCs/npc3/animation_loop_UP/arriba3.png');

    this.load.image('npc3-down-1', 'assets/NPCs/npc3/animation_loop_down/abajo1.png');
    this.load.image('npc3-down-2', 'assets/NPCs/npc3/animation_loop_down/abajo2.png');
    this.load.image('npc3-down-3', 'assets/NPCs/npc3/animation_loop_down/abajo3.png');

    this.load.image('npc3-left-1', 'assets/NPCs/npc3/animation_loop_left/izquierda1.png');
    this.load.image('npc3-left-2', 'assets/NPCs/npc3/animation_loop_left/izquierda2.png');
    this.load.image('npc3-left-3', 'assets/NPCs/npc3/animation_loop_left/izquierda3.png');

    this.load.image('npc3-right-1', 'assets/NPCs/npc3/animation_loop_right/derecha1.png');
    this.load.image('npc3-right-2', 'assets/NPCs/npc3/animation_loop_right/derecha2.png');
    this.load.image('npc3-right-3', 'assets/NPCs/npc3/animation_loop_right/derecha3.png');
    
    // Cargar sprites idle NPC4
    this.load.image('npc4-idle-up', 'assets/NPCs/npc4/idle/arriba1.png');
    this.load.image('npc4-idle-down', 'assets/NPCs/npc4/idle/abajo1.png');
    this.load.image('npc4-idle-left', 'assets/NPCs/npc4/idle/izquierda1.png');
    this.load.image('npc4-idle-right', 'assets/NPCs/npc4/idle/derecha1.png');

    this.load.image('npc4-up-1', 'assets/NPCs/npc4/animation_loop_UP/arriba1.png');
    this.load.image('npc4-up-2', 'assets/NPCs/npc4/animation_loop_UP/arriba2.png');
    this.load.image('npc4-up-3', 'assets/NPCs/npc4/animation_loop_UP/arriba3.png');

    this.load.image('npc4-down-1', 'assets/NPCs/npc4/animation_loop_down/abajo1.png');
    this.load.image('npc4-down-2', 'assets/NPCs/npc4/animation_loop_down/abajo2.png');
    this.load.image('npc4-down-3', 'assets/NPCs/npc4/animation_loop_down/abajo3.png');

    this.load.image('npc4-left-1', 'assets/NPCs/npc4/animation_loop_left/izquierda1.png');
    this.load.image('npc4-left-2', 'assets/NPCs/npc4/animation_loop_left/izquierda2.png');
    this.load.image('npc4-left-3', 'assets/NPCs/npc4/animation_loop_left/izquierda3.png');

    this.load.image('npc4-right-1', 'assets/NPCs/npc4/animation_loop_right/derecha1.png');
    this.load.image('npc4-right-2', 'assets/NPCs/npc4/animation_loop_right/derecha2.png');
    this.load.image('npc4-right-3', 'assets/NPCs/npc4/animation_loop_right/derecha3.png');

    // Cargar sprites idle NPC5
    this.load.image('npc5-idle-up', 'assets/NPCs/npc5/idle/arriba1.png');
    this.load.image('npc5-idle-down', 'assets/NPCs/npc5/idle/abajo1.png');
    this.load.image('npc5-idle-left', 'assets/NPCs/npc5/idle/izquierda1.png');
    this.load.image('npc5-idle-right', 'assets/NPCs/npc5/idle/derecha1.png');

    this.load.image('npc5-up-1', 'assets/NPCs/npc5/animation_loop_UP/arriba1.png');
    this.load.image('npc5-up-2', 'assets/NPCs/npc5/animation_loop_UP/arriba2.png');
    this.load.image('npc5-up-3', 'assets/NPCs/npc5/animation_loop_UP/arriba3.png');

    this.load.image('npc5-down-1', 'assets/NPCs/npc5/animation_loop_down/abajo1.png');
    this.load.image('npc5-down-2', 'assets/NPCs/npc5/animation_loop_down/abajo2.png');
    this.load.image('npc5-down-3', 'assets/NPCs/npc5/animation_loop_down/abajo3.png');

    this.load.image('npc5-left-1', 'assets/NPCs/npc5/animation_loop_left/izquierda1.png');
    this.load.image('npc5-left-2', 'assets/NPCs/npc5/animation_loop_left/izquierda2.png');
    this.load.image('npc5-left-3', 'assets/NPCs/npc5/animation_loop_left/izquierda3.png');

    this.load.image('npc5-right-1', 'assets/NPCs/npc5/animation_loop_right/derecha1.png');
    this.load.image('npc5-right-2', 'assets/NPCs/npc5/animation_loop_right/derecha2.png');
    this.load.image('npc5-right-3', 'assets/NPCs/npc5/animation_loop_right/derecha3.png');

    // Cargar sprites idle NPC6
    this.load.image('npc6-idle-up', 'assets/NPCs/npc6/idle/arriba1.png');
    this.load.image('npc6-idle-down', 'assets/NPCs/npc6/idle/abajo1.png');
    this.load.image('npc6-idle-left', 'assets/NPCs/npc6/idle/izquierda1.png');
    this.load.image('npc6-idle-right', 'assets/NPCs/npc6/idle/derecha1.png');

    this.load.image('npc6-up-1', 'assets/NPCs/npc6/animation_loop_UP/arriba1.png');
    this.load.image('npc6-up-2', 'assets/NPCs/npc6/animation_loop_UP/arriba2.png');
    this.load.image('npc6-up-3', 'assets/NPCs/npc6/animation_loop_UP/arriba3.png');
    
    this.load.image('npc6-down-1', 'assets/NPCs/npc6/animation_loop_down/abajo1.png');
    this.load.image('npc6-down-2', 'assets/NPCs/npc6/animation_loop_down/abajo2.png');
    this.load.image('npc6-down-3', 'assets/NPCs/npc6/animation_loop_down/abajo3.png');

    this.load.image('npc6-left-1', 'assets/NPCs/npc6/animation_loop_left/izquierda1.png');
    this.load.image('npc6-left-2', 'assets/NPCs/npc6/animation_loop_left/izquierda2.png');
    this.load.image('npc6-left-3', 'assets/NPCs/npc6/animation_loop_left/izquierda3.png');

    this.load.image('npc6-right-1', 'assets/NPCs/npc6/animation_loop_right/derecha1.png');
    this.load.image('npc6-right-2', 'assets/NPCs/npc6/animation_loop_right/derecha2.png');
    this.load.image('npc6-right-3', 'assets/NPCs/npc6/animation_loop_right/derecha3.png');
}

// Crea y configura los elementos del juego
function create() {
    // 1. INICIALIZACIÓN BÁSICA
    const background = this.add.image(0, 0, 'background');
    background.setOrigin(0, 0);
    background.setDisplaySize(1280, 720);

    // 2. CREACIÓN DE ELEMENTOS DEL JUEGO
    // Contenedor de basura
    trashBin = this.physics.add.sprite(90, 560, 'trashBin');
    trashBin.setDisplaySize(120, 130);
    trashBin.setCircle(150, 200);
    trashBin.setImmovable(true);

    // Jugador
    player = this.physics.add.sprite(400, 300, 'idle-down');
    player.setCollideWorldBounds(true);
    player.setDisplaySize(50, 60);

    // Animaciones del jugador
    this.anims.create({
        key: 'walk-up',
        frames: [
            { key: 'walk-up-1' },
            { key: 'walk-up-2' },
            { key: 'walk-up-3' }
        ],
        frameRate: 8,
        repeat: -1
    });

    this.anims.create({
        key: 'walk-down',
        frames: [
            { key: 'walk-down-1' },
            { key: 'walk-down-2' },
            { key: 'walk-down-3' }
        ],
        frameRate: 8,
        repeat: -1
    });

    this.anims.create({
        key: 'walk-left',
        frames: [
            { key: 'walk-left-1' },
            { key: 'walk-left-2' },
            { key: 'walk-left-3' }
        ],
        frameRate: 8,
        repeat: -1
    });

    this.anims.create({
        key: 'walk-right',
        frames: [
            { key: 'walk-right-1' },
            { key: 'walk-right-2' },
            { key: 'walk-right-3' }
        ],
        frameRate: 8,
        repeat: -1
    });

    // Crear animaciones para cada NPC
    for (let i = 1; i <= 6; i++) {
        // Animaciones para caminar hacia arriba
        this.anims.create({
            key: `npc${i}-walk-up`,
            frames: [
                { key: `npc${i}-up-1` },
                { key: `npc${i}-up-2` },
                { key: `npc${i}-up-3` }
            ],
            frameRate: 8,
            repeat: -1
        });

        // Animaciones para caminar hacia abajo
        this.anims.create({
            key: `npc${i}-walk-down`,
            frames: [
                { key: `npc${i}-down-1` },
                { key: `npc${i}-down-2` },
                { key: `npc${i}-down-3` }
            ],
            frameRate: 8,
            repeat: -1
        });

        // Animaciones para caminar hacia la izquierda
        this.anims.create({
            key: `npc${i}-walk-left`,
            frames: [
                { key: `npc${i}-left-1` },
                { key: `npc${i}-left-2` },
                { key: `npc${i}-left-3` }
            ],
            frameRate: 8,
            repeat: -1
        });

        // Animaciones para caminar hacia la derecha
        this.anims.create({
            key: `npc${i}-walk-right`,
            frames: [
                { key: `npc${i}-right-1` },
                { key: `npc${i}-right-2` },
                { key: `npc${i}-right-3` }
            ],
            frameRate: 8,
            repeat: -1
        });
    }

    // Creación de NPCs
    npcs = [];
    for (let i = 0; i < 6; i++) {
        const npc = this.physics.add.sprite(
            Phaser.Math.Between(100, 700),
            Phaser.Math.Between(100, 500),
            `npc${i + 1}-idle-down`
        );
        npc.setCollideWorldBounds(true);
        npc.setDisplaySize(25, 50);
        npc.npcIndex = i + 1; // Guardar el índice del NPC
        npcs.push(npc);

        const settings = DIFFICULTY_SETTINGS[currentDifficulty];
        const timer = this.time.addEvent({
            delay: Phaser.Math.Between(settings.minDelay, settings.maxDelay),
            callback: () => dropTrash(npc),
            callbackScope: this,
            loop: true
        });
        trashTimers.push(timer);
    }

    // Objetos del mall
    createMallObject(this, 200, 150, 'bench', 100, 60);
    createMallObject(this, 400, 150, 'plant', 50, 90);
    createMallObject(this, 600, 150, 'bench', 100, 60);
    createMallObject(this, 200, 400, 'plant', 50, 90);
    createMallObject(this, 400, 400, 'kiosk', 100, 70);
    createMallObject(this, 600, 400, 'plant', 50, 90);

    // 3. FÍSICAS Y COLISIONES
    this.physics.world.setBounds(0, 50, 750, 550);

    // Colisiones con el contenedor
    this.physics.add.collider(player, trashBin, emptyInventory, null, this);
    npcs.forEach(npc => this.physics.add.collider(npc, trashBin));

    // Colisiones entre jugador y NPCs
    npcs.forEach(npc => this.physics.add.collider(player, npc));

    // Colisiones entre NPCs
    npcs.forEach((npc1, index) => {
        for (let j = index + 1; j < npcs.length; j++) {
            this.physics.add.collider(npc1, npcs[j]);
        }
    });

    // Colisiones con objetos del mall
    mallObjects.forEach(obj => {
        npcs.forEach(npc => this.physics.add.collider(npc, obj));
        this.physics.add.collider(player, obj);
    });

    // Grupo de basura y su colisión
    trashGroup = this.physics.add.group();
    this.physics.add.overlap(player, trashGroup, collectTrash, null, this);

    // 4. TEMPORIZADORES
    this.time.addEvent({
        delay: 1000,
        callback: changeNpcDirection,
        callbackScope: this,
        loop: true
    });

    this.time.addEvent({
        delay: 1000,
        callback: checkTrashPenalty,
        callbackScope: this,
        loop: true
    });

    this.time.addEvent({
        delay: 1000,
        callback: updateTimer,
        callbackScope: this,
        loop: true
    });

    // 5. UI Y CONTROLES
    cursors = this.input.keyboard.createCursorKeys();
    pauseKey = this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.P);

    // Textos del juego
    scoreText = this.add.text(16, 16, 'Puntos: ' + score, { fontSize: '32px', fill: '#fff' });
    const maxInventory = DIFFICULTY_SETTINGS[currentDifficulty].inventorySize;
    inventoryText = this.add.text(16, 50, `Inventario: ${inventory}/${maxInventory}`, { fontSize: '32px', fill: '#fff' });
    timerText = this.add.text(16, 84, 'Tiempo: 02:00', { fontSize: '32px', fill: '#fff' });

    // Textos de estado
    pauseText = this.add.text(400, 300, 'PAUSA', { fontSize: '32px', fill: '#fff' }).setOrigin(0.5);
    inventoryFullText = this.add.text(400, 300, 'Inventario lleno, ve a vaciarlo', { fontSize: '32px', fill: '#ff0000' }).setOrigin(0.5);
    inventoryCleanText = this.add.text(400, 300, 'Inventario limpio', { fontSize: '32px', fill: '#00ff00' }).setOrigin(0.5);
    gameOverText = this.add.text(400, 300, '', { fontSize: '32px', fill: '#fff' }).setOrigin(0.5);

    // Establecer visibilidad inicial
    pauseText.setVisible(false);
    inventoryFullText.setVisible(false);
    inventoryCleanText.setVisible(false);
    gameOverText.setVisible(false);

    // Nombre del jugador
    playerNameText = this.add.text(player.x, player.y - 40, playerName, { fontSize: '20px', fill: '#fff' }).setOrigin(0.5);
}

// Crea objetos del mall en posiciones específicas
function createMallObject(scene, x, y, key, width, height) {
    const obj = scene.physics.add.sprite(x, y, key);
    obj.setDisplaySize(width, height); // Ajustar el tamaño del sprite
    obj.setImmovable(true);
    mallObjects.push(obj);
}

let pauseButtonPressed = false;

// Maneja la entrada del gamepad para controlar al jugador
function handleGamepadInput() {
    const gamepads = navigator.getGamepads();
    if (!gamepads) return;

    for (let i = 0; i < gamepads.length; i++) {
        const gamepad = gamepads[i];
        if (!gamepad) continue;

        const speed = 200;
        let moving = false;

        // Joystick izquierdo con deadzone ajustada
        const leftStickX = Math.abs(gamepad.axes[0]) > 0.15 ? gamepad.axes[0] : 0;
        const leftStickY = Math.abs(gamepad.axes[1]) > 0.15 ? gamepad.axes[1] : 0;

        player.setVelocityX(leftStickX * speed);
        player.setVelocityY(leftStickY * speed);

        if (leftStickX !== 0 || leftStickY !== 0) {
            moving = true;
            gamepadActive = true;
        }

        // D-pad
        const dpadUp = gamepad.buttons[12]?.pressed;
        const dpadDown = gamepad.buttons[13]?.pressed;
        const dpadLeft = gamepad.buttons[14]?.pressed;
        const dpadRight = gamepad.buttons[15]?.pressed;

        if (dpadLeft) {
            player.setVelocityX(-speed);
            moving = true;
            gamepadActive = true;
        }
        if (dpadRight) {
            player.setVelocityX(speed);
            moving = true;
            gamepadActive = true;
        }
        if (dpadUp) {
            player.setVelocityY(-speed);
            moving = true;
            gamepadActive = true;
        }
        if (dpadDown) {
            player.setVelocityY(speed);
            moving = true;
            gamepadActive = true;
        }

        // Actualizar animación si hay movimiento
        if (moving) {
            updatePlayerAnimation();
        } else {
            player.setVelocity(0);
            player.anims.stop();
            if (player.anims.currentAnim) {
                const direction = player.anims.currentAnim.key.split('-')[1];
                player.setTexture(`idle-${direction}`);
            }
        }

        // Feedback de vibración no en este lugar
    }
}

let lastInputType = 'keyboard'; // o 'gamepad'

// Maneja la entrada del usuario, ya sea teclado o gamepad
function handleInput() {
    if (gamepadActive) {
        lastInputType = 'gamepad';
        handleGamepadInput.call(this);
    } else {
        lastInputType = 'keyboard';
        handleKeyboardInput.call(this);
    }
}

// Maneja la entrada del teclado para controlar al jugador
function handleKeyboardInput() {
    const speed = 200;
    let moving = false;

    if (cursors.left.isDown) {
        player.setVelocityX(-speed);
        moving = true;
        gamepadActive = false;
    } else if (cursors.right.isDown) {
        player.setVelocityX(speed);
        moving = true;
        gamepadActive = false;
    }

    if (cursors.up.isDown) {
        player.setVelocityY(-speed);
        moving = true;
        gamepadActive = false;
    } else if (cursors.down.isDown) {
        player.setVelocityY(speed);
        moving = true;
        gamepadActive = false;
    }

    if (moving) {
        updatePlayerAnimation();
    } else {
        player.setVelocity(0);
        player.anims.stop();
        if (player.anims.currentAnim) {
            const direction = player.anims.currentAnim.key.split('-')[1];
            player.setTexture(`idle-${direction}`);
        }
    }
}

// Actualiza la animación del jugador según su dirección de movimiento
function updatePlayerAnimation() {
    if (player.body.velocity.x < 0) {
        player.anims.play('walk-left', true);
    } else if (player.body.velocity.x > 0) {
        player.anims.play('walk-right', true);
    } else if (player.body.velocity.y < 0) {
        player.anims.play('walk-up', true);
    } else if (player.body.velocity.y > 0) {
        player.anims.play('walk-down', true);
    }
}

// Alterna el estado de pausa del juego
function togglePause() {
    isPaused = !isPaused;
    if (isPaused) {
        this.physics.pause();
        pauseText.setVisible(true);
        trashTimers.forEach(timer => timer.paused = true);
        this.time.paused = true;
    } else {
        this.physics.resume();
        pauseText.setVisible(false);
        trashTimers.forEach(timer => timer.paused = false);
        this.time.paused = false;
    }
}

// Función principal de actualización del juego, llamada en cada frame
function update() {
    if (Phaser.Input.Keyboard.JustDown(pauseKey) && !gameOverText.visible) {
        togglePause.call(this);
    }

    if (!isPaused) {
        // Resetear velocidad
        player.setVelocity(0);

        // Manejar input
        handleInput.call(this);

        updatePlayerAnimation();
        playerNameText.setPosition(player.x, player.y - 40);
        applySeparation();
    }
    
    updateBossEmotion();
}

// Cambia la dirección de movimiento de todos los NPCs a una dirección aleatoria
function changeNpcDirection() {
    npcs.forEach(npc => {
        const angle = Phaser.Math.FloatBetween(0, 2 * Math.PI);
        const speed = 100;
        const velocityX = Math.cos(angle) * speed;
        const velocityY = Math.sin(angle) * speed;
        
        npc.setVelocity(velocityX, velocityY);

        // Determinar la dirección predominante y reproducir la animación correspondiente
        if (Math.abs(velocityX) > Math.abs(velocityY)) {
            if (velocityX > 0) {
                npc.anims.play(`npc${npc.npcIndex}-walk-right`, true);
            } else {
                npc.anims.play(`npc${npc.npcIndex}-walk-left`, true);
            }
        } else {
            if (velocityY > 0) {
                npc.anims.play(`npc${npc.npcIndex}-walk-down`, true);
            } else {
                npc.anims.play(`npc${npc.npcIndex}-walk-up`, true);
            }
        }

        // Si la velocidad es muy baja, mostrar el sprite idle correspondiente
        if (Math.abs(velocityX) < 10 && Math.abs(velocityY) < 10) {
            // Detener la animación actual
            npc.anims.stop();
            
            // Determinar la última dirección y establecer el sprite idle correspondiente
            let direction = 'down'; // dirección por defecto
            if (npc.anims.currentAnim) {
                const currentAnimName = npc.anims.currentAnim.key;
                direction = currentAnimName.split('-')[2]; // obtiene 'up', 'down', 'left' o 'right'
            }
            npc.setTexture(`npc${npc.npcIndex}-idle-${direction}`);
        }
    });
}

// Actualiza la emoción del jefe basándose en la cantidad de basura activa
function updateBossEmotion() {
    const trashCount = trashGroup.countActive(true);
    const bossImage = document.getElementById('bossImage');

    if (trashCount >= 0 && trashCount <= 9) {
        bossImage.src = 'assets/boss/Gud.png';
    } else if (trashCount >= 10 && trashCount <= 14) {
        bossImage.src = 'assets/boss/ok.png';
    } else if (trashCount >= 15 && trashCount <= 20) {
        bossImage.src = 'assets/boss/HAAA.png';
    }
}

// Aplica una separación entre los NPCs para evitar que se aglomeren
function applySeparation() {
    const separationDistance = 50;
    npcs.forEach(npc1 => {
        let separationForceX = 0;
        let separationForceY = 0;
        npcs.forEach(npc2 => {
            if (npc1 !== npc2) {
                const distance = Phaser.Math.Distance.Between(npc1.x, npc1.y, npc2.x, npc2.y);
                if (distance < separationDistance) {
                    const angle = Phaser.Math.Angle.Between(npc1.x, npc1.y, npc2.x, npc2.y);
                    separationForceX -= Math.cos(angle);
                    separationForceY -= Math.sin(angle);
                }
            }
        });
        npc1.setVelocity(npc1.body.velocity.x + separationForceX * 10, npc1.body.velocity.y + separationForceY * 10);
    });
}

// Crea una nueva basura en la posición del NPC especificado
function dropTrash(npc) {
    if (trashGroup.countActive(true) < 20) {
        // Obtener un número aleatorio entre 1 y 14 para seleccionar la textura
        const textureNumber = Phaser.Math.Between(1, 14);
        const trashTextureName = `trashTexture${textureNumber}`;
        
        const trash = trashGroup.create(npc.x, npc.y, trashTextureName);
        trash.setCollideWorldBounds(true);
        trash.setDisplaySize(10, 20);
    }
}

// Recoge la basura cuando el jugador colisiona con ella
function collectTrash(player, trash) {
    const maxInventory = DIFFICULTY_SETTINGS[currentDifficulty].inventorySize;
    if (inventory < maxInventory) {
        trash.destroy();
        inventory++;
        score += 10;
        scoreText.setText('Puntos: ' + score);
        inventoryText.setText('Inventario: ' + inventory + '/' + maxInventory);
        
        // Vibración al recolectar basura
        vibrateGamepad();
    } else {
        inventoryFullText.setVisible(true);
        this.time.delayedCall(2000, () => {
            inventoryFullText.setVisible(false);
        });
    }
}

// Vacía el inventario cuando el jugador interactúa con el contenedor de basura
function emptyInventory() {
    if (inventory > 0) {
        inventory = 0;
        const maxInventory = DIFFICULTY_SETTINGS[currentDifficulty].inventorySize;
        inventoryText.setText('Inventario: ' + inventory + '/' + maxInventory);
        inventoryCleanText.setVisible(true);
        this.time.delayedCall(2000, () => {
            inventoryCleanText.setVisible(false);
        });

        // Vibración al limpiar inventario
        vibrateGamepad();
    }
}

// Aplica una penalización al puntaje si hay demasiada basura en juego
function checkTrashPenalty() {
    const trashCount = trashGroup.countActive(true);
    if (trashCount > 10 && trashCount < 20 && trashCount > lastTrashCount) {
        const penalty = DIFFICULTY_SETTINGS[currentDifficulty].penalty;
        score = Math.max(0, score - penalty); // Asegurarse de que el puntaje no sea menor a 0
        scoreText.setText('Puntos: ' + score);
    }
    lastTrashCount = trashCount;
}

// Actualiza el temporizador del juego y termina el juego cuando se agota el tiempo
function updateTimer() {
    if (timeLimit > 0) {
        timeLimit--;
        const minutes = Math.floor(timeLimit / 60);
        const seconds = timeLimit % 60;
        timerText.setText('Tiempo: ' + minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0'));
    } else {
        endGame.call(this);
    }
}

// Actualiza los puntajes altos en el almacenamiento local
function updateHighScores(playerName, score) {
    const scoreKey = SCORES_KEYS[currentDifficulty];
    let scores = JSON.parse(localStorage.getItem(scoreKey)) || [];
    const existingScoreIndex = scores.findIndex(item => item.name === playerName);
    let isNewPlayer = false;
    
    if (existingScoreIndex !== -1) {
        if (score > scores[existingScoreIndex].score) {
            scores[existingScoreIndex].score = score;
        }
    } else {
        scores.push({ name: playerName, score: score });
        isNewPlayer = true;
    }
    
    scores.sort((a, b) => b.score - a.score);
    scores = scores.slice(0, 10);
    
    localStorage.setItem(scoreKey, JSON.stringify(scores));
    updateScoreBoard();
    
    return isNewPlayer;
}

// Actualiza la tabla de puntajes en la interfaz de usuario
function updateScoreBoard(forceDifficulty = null) {
    const difficulty = forceDifficulty || currentDifficulty;
    const scores = JSON.parse(localStorage.getItem(SCORES_KEYS[difficulty])) || [];
    const tbody = document.querySelector('#highScoresTable tbody');
    
    // Actualizar botones de dificultad
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.difficulty === difficulty);
    });
    
    tbody.innerHTML = '';
    
    if (scores.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="3">No hay puntajes para ${difficulty}</td>
        `;
        tbody.appendChild(row);
        return;
    }
    
    scores.forEach((item, index) => {
        const row = document.createElement('tr');
        const isCurrentPlayer = item.name === playerName;
        
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.name}${isCurrentPlayer ? ' (Tú)' : ''}</td>
            <td>${item.score}</td>
        `;
        
        if (isCurrentPlayer) {
            row.classList.add('current-player');
        }
        
        tbody.appendChild(row);
    });
}

// Termina el juego, actualiza puntajes y muestra animaciones
function endGame() {
    this.physics.pause();
    trashTimers.forEach(timer => timer.paused = true);
    gameOverText.setText('Juego terminado! Puntaje final: ' + score);
    gameOverText.setVisible(true);
    isPaused = true;
    
    // Obtener los récords personales de todos los jugadores
    let personalBests = JSON.parse(localStorage.getItem(PERSONAL_BESTS_KEY)) || {};
    
    // Obtener el récord personal del jugador actual
    const playerBest = personalBests[playerName] || 0;
    
    console.log('Puntaje actual:', score);
    console.log('Mejor puntaje personal:', playerBest);

    // Verificar si es nuevo jugador
    let isNewPlayer = updateHighScores(playerName, score);

    // Verificar si rompió su récord personal
    let brokePersonalBest = false;
    if (score > playerBest) {
        // Actualizar el récord personal del jugador
        personalBests[playerName] = score;
        localStorage.setItem(PERSONAL_BESTS_KEY, JSON.stringify(personalBests));
        brokePersonalBest = true;
    }

    // Lanzar confeti si es nuevo jugador o rompió su récord
    if (isNewPlayer || brokePersonalBest) {
        confetti({
            particleCount: 500,
            spread: 180,
            origin: { y: 0.6 }
        });
    }
}

// Función para vibrar el gamepad si está disponible
function vibrateGamepad() {
    const gamepads = navigator.getGamepads ? navigator.getGamepads() : [];
    for (let gp of gamepads) {
        if (gp && gp.vibrationActuator) {
            gp.vibrationActuator.playEffect('dual-rumble', {
                duration: 100,
                strongMagnitude: 0.7,
                weakMagnitude: 0.7
            }).catch(err => {
                console.warn('Vibración no soportada:', err);
            });
        }
    }
}

