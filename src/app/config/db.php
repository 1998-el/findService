<?php
// DbConfig.php

class DbConfig {
    private $host = '127.0.0.1';
    private $db = 'services';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';

    public function getConnection() {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {
            return new PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException("Erreur de connexion : " . $e->getMessage());
        }
    }
}
?>