<?php

/**
 * Controller HomeController - Páginas gerais
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

require_once __DIR__ . '/../config/config.php';

class HomeController {

    /**
     * Página inicial
     */
    public function index() {
        // Se estiver logado, redirecionar para dashboard
        if (isLoggedIn()) {
            redirect('/dashboard');
        }

        $data = [
            'title' => APP_NAME . ' - Sistema de Gerenciamento de Tarefas'
        ];

        $this->loadView('home/index', $data);
    }

    /**
     * Página sobre
     */
    public function about() {
        $data = [
            'title' => 'Sobre - ' . APP_NAME
        ];

        $this->loadView('home/about', $data);
    }

    /**
     * Página de contato
     */
    public function contact() {
        $data = [
            'title' => 'Contato - ' . APP_NAME,
            'csrf_token' => generateCSRFToken()
        ];

        $this->loadView('home/contact', $data);
    }

    /**
     * Processar formulário de contato
     */
    public function sendContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/contact');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/contact');
        }

        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');

        // Validar campos obrigatórios
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Nome é obrigatório';
        }

        if (empty($email)) {
            $errors['email'] = 'Email é obrigatório';
        } elseif (!validateEmail($email)) {
            $errors['email'] = 'Email inválido';
        }

        if (empty($subject)) {
            $errors['subject'] = 'Assunto é obrigatório';
        }

        if (empty($message)) {
            $errors['message'] = 'Mensagem é obrigatória';
        }

        // Se houver erros, retornar para o formulário
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ];
            redirect('/contact');
        }

        // Aqui você pode implementar o envio de email
        // Por exemplo, usando PHPMailer ou a função mail() do PHP
        
        // Simular envio bem-sucedido
        $_SESSION['success'] = 'Mensagem enviada com sucesso! Entraremos em contato em breve.';
        redirect('/contact');
    }

    /**
     * Página de erro 404
     */
    public function notFound() {
        http_response_code(404);
        
        $data = [
            'title' => 'Página não encontrada - ' . APP_NAME
        ];

        $this->loadView('errors/404', $data);
    }

    /**
     * Página de erro 500
     */
    public function serverError() {
        http_response_code(500);
        
        $data = [
            'title' => 'Erro interno - ' . APP_NAME
        ];

        $this->loadView('errors/500', $data);
    }

    /**
     * Página de manutenção
     */
    public function maintenance() {
        http_response_code(503);
        
        $data = [
            'title' => 'Manutenção - ' . APP_NAME
        ];

        $this->loadView('errors/maintenance', $data);
    }

    /**
     * Informações do sistema (apenas para desenvolvimento)
     */
    public function info() {
        // Apenas em ambiente de desenvolvimento
        if (!defined('DEBUG') || !DEBUG) {
            redirect('/');
        }

        $data = [
            'title' => 'Informações do Sistema - ' . APP_NAME,
            'php_version' => phpversion(),
            'server_info' => $_SERVER,
            'session_info' => $_SESSION ?? [],
            'app_config' => [
                'APP_NAME' => APP_NAME,
                'APP_VERSION' => APP_VERSION,
                'APP_URL' => APP_URL,
                'SESSION_TIMEOUT' => SESSION_TIMEOUT,
                'TASKS_PER_PAGE' => TASKS_PER_PAGE
            ]
        ];

        $this->loadView('home/info', $data);
    }

    /**
     * Verificar status da aplicação (health check)
     */
    public function health() {
        header('Content-Type: application/json');

        $status = 'ok';
        $checks = [];

        // Verificar conexão com banco de dados
        try {
            $database = new Database();
            $conn = $database->getConnection();
            $checks['database'] = 'ok';
        } catch (Exception $e) {
            $status = 'error';
            $checks['database'] = 'error';
        }

        // Verificar sessões
        if (session_status() === PHP_SESSION_ACTIVE) {
            $checks['session'] = 'ok';
        } else {
            $checks['session'] = 'warning';
        }

        // Verificar permissões de escrita
        if (is_writable(session_save_path())) {
            $checks['session_path'] = 'ok';
        } else {
            $checks['session_path'] = 'warning';
        }

        $response = [
            'status' => $status,
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => APP_VERSION,
            'checks' => $checks
        ];

        echo json_encode($response, JSON_PRETTY_PRINT);
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
