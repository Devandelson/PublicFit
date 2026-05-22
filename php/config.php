<?php

class Database
{
    private $hostname = "localhost";
    private $port = "3306";
    private $database = "ruta";
    private $username = "root";
    private $password = "1212";
    private $charset = "utf8";

    function conectar()
    {
        try {
            $conexion = "mysql:host=" . $this->hostname . ";port=" . $this->port . ";dbname=" . $this->database . ";charset=" . $this->charset;

            $option = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            $pdo = new PDO($conexion, $this->username, $this->password, $option);

            return $pdo;
        } catch (PDOException $e) {
            echo 'Error de la conexión: ' . $e->getMessage();
            exit;
        }
    }
}
