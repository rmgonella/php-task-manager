<?php

/**
 * Configurações do banco de dados
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'php_task_manager';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    public $conn;

    /**
     * Conectar ao banco de dados MySQL
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $exception) {
            error_log("Erro de conexão: " . $exception->getMessage());
            throw new Exception("Erro ao conectar com o banco de dados");
        }
        
        return $this->conn;
    }

    /**
     * Fechar conexão com o banco de dados
     */
    public function closeConnection() {
        $this->conn = null;
    }

    /**
     * Verificar se a conexão está ativa
     * @return bool
     */
    public function isConnected() {
        return $this->conn !== null;
    }
}

?>
