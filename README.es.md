<div align="center">

# Dinamize

**Kiosco digital para plazas comerciales — directorio de tiendas, administración de la plaza y un minijuego.**

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=white) ![Python](https://img.shields.io/badge/Python-3776AB?style=flat-square&logo=python&logoColor=white)

[English](README.md) · **Español**

</div>

---

Una plataforma web para kioscos interactivos en plazas comerciales: los visitantes recorren las tiendas en una pantalla táctil y los administradores de la plaza mantienen el directorio al día desde un panel.

## Funciones

- 🏬 **Modo kiosco**: directorio de tiendas táctil para visitantes ([`kioskUI.php`](kioskUI.php)).
- 🗺️ **Administración de la plaza**: edita la información de la plaza y sus locales; asigna o libera un local para cada negocio.
- 👤 **Cuentas**: registro, inicio de sesión y perfil con foto.
- 🌙 Modo oscuro.
- 🔑 **Claves de activación**: herramientas de escritorio en Python (`Claves.py`) para vender y controlar licencias de kiosco.
- 🎮 **Cleaners**: un minijuego para el navegador (PWA instalable) en [`CleanersGame/`](CleanersGame).

## Instalación

1. Importa [`DB/DB.sql`](DB/DB.sql) en MySQL / MariaDB.
2. Copia `config.example.php` como **`config.local.php`** y pon tus credenciales de la base de datos (git ignora ese archivo). También puedes usar las variables de entorno `DB_HOST`, `DB_USER`, `DB_PASS` y `DB_NAME`.
3. Sirve la carpeta con PHP 8+ (XAMPP, Laragon o `php -S localhost:8000`) y abre `Index.php`.

Las herramientas de claves en Python leen las mismas variables:

```sh
python -m pip install customtkinter mysql-connector-python pillow
python Claves.py
```

## Seguridad

Las contraseñas se guardan con `password_hash()`. Las cuentas viejas con SHA-1 siguen funcionando y se actualizan solas la próxima vez que inician sesión.

## Estructura

```
*.php            páginas (kiosco, panel, login, administración)
backend/         manejadores de formularios y acceso a la base de datos
js/ · css/       scripts y estilos
DB/DB.sql        esquema de la base de datos
CleanersGame/    minijuego para navegador
Claves*.py       herramientas de claves de activación
```

La rama `Game` contiene solo el juego Cleaners.

---

Hecho por **[Jesús Aragón](https://github.com/RobiiAragon)** · [Portafolio](https://robiiaragon.github.io/portfolio/)
