<?php

/**
 * Model User - Gerenciamento de usuários
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    // Propriedades do usuário
    public $id;
    public $name;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;

    /**
     * Construtor
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Criar novo usuário
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name = :name, email = :email, password = :password";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind dos parâmetros
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Buscar usuário por email
     * @param string $email
     * @return bool
     */
    public function findByEmail($email) {
        $query = "SELECT id, name, email, password, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }

        return false;
    }

    /**
     * Buscar usuário por ID
     * @param int $id
     * @return bool
     */
    public function findById($id) {
        $query = "SELECT id, name, email, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }

        return false;
    }

    /**
     * Verificar se email já existe
     * @param string $email
     * @param int $exclude_id ID para excluir da verificação (para updates)
     * @return bool
     */
    public function emailExists($email, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $query .= " LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        
        if ($exclude_id) {
            $stmt->bindParam(":exclude_id", $exclude_id);
        }
        
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Verificar senha
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    /**
     * Atualizar dados do usuário
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, email = :email 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind dos parâmetros
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualizar senha do usuário
     * @param string $new_password
     * @return bool
     */
    public function updatePassword($new_password) {
        $query = "UPDATE " . $this->table_name . " 
                  SET password = :password 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar senha: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Excluir usuário
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todos os usuários
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT id, name, email, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar total de usuários
     * @return int
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Validar dados do usuário
     * @param array $data
     * @return array Array com erros de validação
     */
    public static function validate($data) {
        $errors = [];

        // Validar nome
        if (empty($data['name'])) {
            $errors['name'] = 'Nome é obrigatório';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Nome deve ter pelo menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Nome deve ter no máximo 100 caracteres';
        }

        // Validar email
        if (empty($data['email'])) {
            $errors['email'] = 'Email é obrigatório';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        } elseif (strlen($data['email']) > 150) {
            $errors['email'] = 'Email deve ter no máximo 150 caracteres';
        }

        // Validar senha (apenas se fornecida)
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < MIN_PASSWORD_LENGTH) {
                $errors['password'] = 'Senha deve ter pelo menos ' . MIN_PASSWORD_LENGTH . ' caracteres';
            }
        }

        return $errors;
    }

    /**
     * Obter estatísticas do usuário
     * @return array
     */
    public function getStats() {
        $query = "SELECT 
                    COUNT(t.id) as total_tasks,
                    SUM(CASE WHEN t.status = 'pendente' THEN 1 ELSE 0 END) as pending_tasks,
                    SUM(CASE WHEN t.status = 'concluida' THEN 1 ELSE 0 END) as completed_tasks,
                    SUM(CASE WHEN t.priority = 'alta' THEN 1 ELSE 0 END) as high_priority_tasks,
                    SUM(CASE WHEN t.due_date < CURDATE() AND t.status = 'pendente' THEN 1 ELSE 0 END) as overdue_tasks
                  FROM tasks t 
                  WHERE t.user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>
