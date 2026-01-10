<?php
class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=localhost;dbname=vid10000_afol55;charset=utf8mb4",
                    "root",      // Benutzername (ggf. anpassen)
                    "",          // Passwort (ggf. anpassen)
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                die("❌ Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}



