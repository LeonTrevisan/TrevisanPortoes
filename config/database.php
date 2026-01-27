<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $conn;

    public static function connect()
    {
        if (!self::$conn) {
            try {
                self::$conn = new PDO(
                    "mysql:host=localhost;dbname=bd_trevisanportoes;charset=utf8",
                    "root",
                    "",
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
