<?php

/**
 * Controller TaskController - Gerenciamento de tarefas
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/User.php';

class TaskController {
    private $task;
    private $user;

    /**
     * Construtor
     */
    public function __construct() {
        $this->task = new Task();
        $this->user = new User();
        
        // Verificar autenticação
        AuthController::requireAuth();
    }

    /**
     * Exibir dashboard com lista de tarefas
     */
    public function dashboard() {
        $user_id = $_SESSION['user_id'];
        
        // Parâmetros de filtro e paginação
        $status = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = TASKS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $order_by = $_GET['order_by'] ?? 'created_at';
        $order_direction = $_GET['order_direction'] ?? 'DESC';

        // Buscar tarefas
        $tasks = $this->task->getByUser($user_id, $status, $priority, $limit, $offset, $order_by, $order_direction);
        $total_tasks = $this->task->countByUser($user_id, $status, $priority);
        $total_pages = ceil($total_tasks / $limit);

        // Buscar estatísticas
        $stats = $this->task->getUserStats($user_id);
        
        // Buscar tarefas em atraso
        $overdue_tasks = $this->task->getOverdueTasks($user_id);
        
        // Buscar tarefas próximas do vencimento
        $upcoming_tasks = $this->task->getUpcomingTasks($user_id);

        $data = [
            'title' => 'Dashboard - ' . APP_NAME,
            'tasks' => $tasks,
            'stats' => $stats,
            'overdue_tasks' => $overdue_tasks,
            'upcoming_tasks' => $upcoming_tasks,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_tasks' => $total_tasks,
            'filters' => [
                'status' => $status,
                'priority' => $priority,
                'order_by' => $order_by,
                'order_direction' => $order_direction
            ],
            'csrf_token' => generateCSRFToken()
        ];

        $this->loadView('tasks/dashboard', $data);
    }

    /**
     * Exibir formulário de criação de tarefa
     */
    public function create() {
        $data = [
            'title' => 'Nova Tarefa - ' . APP_NAME,
            'csrf_token' => generateCSRFToken()
        ];

        $this->loadView('tasks/create', $data);
    }

    /**
     * Processar criação de tarefa
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tasks/create');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/tasks/create');
        }

        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $priority = sanitizeInput($_POST['priority'] ?? 'media');
        $due_date = sanitizeInput($_POST['due_date'] ?? '');

        // Validar dados
        $validation_errors = Task::validate([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'due_date' => $due_date
        ]);

        // Se houver erros, retornar para o formulário
        if (!empty($validation_errors)) {
            $_SESSION['errors'] = $validation_errors;
            $_SESSION['old_data'] = [
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'due_date' => $due_date
            ];
            redirect('/tasks/create');
        }

        // Criar tarefa
        $this->task->user_id = $_SESSION['user_id'];
        $this->task->title = $title;
        $this->task->description = $description;
        $this->task->status = TASK_STATUS_PENDING;
        $this->task->priority = $priority;
        $this->task->due_date = !empty($due_date) ? $due_date : null;

        if ($this->task->create()) {
            $_SESSION['success'] = 'Tarefa criada com sucesso!';
            redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Erro ao criar tarefa. Tente novamente.';
            redirect('/tasks/create');
        }
    }

    /**
     * Exibir detalhes da tarefa
     */
    public function show($id) {
        if (!$this->task->findById($id)) {
            $_SESSION['error'] = 'Tarefa não encontrada';
            redirect('/dashboard');
        }

        // Verificar se a tarefa pertence ao usuário
        if ($this->task->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Acesso negado';
            redirect('/dashboard');
        }

        $data = [
            'title' => 'Tarefa: ' . $this->task->title . ' - ' . APP_NAME,
            'task' => $this->task,
            'csrf_token' => generateCSRFToken()
        ];

        $this->loadView('tasks/show', $data);
    }

    /**
     * Exibir formulário de edição de tarefa
     */
    public function edit($id) {
        if (!$this->task->findById($id)) {
            $_SESSION['error'] = 'Tarefa não encontrada';
            redirect('/dashboard');
        }

        // Verificar se a tarefa pertence ao usuário
        if ($this->task->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Acesso negado';
            redirect('/dashboard');
        }

        $data = [
            'title' => 'Editar Tarefa - ' . APP_NAME,
            'task' => $this->task,
            'csrf_token' => generateCSRFToken()
        ];

        $this->loadView('tasks/edit', $data);
    }

    /**
     * Processar atualização de tarefa
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tasks/' . $id . '/edit');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/tasks/' . $id . '/edit');
        }

        if (!$this->task->findById($id)) {
            $_SESSION['error'] = 'Tarefa não encontrada';
            redirect('/dashboard');
        }

        // Verificar se a tarefa pertence ao usuário
        if ($this->task->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Acesso negado';
            redirect('/dashboard');
        }

        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $status = sanitizeInput($_POST['status'] ?? '');
        $priority = sanitizeInput($_POST['priority'] ?? '');
        $due_date = sanitizeInput($_POST['due_date'] ?? '');

        // Validar dados
        $validation_errors = Task::validate([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'priority' => $priority,
            'due_date' => $due_date
        ]);

        // Se houver erros, retornar para o formulário
        if (!empty($validation_errors)) {
            $_SESSION['errors'] = $validation_errors;
            redirect('/tasks/' . $id . '/edit');
        }

        // Atualizar tarefa
        $this->task->title = $title;
        $this->task->description = $description;
        $this->task->status = $status;
        $this->task->priority = $priority;
        $this->task->due_date = !empty($due_date) ? $due_date : null;

        if ($this->task->update()) {
            $_SESSION['success'] = 'Tarefa atualizada com sucesso!';
            redirect('/tasks/' . $id);
        } else {
            $_SESSION['error'] = 'Erro ao atualizar tarefa. Tente novamente.';
            redirect('/tasks/' . $id . '/edit');
        }
    }

    /**
     * Excluir tarefa
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/dashboard');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/dashboard');
        }

        if (!$this->task->findById($id)) {
            $_SESSION['error'] = 'Tarefa não encontrada';
            redirect('/dashboard');
        }

        // Verificar se a tarefa pertence ao usuário
        if ($this->task->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Acesso negado';
            redirect('/dashboard');
        }

        if ($this->task->delete()) {
            $_SESSION['success'] = 'Tarefa excluída com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir tarefa. Tente novamente.';
        }

        redirect('/dashboard');
    }

    /**
     * Marcar tarefa como concluída
     */
    public function complete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/dashboard');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/dashboard');
        }

        if (!$this->task->findById($id)) {
            $_SESSION['error'] = 'Tarefa não encontrada';
            redirect('/dashboard');
        }

        // Verificar se a tarefa pertence ao usuário
        if ($this->task->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Acesso negado';
            redirect('/dashboard');
        }

        if ($this->task->markAsCompleted()) {
            $_SESSION['success'] = 'Tarefa marcada como concluída!';
        } else {
            $_SESSION['error'] = 'Erro ao atualizar tarefa. Tente novamente.';
        }

        // Redirecionar de volta para a página anterior ou dashboard
        $redirect_url = $_POST['redirect_url'] ?? '/dashboard';
        redirect($redirect_url);
    }

    /**
     * Marcar tarefa como pendente
     */
    public function reopen($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/dashboard');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/dashboard');
        }

        if (!$this->task->findById($id)) {
            $_SESSION['error'] = 'Tarefa não encontrada';
            redirect('/dashboard');
        }

        // Verificar se a tarefa pertence ao usuário
        if ($this->task->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Acesso negado';
            redirect('/dashboard');
        }

        if ($this->task->markAsPending()) {
            $_SESSION['success'] = 'Tarefa reaberta com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao atualizar tarefa. Tente novamente.';
        }

        // Redirecionar de volta para a página anterior ou dashboard
        $redirect_url = $_POST['redirect_url'] ?? '/dashboard';
        redirect($redirect_url);
    }

    /**
     * Listar tarefas (API JSON)
     */
    public function apiList() {
        header('Content-Type: application/json');

        $user_id = $_SESSION['user_id'];
        $status = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';
        $limit = min(100, max(1, intval($_GET['limit'] ?? 10)));
        $offset = max(0, intval($_GET['offset'] ?? 0));

        $tasks = $this->task->getByUser($user_id, $status, $priority, $limit, $offset);
        $total = $this->task->countByUser($user_id, $status, $priority);

        echo json_encode([
            'success' => true,
            'data' => $tasks,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * Carregar view
     * @param string $view
     * @param array $data
     */
    private function loadView($view, $data = []) {
        // Extrair dados para variáveis
        extract($data);

        // Incluir header
        include __DIR__ . '/../views/layouts/header.php';

        // Incluir view específica
        $view_file = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo "<div class='alert alert-danger'>View não encontrada: " . $view . "</div>";
        }

        // Incluir footer
        include __DIR__ . '/../views/layouts/footer.php';
    }
}

?>
