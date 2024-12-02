// Agregar al inicio de GameplayScene.js
const PERSONAL_BESTS_KEY = 'personalBests';

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
let timeLimit = 120; // 2 minutes in seconds

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

// Agregar al inicio de GameplayScene.js
document.addEventListener('DOMContentLoaded', () => {
     // Cargar y mostrar los puntajes guardados al inicio
     const savedScores = JSON.parse(localStorage.getItem('highScores')) || [];
     updateScoreBoard(savedScores);
 
     const settingsBtn = document.getElementById('settingsButton');
     const popup = document.getElementById('settingsPopup');
     const closeBtn = document.querySelector('.close');
     const clearScoresBtn = document.getElementById('clearScores');
     const difficultySelect = document.getElementById('gameDifficulty');
     const nameInput = document.getElementById('nameInput');
     const startButton = document.getElementById('startButton');
     const nameInputContainer = document.getElementById('nameInputContainer');
 

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

    clearScoresBtn.onclick = () => {
        localStorage.removeItem('highScores');
        localStorage.removeItem(PERSONAL_BESTS_KEY);
        updateScoreBoard([]);
    }

    // 3. Modificar la función de cambio de dificultad en el event listener
    difficultySelect.onchange = (e) => {
    currentDifficulty = e.target.value;
    updateNPCTimers();
    // Actualizar el texto del inventario con el nuevo tamaño máximo
    const maxInventory = DIFFICULTY_SETTINGS[currentDifficulty].inventorySize;
    inventoryText.setText('Inventario: ' + inventory + '/' + maxInventory);
};
    
});

function updateNPCTimers() {
    const settings = DIFFICULTY_SETTINGS[currentDifficulty];
    trashTimers.forEach(timer => {
        timer.delay = Phaser.Math.Between(settings.minDelay, settings.maxDelay);
        timer.reset(timer.delay);
    });
}

function preload() {
    this.load.image('background', 'assets/Background.png');
    this.load.image('trashBin', 'assets/Basurero.png');
    this.load.image('bench', 'assets/bench.png');
    this.load.image('kiosk', 'assets/kiosk.png');
    this.load.image('plant', 'assets/plant.png');
    this.load.image('trashTexture', 'assets/trash/trash.png');

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
}

