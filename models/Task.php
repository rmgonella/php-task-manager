<?php

/**
 * Model Task - Gerenciamento de tarefas
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

require_once __DIR__ . '/../config/database.php';

class Task {
    private $conn;
    private $table_name = "tasks";

    // Propriedades da tarefa
    public $id;
    public $user_id;
    public $title;
    public $description;
    public $status;
    public $priority;
    public $due_date;
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
     * Criar nova tarefa
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id = :user_id, title = :title, description = :description, 
                      status = :status, priority = :priority, due_date = :due_date";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->priority = htmlspecialchars(strip_tags($this->priority));

        // Bind dos parâmetros
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":priority", $this->priority);
        $stmt->bindParam(":due_date", $this->due_date);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar tarefa: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Buscar tarefa por ID
     * @param int $id
     * @return bool
     */
    public function findById($id) {
        $query = "SELECT id, user_id, title, description, status, priority, due_date, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->status = $row['status'];
            $this->priority = $row['priority'];
            $this->due_date = $row['due_date'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }

        return false;
    }

    /**
     * Atualizar tarefa
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, description = :description, status = :status, 
                      priority = :priority, due_date = :due_date 
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->priority = htmlspecialchars(strip_tags($this->priority));

        // Bind dos parâmetros
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":priority", $this->priority);
        $stmt->bindParam(":due_date", $this->due_date);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar tarefa: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Excluir tarefa
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao excluir tarefa: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar tarefas do usuário
     * @param int $user_id
     * @param string $status Filtro por status (opcional)
     * @param string $priority Filtro por prioridade (opcional)
     * @param int $limit
     * @param int $offset
     * @param string $order_by Campo para ordenação
     * @param string $order_direction Direção da ordenação (ASC/DESC)
     * @return array
     */
    public function getByUser($user_id, $status = null, $priority = null, $limit = 10, $offset = 0, $order_by = 'created_at', $order_direction = 'DESC') {
        $query = "SELECT id, user_id, title, description, status, priority, due_date, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id";

        // Adicionar filtros
        if ($status) {
            $query .= " AND status = :status";
        }
        
        if ($priority) {
            $query .= " AND priority = :priority";
        }

        // Adicionar ordenação
        $allowed_order_fields = ['id', 'title', 'status', 'priority', 'due_date', 'created_at', 'updated_at'];
        if (!in_array($order_by, $allowed_order_fields)) {
            $order_by = 'created_at';
        }
        
        $order_direction = strtoupper($order_direction) === 'ASC' ? 'ASC' : 'DESC';
        $query .= " ORDER BY " . $order_by . " " . $order_direction;

        // Adicionar paginação
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        
        if ($status) {
            $stmt->bindParam(":status", $status);
        }
        
        if ($priority) {
            $stmt->bindParam(":priority", $priority);
        }
        
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar tarefas do usuário
     * @param int $user_id
     * @param string $status Filtro por status (opcional)
     * @param string $priority Filtro por prioridade (opcional)
     * @return int
     */
    public function countByUser($user_id, $status = null, $priority = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE user_id = :user_id";

        if ($status) {
            $query .= " AND status = :status";
        }
        
        if ($priority) {
            $query .= " AND priority = :priority";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        
        if ($status) {
            $stmt->bindParam(":status", $status);
        }
        
        if ($priority) {
            $stmt->bindParam(":priority", $priority);
        }
        
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Marcar tarefa como concluída
     * @return bool
     */
    public function markAsCompleted() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'concluida' 
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao marcar tarefa como concluída: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar tarefa como pendente
     * @return bool
     */
    public function markAsPending() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'pendente' 
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao marcar tarefa como pendente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar tarefas em atraso do usuário
     * @param int $user_id
     * @return array
     */
    public function getOverdueTasks($user_id) {
        $query = "SELECT id, user_id, title, description, status, priority, due_date, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND due_date < CURDATE() AND status = 'pendente'
                  ORDER BY due_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar tarefas próximas do vencimento
     * @param int $user_id
     * @param int $days Número de dias para considerar como "próximo"
     * @return array
     */
    public function getUpcomingTasks($user_id, $days = 7) {
        $query = "SELECT id, user_id, title, description, status, priority, due_date, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY) 
                        AND status = 'pendente'
                  ORDER BY due_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":days", $days, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Validar dados da tarefa
     * @param array $data
     * @return array Array com erros de validação
     */
    public static function validate($data) {
        $errors = [];

        // Validar título
        if (empty($data['title'])) {
            $errors['title'] = 'Título é obrigatório';
        } elseif (strlen($data['title']) < 3) {
            $errors['title'] = 'Título deve ter pelo menos 3 caracteres';
        } elseif (strlen($data['title']) > MAX_TITLE_LENGTH) {
            $errors['title'] = 'Título deve ter no máximo ' . MAX_TITLE_LENGTH . ' caracteres';
        }

        // Validar descrição (opcional)
        if (!empty($data['description']) && strlen($data['description']) > MAX_DESCRIPTION_LENGTH) {
            $errors['description'] = 'Descrição deve ter no máximo ' . MAX_DESCRIPTION_LENGTH . ' caracteres';
        }

        // Validar status
        $valid_statuses = ['pendente', 'concluida'];
        if (!empty($data['status']) && !in_array($data['status'], $valid_statuses)) {
            $errors['status'] = 'Status inválido';
        }

        // Validar prioridade
        $valid_priorities = ['baixa', 'media', 'alta'];
        if (!empty($data['priority']) && !in_array($data['priority'], $valid_priorities)) {
            $errors['priority'] = 'Prioridade inválida';
        }

        // Validar data de vencimento (opcional)
        if (!empty($data['due_date'])) {
            $date = DateTime::createFromFormat('Y-m-d', $data['due_date']);
            if (!$date || $date->format('Y-m-d') !== $data['due_date']) {
                $errors['due_date'] = 'Data de vencimento inválida';
            }
        }

        return $errors;
    }

    /**
     * Verificar se a tarefa pertence ao usuário
     * @param int $task_id
     * @param int $user_id
     * @return bool
     */
    public function belongsToUser($task_id, $user_id) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE id = :task_id AND user_id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":task_id", $task_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Obter estatísticas das tarefas do usuário
     * @param int $user_id
     * @return array
     */
    public function getUserStats($user_id) {
        $query = "SELECT 
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pending_tasks,
                    SUM(CASE WHEN status = 'concluida' THEN 1 ELSE 0 END) as completed_tasks,
                    SUM(CASE WHEN priority = 'alta' THEN 1 ELSE 0 END) as high_priority_tasks,
                    SUM(CASE WHEN priority = 'media' THEN 1 ELSE 0 END) as medium_priority_tasks,
                    SUM(CASE WHEN priority = 'baixa' THEN 1 ELSE 0 END) as low_priority_tasks,
                    SUM(CASE WHEN due_date < CURDATE() AND status = 'pendente' THEN 1 ELSE 0 END) as overdue_tasks
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>
