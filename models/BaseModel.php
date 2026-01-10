<?php

namespace models;
class BaseModel
{
    protected $db;

    public function __construct()
    {
        try {
            $this->db = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            die('Datenbankverbindung fehlgeschlagen: ' . $e->getMessage());
        }
    }
}
