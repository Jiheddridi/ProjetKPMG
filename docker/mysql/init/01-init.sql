-- Script d'initialisation de la base de données MySQL pour COBIT Laravel

-- Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS cobit_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
USE cobit_laravel;

-- Accorder tous les privilèges à l'utilisateur Laravel
GRANT ALL PRIVILEGES ON cobit_laravel.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;

-- Configuration MySQL optimisée pour Laravel
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';
