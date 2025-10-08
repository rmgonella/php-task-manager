<?php

/**
 * Configurações gerais da aplicação
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

// Iniciar sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurações de erro
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações da aplicação
define('APP_NAME', 'PHP Task Manager');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/php-task-manager');

// Configurações de segurança
define('HASH_ALGO', PASSWORD_DEFAULT);
define('SESSION_TIMEOUT', 3600); // 1 hora em segundos

// Configurações de paginação
define('TASKS_PER_PAGE', 10);

// Status das tarefas
define('TASK_STATUS_PENDING', 'pendente');
define('TASK_STATUS_COMPLETED', 'concluida');

// Configurações de validação
define('MIN_PASSWORD_LENGTH', 6);
define('MAX_TITLE_LENGTH', 255);
define('MAX_DESCRIPTION_LENGTH', 1000);

// Função para autoload das classes
spl_autoload_register(function ($class_name) {
    $directories = [
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../app/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Função para verificar se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Função para verificar timeout da sessão
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            return false;
        }
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Função para redirecionar
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Função para sanitizar entrada
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Função para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Função para gerar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Função para verificar token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>
