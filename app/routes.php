<?php

/**
 * Definição de rotas da aplicação
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

// Incluir dependências
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/../config/config.php';

// Criar instância do router
$router = new Router();

// Middleware global para rate limiting
$router->middleware('Router::rateLimitMiddleware');

// ==========================================
// ROTAS PÚBLICAS (sem autenticação)
// ==========================================

// Página inicial
$router->get('/', 'HomeController@index');

// Páginas informativas
$router->get('/about', 'HomeController@about');
$router->get('/contact', 'HomeController@contact');
$router->post('/contact', 'HomeController@sendContact');

// Autenticação - Login
$router->get('/login', 'AuthController@showLogin', 'Router::guestMiddleware');
$router->post('/login', 'AuthController@login', 'Router::guestMiddleware');

// Autenticação - Registro
$router->get('/register', 'AuthController@showRegister', 'Router::guestMiddleware');
$router->post('/register', 'AuthController@register', 'Router::guestMiddleware');

// Logout
$router->post('/logout', 'AuthController@logout');

// Health check
$router->get('/health', 'HomeController@health');

// ==========================================
// ROTAS PROTEGIDAS (com autenticação)
// ==========================================

// Dashboard
$router->get('/dashboard', 'TaskController@dashboard', 'Router::authMiddleware');

// Perfil do usuário
$router->get('/profile', 'AuthController@showProfile', 'Router::authMiddleware');
$router->post('/profile', 'AuthController@updateProfile', 'Router::authMiddleware');
$router->post('/profile/password', 'AuthController@changePassword', 'Router::authMiddleware');

// ==========================================
// ROTAS DE TAREFAS (CRUD)
// ==========================================

// Criar tarefa
$router->get('/tasks/create', 'TaskController@create', 'Router::authMiddleware');
$router->post('/tasks/create', 'TaskController@store', 'Router::authMiddleware');

// Visualizar tarefa
$router->get('/tasks/{id}', 'TaskController@show', 'Router::authMiddleware');

// Editar tarefa
$router->get('/tasks/{id}/edit', 'TaskController@edit', 'Router::authMiddleware');
$router->post('/tasks/{id}/edit', 'TaskController@update', 'Router::authMiddleware');

// Excluir tarefa
$router->post('/tasks/{id}/delete', 'TaskController@delete', 'Router::authMiddleware');

// Marcar como concluída
$router->post('/tasks/{id}/complete', 'TaskController@complete', 'Router::authMiddleware');

// Reabrir tarefa
$router->post('/tasks/{id}/reopen', 'TaskController@reopen', 'Router::authMiddleware');

// ==========================================
// API ROUTES (JSON)
// ==========================================

// API - Listar tarefas
$router->get('/api/tasks', 'TaskController@apiList', 'Router::authMiddleware');

// API - Estatísticas do usuário
$router->get('/api/stats', function() {
    $user = new User();
    $user->findById($_SESSION['user_id']);
    $stats = $user->getStats();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
}, 'Router::authMiddleware');

// API - Verificar sessão
$router->get('/api/check-session', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'valid' => isLoggedIn() && checkSessionTimeout()
    ]);
});

// API - Buscar tarefas
$router->get('/api/search', function() {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autorizado']);
        return;
    }
    
    $query = $_GET['q'] ?? '';
    $status = $_GET['status'] ?? '';
    
    if (strlen($query) < 2) {
        echo json_encode(['results' => []]);
        return;
    }
    
    $task = new Task();
    $user_id = $_SESSION['user_id'];
    
    // Busca simples por título
    $sql = "SELECT id, title, status, priority, due_date 
            FROM tasks 
            WHERE user_id = :user_id 
            AND title LIKE :query";
    
    if ($status) {
        $sql .= " AND status = :status";
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT 10";
    
    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindValue(':query', '%' . $query . '%');
    
    if ($status) {
        $stmt->bindParam(':status', $status);
    }
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['results' => $results]);
}, 'Router::authMiddleware');

// ==========================================
// ROTAS DE ERRO
// ==========================================

// Página de erro 404
$router->get('/404', 'HomeController@notFound');

// Página de erro 500
$router->get('/500', 'HomeController@serverError');

// Página de manutenção
$router->get('/maintenance', 'HomeController@maintenance');

// ==========================================
// ROTAS DE DESENVOLVIMENTO (apenas em debug)
// ==========================================

if (defined('DEBUG') && DEBUG) {
    // Informações do sistema
    $router->get('/info', 'HomeController@info');
    
    // Teste de email
    $router->get('/test/email', function() {
        echo "<h1>Teste de Email</h1>";
        echo "<p>Funcionalidade de teste disponível apenas em modo de desenvolvimento.</p>";
    });
    
    // Limpar sessão
    $router->get('/test/clear-session', function() {
        session_unset();
        session_destroy();
        echo "<h1>Sessão Limpa</h1>";
        echo "<p><a href='/'>Voltar ao início</a></p>";
    });
}

// ==========================================
// ROTAS DE REDIRECIONAMENTO
// ==========================================

// Redirecionamentos para compatibilidade
$router->get('/home', function() {
    redirect('/');
});

$router->get('/tasks', function() {
    redirect('/dashboard');
});

$router->get('/user/profile', function() {
    redirect('/profile');
});

// ==========================================
// MIDDLEWARE PERSONALIZADO
// ==========================================

// Middleware para verificar se a tarefa pertence ao usuário
function taskOwnershipMiddleware($taskId) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    
    $task = new Task();
    if (!$task->belongsToUser($taskId, $_SESSION['user_id'])) {
        $_SESSION['error'] = 'Acesso negado';
        redirect('/dashboard');
    }
}

// ==========================================
// TRATAMENTO DE ERROS
// ==========================================

// Definir handler de erro personalizado
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    error_log("Erro PHP: $message em $file:$line");
    
    if (defined('DEBUG') && DEBUG) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 1rem; margin: 1rem; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<strong>Erro:</strong> $message<br>";
        echo "<strong>Arquivo:</strong> $file<br>";
        echo "<strong>Linha:</strong> $line";
        echo "</div>";
    }
    
    return true;
});

// Definir handler de exceção personalizado
set_exception_handler(function($exception) {
    error_log("Exceção não capturada: " . $exception->getMessage());
    
    if (defined('DEBUG') && DEBUG) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 1rem; margin: 1rem; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<strong>Exceção:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>Arquivo:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Linha:</strong> " . $exception->getLine() . "<br>";
        echo "<strong>Stack Trace:</strong><pre>" . $exception->getTraceAsString() . "</pre>";
        echo "</div>";
    } else {
        http_response_code(500);
        include __DIR__ . '/../views/errors/500.php';
    }
});

// ==========================================
// EXECUTAR ROTEAMENTO
// ==========================================

// Processar a requisição
try {
    $router->dispatch();
} catch (Exception $e) {
    error_log("Erro no roteamento: " . $e->getMessage());
    
    if (defined('DEBUG') && DEBUG) {
        echo "<h1>Erro no Roteamento</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        $controller = new HomeController();
        $controller->serverError();
    }
}

?>
