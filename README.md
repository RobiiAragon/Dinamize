<div align="center">

# Dinamize

**Digital kiosk for shopping malls — store directory, mall management and a mini-game.**

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=white) ![Python](https://img.shields.io/badge/Python-3776AB?style=flat-square&logo=python&logoColor=white)

**English** · [Español](README.es.md)

</div>

---

A web platform for interactive kiosks in shopping malls: visitors browse the stores on a touch screen, and mall administrators keep the directory up to date from a dashboard.

## Features

- 🏬 **Kiosk mode** — a touch-friendly store directory for visitors ([`kioskUI.php`](kioskUI.php)).
- 🗺️ **Mall management** — edit the mall's info and its retail units; assign or free a unit for each business.
- 👤 **Accounts** — sign up, log in and a profile with photo.
- 🌙 Dark mode.
- 🔑 **Activation keys** — small Python desktop tools (`Claves.py`) to sell and track kiosk license keys.
- 🎮 **Cleaners** — a little browser game (installable PWA) in [`CleanersGame/`](CleanersGame).

## Setup

1. Import [`DB/DB.sql`](DB/DB.sql) into MySQL / MariaDB.
2. Copy `config.example.php` to **`config.local.php`** and fill in your database credentials (this file is ignored by git). You can also use the `DB_HOST`, `DB_USER`, `DB_PASS` and `DB_NAME` environment variables.
3. Serve the folder with PHP 8+ (XAMPP, Laragon or `php -S localhost:8000`) and open `Index.php`.

The Python key tools read the same environment variables:

```sh
python -m pip install customtkinter mysql-connector-python pillow
python Claves.py
```

## Security

Passwords are stored with `password_hash()`. Old SHA-1 accounts still work and are upgraded automatically the next time they log in.

## Structure

```
*.php            pages (kiosk, dashboard, login, management)
backend/         form handlers and database access
js/ · css/       front-end scripts and styles
DB/DB.sql        database schema
CleanersGame/    browser mini-game
Claves*.py       activation key tools
```

The `Game` branch contains only the Cleaners game.

> The interface is mostly in Spanish.

---

Made by **[Jesús Aragón](https://github.com/RobiiAragon)** · [Portfolio](https://robiiaragon.github.io/portfolio/)
