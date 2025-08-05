<?php
// lib/Database.php

class Database
{
    public static function connect()
    {
        // 1. On récupère le tableau de config
        $cfg = require __DIR__ . '/../config/db.php';

        // 2. On construit le DSN en utilisant ces valeurs
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8',
            $cfg['host'],
            $cfg['port'],
            $cfg['database']
        );

        // 3. On instancie PDO avec user/password de la config
        $db = new PDO(
            $dsn,
            $cfg['username'],
            $cfg['password']
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}
