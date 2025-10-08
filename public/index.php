<?php

/**
 * Arquivo principal da aplicação
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 * 
 * Este arquivo é o ponto de entrada de todas as requisições
 * e gerencia o roteamento da aplicação.
 */

// Definir constantes de ambiente
define('DEBUG', true); // Alterar para false em produção
define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_ROOT', __DIR__);

// Configurar timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurar exibição de erros baseado no ambiente
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', APP_ROOT . '/logs/error.log');
}

// Verificar se o diretório de logs existe
$logDir = APP_ROOT . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Iniciar sessão
session_start();

// Incluir configurações
require_once APP_ROOT . '/config/config.php';

// Verificar se é uma requisição para arquivo estático
$requestUri = $_SERVER['REQUEST_URI'];
$publicPath = parse_url($requestUri, PHP_URL_PATH);

// Servir arquivos estáticos diretamente
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $publicPath)) {
    $filePath = PUBLIC_ROOT . $publicPath;
    
    if (file_exists($filePath)) {
        // Definir tipo MIME
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        // Definir cabeçalhos de cache
        $maxAge = 86400; // 1 dia
        header('Content-Type: ' . $mimeType);
        header('Cache-Control: public, max-age=' . $maxAge);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT');
        
        // Verificar If-Modified-Since
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            $fileModTime = filemtime($filePath);
            
            if ($fileModTime <= $ifModifiedSince) {
                http_response_code(304);
                exit();
            }
        }
        
        // Servir arquivo
        readfile($filePath);
        exit();
    } else {
        http_response_code(404);
        echo "Arquivo não encontrado: " . htmlspecialchars($publicPath);
        exit();
    }
}

// Verificar conexão com banco de dados
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Teste simples de conexão
    $stmt = $conn->query("SELECT 1");
    
} catch (Exception $e) {
    error_log("Erro de conexão com banco de dados: " . $e->getMessage());
    
    if (DEBUG) {
        echo "<h1>Erro de Conexão com Banco de Dados</h1>";
        echo "<p>Não foi possível conectar ao banco de dados MySQL.</p>";
        echo "<p><strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<h3>Verificações necessárias:</h3>";
        echo "<ul>";
        echo "<li>Verifique se o MySQL está rodando</li>";
        echo "<li>Confirme as credenciais em config/database.php</li>";
        echo "<li>Certifique-se de que o banco 'php_task_manager' existe</li>";
        echo "<li>Execute o script database.sql para criar as tabelas</li>";
        echo "</ul>";
    } else {
        echo "<h1>Erro Interno do Servidor</h1>";
        echo "<p>Ocorreu um erro interno. Tente novamente mais tarde.</p>";
    }
    exit();
}

// Verificar se as tabelas existem
try {
    $stmt = $conn->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() === 0) {
        throw new Exception("Tabela 'users' não encontrada");
    }
    
    $stmt = $conn->query("SHOW TABLES LIKE 'tasks'");
    if ($stmt->rowCount() === 0) {
        throw new Exception("Tabela 'tasks' não encontrada");
    }
    
} catch (Exception $e) {
    error_log("Erro de estrutura do banco: " . $e->getMessage());
    
    if (DEBUG) {
        echo "<h1>Erro de Estrutura do Banco</h1>";
        echo "<p>As tabelas necessárias não foram encontradas.</p>";
        echo "<p><strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<h3>Solução:</h3>";
        echo "<p>Execute o arquivo <code>database.sql</code> no seu banco MySQL:</p>";
        echo "<pre>mysql -u root -p php_task_manager < database.sql</pre>";
    } else {
        echo "<h1>Sistema em Manutenção</h1>";
        echo "<p>O sistema está temporariamente indisponível para manutenção.</p>";
    }
    exit();
}

// Verificar timeout de sessão para usuários logados
if (isLoggedIn()) {
    if (!checkSessionTimeout()) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['warning'] = 'Sua sessão expirou por inatividade. Faça login novamente.';
    }
}

// Log de acesso (apenas em debug)
if (DEBUG) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'];
    if (isset($_SESSION['user_id'])) {
        $logMessage .= " - User: " . $_SESSION['user_id'];
    }
    $logMessage .= " - IP: " . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
    error_log($logMessage . PHP_EOL, 3, $logDir . '/access.log');
}

// Incluir e executar roteamento
try {
    require_once APP_ROOT . '/app/routes.php';
    
} catch (Exception $e) {
    error_log("Erro fatal na aplicação: " . $e->getMessage());
    
    if (DEBUG) {
        echo "<h1>Erro Fatal</h1>";
        echo "<p><strong>Mensagem:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Arquivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Linha:</strong> " . $e->getLine() . "</p>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        echo "<h1>Erro Interno do Servidor</h1>";
        echo "<p>Ocorreu um erro interno. Nossa equipe foi notificada.</p>";
        echo "<p><a href='/'>Voltar ao início</a></p>";
    }
}

// Função para shutdown
register_shutdown_function(function() {
    $error = error_get_last();
    
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        error_log("Erro fatal: " . $error['message'] . " em " . $error['file'] . ":" . $error['line']);
        
        if (!DEBUG) {
            // Limpar output buffer se houver
            if (ob_get_level()) {
                ob_clean();
            }
            
            http_response_code(500);
            echo "<h1>Erro Interno do Servidor</h1>";
            echo "<p>Ocorreu um erro interno. Tente novamente mais tarde.</p>";
        }
    }
});

// Função para limpeza de sessões antigas (executar ocasionalmente)
if (rand(1, 100) === 1) { // 1% de chance
    $sessionPath = session_save_path();
    if ($sessionPath && is_dir($sessionPath)) {
        $files = glob($sessionPath . '/sess_*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > SESSION_TIMEOUT) {
                unlink($file);
            }
        }
    }
}

?>
