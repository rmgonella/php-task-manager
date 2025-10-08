<?php

/**
 * Controller AuthController - Gerenciamento de autenticação
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    /**
     * Construtor
     */
    public function __construct() {
        $this->user = new User();
    }

    /**
     * Exibir página de login
     */
    public function showLogin() {
        // Se já estiver logado, redirecionar para dashboard
        if (isLoggedIn()) {
            redirect('/dashboard');
        }

        $data = [
            'title' => 'Login - ' . APP_NAME,
            'csrf_token' => generateCSRFToken()
        ];

        $this->loadView('auth/login', $data);
    }

    /**
     * Processar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/login');
        }

        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validar campos obrigatórios
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email e senha são obrigatórios';
            redirect('/login');
        }

        // Validar formato do email
        if (!validateEmail($email)) {
            $_SESSION['error'] = 'Email inválido';
            redirect('/login');
        }

        // Buscar usuário por email
        if ($this->user->findByEmail($email)) {
            // Verificar senha
            if ($this->user->verifyPassword($password)) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->name;
                $_SESSION['user_email'] = $this->user->email;
                $_SESSION['last_activity'] = time();
                
                // Regenerar ID da sessão por segurança
                session_regenerate_id(true);

                $_SESSION['success'] = 'Login realizado com sucesso!';
                redirect('/dashboard');
            } else {
                $_SESSION['error'] = 'Email ou senha incorretos';
                redirect('/login');
            }
        } else {
            $_SESSION['error'] = 'Email ou senha incorretos';
            redirect('/login');
        }
    }

    /**
     * Exibir página de registro
     */
    public function showRegister() {
        // Se já estiver logado, redirecionar para dashboard
        if (isLoggedIn()) {
            redirect('/dashboard');
        }

        $data = [
            'title' => 'Registro - ' . APP_NAME,
            'csrf_token' => generateCSRFToken()
        ];

        $this->loadView('auth/register', $data);
    }

    /**
     * Processar registro
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/register');
        }

        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validar dados
        $validation_errors = User::validate([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        // Verificar se as senhas coincidem
        if ($password !== $confirm_password) {
            $validation_errors['confirm_password'] = 'As senhas não coincidem';
        }

        // Verificar se email já existe
        if ($this->user->emailExists($email)) {
            $validation_errors['email'] = 'Este email já está em uso';
        }

        // Se houver erros, retornar para o formulário
        if (!empty($validation_errors)) {
            $_SESSION['errors'] = $validation_errors;
            $_SESSION['old_data'] = [
                'name' => $name,
                'email' => $email
            ];
            redirect('/register');
        }

        // Criar usuário
        $this->user->name = $name;
        $this->user->email = $email;
        $this->user->password = $password;

        if ($this->user->create()) {
            // Registro bem-sucedido - fazer login automático
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['user_name'] = $this->user->name;
            $_SESSION['user_email'] = $this->user->email;
            $_SESSION['last_activity'] = time();
            
            // Regenerar ID da sessão por segurança
            session_regenerate_id(true);

            $_SESSION['success'] = 'Conta criada com sucesso! Bem-vindo ao ' . APP_NAME;
            redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Erro ao criar conta. Tente novamente.';
            redirect('/register');
        }
    }

    /**
     * Processar logout
     */
    public function logout() {
        // Destruir sessão
        session_unset();
        session_destroy();

        // Iniciar nova sessão para mensagem
        session_start();
        $_SESSION['success'] = 'Logout realizado com sucesso!';

        redirect('/login');
    }

    /**
     * Exibir página de perfil
     */
    public function showProfile() {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        if (!checkSessionTimeout()) {
            redirect('/login');
        }

        // Buscar dados atualizados do usuário
        $this->user->findById($_SESSION['user_id']);

        $data = [
            'title' => 'Meu Perfil - ' . APP_NAME,
            'user' => $this->user,
            'csrf_token' => generateCSRFToken()
        ];

        $this->loadView('auth/profile', $data);
    }

    /**
     * Atualizar perfil
     */
    public function updateProfile() {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profile');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/profile');
        }

        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');

        // Validar dados
        $validation_errors = User::validate([
            'name' => $name,
            'email' => $email
        ]);

        // Verificar se email já existe (excluindo o usuário atual)
        if ($this->user->emailExists($email, $_SESSION['user_id'])) {
            $validation_errors['email'] = 'Este email já está em uso';
        }

        // Se houver erros, retornar para o formulário
        if (!empty($validation_errors)) {
            $_SESSION['errors'] = $validation_errors;
            redirect('/profile');
        }

        // Atualizar usuário
        $this->user->id = $_SESSION['user_id'];
        $this->user->name = $name;
        $this->user->email = $email;

        if ($this->user->update()) {
            // Atualizar dados da sessão
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            $_SESSION['success'] = 'Perfil atualizado com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao atualizar perfil. Tente novamente.';
        }

        redirect('/profile');
    }

    /**
     * Alterar senha
     */
    public function changePassword() {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profile');
        }

        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido';
            redirect('/profile');
        }

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validar campos obrigatórios
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = 'Todos os campos de senha são obrigatórios';
            redirect('/profile');
        }

        // Verificar se as novas senhas coincidem
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'As novas senhas não coincidem';
            redirect('/profile');
        }

        // Validar nova senha
        if (strlen($new_password) < MIN_PASSWORD_LENGTH) {
            $_SESSION['error'] = 'A nova senha deve ter pelo menos ' . MIN_PASSWORD_LENGTH . ' caracteres';
            redirect('/profile');
        }

        // Buscar usuário atual
        $this->user->findById($_SESSION['user_id']);

        // Verificar senha atual
        if (!$this->user->verifyPassword($current_password)) {
            $_SESSION['error'] = 'Senha atual incorreta';
            redirect('/profile');
        }

        // Atualizar senha
        if ($this->user->updatePassword($new_password)) {
            $_SESSION['success'] = 'Senha alterada com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao alterar senha. Tente novamente.';
        }

        redirect('/profile');
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

    /**
     * Middleware para verificar autenticação
     */
    public static function requireAuth() {
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Você precisa estar logado para acessar esta página';
            redirect('/login');
        }

        if (!checkSessionTimeout()) {
            $_SESSION['error'] = 'Sua sessão expirou. Faça login novamente.';
            redirect('/login');
        }
    }
}

?>