function create() {
    // 1. INICIALIZACIÓN BÁSICA
    // Fondo del juego
    const background = this.add.image(0, 0, 'background');
    background.setOrigin(0, 0);
    background.setDisplaySize(1280, 720);

    // Generación de texturas
    const graphics = this.add.graphics();
    // Textura del jugador
    graphics.fillStyle(0x0000ff, 1.0);
    graphics.fillRect(0, 0, 50, 50);
    graphics.generateTexture('playerTexture', 50, 50);
    graphics.clear();
    // Textura de NPC
    graphics.fillStyle(0xffff00, 1.0);
    graphics.fillRect(0, 0, 50, 50);
    graphics.generateTexture('npcTexture', 50, 50);
    graphics.clear();
    // Textura de basura
    graphics.fillStyle(0x808080, 1.0);
    graphics.fillRect(0, 0, 20, 20);
    graphics.generateTexture('trashTexture', 15, 20);
    graphics.destroy();

    // 2. CREACIÓN DE ELEMENTOS DEL JUEGO
    // Contenedor de basura
    trashBin = this.physics.add.sprite(90, 530, 'trashBin');
    trashBin.setDisplaySize(120, 90);
    trashBin.setCircle(150, 200);
    trashBin.setImmovable(true);
    // Jugador
    player = this.physics.add.sprite(400, 300, 'idle-down');
    player.setCollideWorldBounds(true);
    player.setDisplaySize(50, 60);

    // Crear animaciones
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

    // NPCs
    for (let i = 0; i < 6; i++) {
        const npc = this.physics.add.sprite(
            Phaser.Math.Between(100, 700),
            Phaser.Math.Between(100, 500),
            'npcTexture'
        );
        npc.setCollideWorldBounds(true);
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
    createMallObject(this, 400, 150, 'plant', 50, 70);
    createMallObject(this, 600, 150, 'bench', 100, 60);
    createMallObject(this, 200, 400, 'plant', 50, 70);
    createMallObject(this, 400, 400, 'kiosk', 100, 50);
    createMallObject(this, 600, 400, 'plant', 50, 70);

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
    // Movimiento de NPCs
    this.time.addEvent({
        delay: 1000,
        callback: changeNpcDirection,
        callbackScope: this,
        loop: true
    });

    // Penalización por basura
    this.time.addEvent({
        delay: 1000,
        callback: checkTrashPenalty,
        callbackScope: this,
        loop: true
    });

    // Temporizador del juego
    this.time.addEvent({
        delay: 1000,
        callback: updateTimer,
        callbackScope: this,
        loop: true
    });

    // 5. UI Y CONTROLES
    // Controles
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

    // Establecer visibilidad inicial de textos de estado
    pauseText.setVisible(false);
    inventoryFullText.setVisible(false);
    inventoryCleanText.setVisible(false);
    gameOverText.setVisible(false);

    // Mostrar el nombre del jugador sobre el jugador
    playerNameText = this.add.text(player.x, player.y - 40, playerName, { fontSize: '20px', fill: '#fff' }).setOrigin(0.5);
}

function createMallObject(scene, x, y, key, width, height) {
    const obj = scene.physics.add.sprite(x, y, key);
    obj.setDisplaySize(width, height); // Ajustar el tamaño del sprite
    obj.setImmovable(true);
    mallObjects.push(obj);
}

function update() {
    if (Phaser.Input.Keyboard.JustDown(pauseKey) && !gameOverText.visible) {
        isPaused = !isPaused;
        if (isPaused) {
            this.physics.pause();
            pauseText.setVisible(true);
            trashTimers.forEach(timer => timer.paused = true);
            this.time.paused = true; // Pausar el tiempo
        } else {
            this.physics.resume();
            pauseText.setVisible(false);
            trashTimers.forEach(timer => timer.paused = false);
            this.time.paused = false; // Reanudar el tiempo
        }
    }

    if (!isPaused) {
        player.setVelocity(0);

        let moving = false;

        if (cursors.left.isDown) {
            player.setVelocityX(-200);
            moving = true;
        } else if (cursors.right.isDown) {
            player.setVelocityX(200);
            moving = true;
        }

        if (cursors.up.isDown) {
            player.setVelocityY(-200);
            moving = true;
        } else if (cursors.down.isDown) {
            player.setVelocityY(200);
            moving = true;
        }

        if (moving) {
            if (cursors.left.isDown && cursors.up.isDown) {
                player.anims.play('walk-left', true);
            } else if (cursors.left.isDown && cursors.down.isDown) {
                player.anims.play('walk-left', true);
            } else if (cursors.right.isDown && cursors.up.isDown) {
                player.anims.play('walk-right', true);
            } else if (cursors.right.isDown && cursors.down.isDown) {
                player.anims.play('walk-right', true);
            } else if (cursors.left.isDown) {
                player.anims.play('walk-left', true);
            } else if (cursors.right.isDown) {
                player.anims.play('walk-right', true);
            } else if (cursors.up.isDown) {
                player.anims.play('walk-up', true);
            } else if (cursors.down.isDown) {
                player.anims.play('walk-down', true);
            }
        } else {
            player.anims.stop();
            // Establecer el sprite idle correspondiente
            if (player.anims.currentAnim) {
                const direction = player.anims.currentAnim.key.split('-')[1];
                player.setTexture(`idle-${direction}`);
            }
        }

        // Actualizar la posición del texto del nombre del jugador
        playerNameText.setPosition(player.x, player.y - 40);

        // Apply separation behavior to NPCs
        applySeparation();
    }
    updateBossEmotion();
}

function changeNpcDirection() {
    npcs.forEach(npc => {
        const angle = Phaser.Math.FloatBetween(0, 2 * Math.PI);
        const speed = 100;
        npc.setVelocity(Math.cos(angle) * speed, Math.sin(angle) * speed);
    });
}

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

function dropTrash(npc) {
    if (trashGroup.countActive(true) < 20) {
        const trash = trashGroup.create(npc.x, npc.y, 'trashTexture');
        trash.setCollideWorldBounds(true);
        trash.setDisplaySize(20, 30); // Ajusta el tamaño de la basura aquí
    }
}

// 2. Modificar la función collectTrash
function collectTrash(player, trash) {
    const maxInventory = DIFFICULTY_SETTINGS[currentDifficulty].inventorySize;
    if (inventory < maxInventory) {
        trash.destroy();
        inventory++;
        score += 10;
        scoreText.setText('Puntos: ' + score);
        inventoryText.setText('Inventario: ' + inventory + '/' + maxInventory);
    } else {
        inventoryFullText.setVisible(true);
        this.time.delayedCall(2000, () => {
            inventoryFullText.setVisible(false);
        });
    }
}

function emptyInventory() {
    if (inventory > 0) {
        inventory = 0;
        const maxInventory = DIFFICULTY_SETTINGS[currentDifficulty].inventorySize;
        inventoryText.setText('Inventario: ' + inventory + '/' + maxInventory);
        inventoryCleanText.setVisible(true);
        this.time.delayedCall(2000, () => {
            inventoryCleanText.setVisible(false);
        });
    }
}


function checkTrashPenalty() {
    const trashCount = trashGroup.countActive(true);
    if (trashCount > 10 && trashCount < 20 && trashCount > lastTrashCount) {
        const penalty = DIFFICULTY_SETTINGS[currentDifficulty].penalty;
        score = Math.max(0, score - penalty); // Asegurarse de que el puntaje no sea menor a 0
        scoreText.setText('Puntos: ' + score);
    }
    lastTrashCount = trashCount;
}

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

// Modificar la función updateHighScores
function updateHighScores(playerName, score) {
    let scores = JSON.parse(localStorage.getItem('highScores')) || [];
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
    
    // Guardar en localStorage y actualizar la visualización
    localStorage.setItem('highScores', JSON.stringify(scores));
    updateScoreBoard(scores);
    
    return isNewPlayer;
}

// Modificar la función updateScoreBoard
function updateScoreBoard(scores) {
    const tbody = document.querySelector('#highScoresTable tbody');
    tbody.innerHTML = '';
    
    if (scores.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="3">No hay puntajes aún</td>
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
            row.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
            row.style.fontWeight = 'bold';
        }
        
        tbody.appendChild(row);
    });
}

// Modificar la función endGame para incluir la actualización de puntajes
// Modificar la función endGame:
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