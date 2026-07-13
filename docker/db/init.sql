-- ── init.sql ────────────────────────────────────────────────
-- Se ejecuta automáticamente la primera vez que el contenedor
-- MySQL se inicia con un volumen vacío.
-- Solo se ejecuta UNA VEZ (cuando la BD se crea por primera vez).

CREATE DATABASE IF NOT EXISTS taskmanager_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON taskmanager_testing.* TO 'taskman'@'%';
FLUSH PRIVILEGES;
