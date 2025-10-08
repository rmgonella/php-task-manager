<?php

/**
 * Router - Sistema de roteamento
 * PHP Task Manager - Sistema de Gerenciamento de Tarefas
 */

class Router {
    private $routes = [];
    private $middlewares = [];

    /**
     * Adicionar rota GET
     */
    public function get($path, $handler, $middleware = null) {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Adicionar rota POST
     */
    public function post($path, $handler, $middleware = null) {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Adicionar rota PUT
     */
    public function put($path, $handler, $middleware = null) {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    /**
     * Adicionar rota DELETE
     */
    public function delete($path, $handler, $middleware = null) {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    /**
     * Adicionar qualquer tipo de rota
     */
    public function addRoute($method, $path, $handler, $middleware = null) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * Adicionar middleware global
     */
    public function middleware($middleware) {
        $this->middlewares[] = $middleware;
    }

    /**
     * Processar requisição
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover trailing slash (exceto para root)
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        // Executar middlewares globais
        foreach ($this->middlewares as $middleware) {
            $this->executeMiddleware($middleware);
        }

        // Buscar rota correspondente
        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $pattern = $this->convertToRegex($route['path']);
                
                if (preg_match($pattern, $uri, $matches)) {
                    // Remover o primeiro elemento (match completo)
                    array_shift($matches);
                    
                    // Executar middleware específico da rota
                    if ($route['middleware']) {
                        $this->executeMiddleware($route['middleware']);
                    }
                    
                    // Executar handler
                    $this->executeHandler($route['handler'], $matches);
                    return;
                }
            }
        }

        // Rota não encontrada
        $this->handleNotFound();
    }

    /**
     * Converter path para regex
     */
    private function convertToRegex($path) {
        // Escapar caracteres especiais
        $pattern = preg_quote($path, '/');
        
        // Converter parâmetros {id} para grupos de captura
        $pattern = preg_replace('/\\\{([^}]+)\\\}/', '([^/]+)', $pattern);
        
        // Adicionar delimitadores e âncoras
        return '/^' . $pattern . '$/';
    }

    /**
     * Executar middleware
     */
    private function executeMiddleware($middleware) {
        if (is_string($middleware)) {
            // Middleware como string (nome da classe::método)
            if (strpos($middleware, '::') !== false) {
                list($class, $method) = explode('::', $middleware);
                call_user_func([$class, $method]);
            } else {
                // Middleware como função
                call_user_func($middleware);
            }
        } elseif (is_callable($middleware)) {
            // Middleware como callable
            call_user_func($middleware);
        }
    }

    /**
     * Executar handler
     */
    private function executeHandler($handler, $params = []) {
        if (is_string($handler)) {
            // Handler como string (Controller@method)
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                
                // Incluir arquivo do controller se necessário
                $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                }
                
                // Instanciar controller e chamar método
                if (class_exists($controller)) {
                    $instance = new $controller();
                    if (method_exists($instance, $method)) {
                        call_user_func_array([$instance, $method], $params);
                    } else {
                        throw new Exception("Método {$method} não encontrado no controller {$controller}");
                    }
                } else {
                    throw new Exception("Controller {$controller} não encontrado");
                }
            } else {
                // Handler como função
                call_user_func_array($handler, $params);
            }
        } elseif (is_callable($handler)) {
            // Handler como callable
            call_user_func_array($handler, $params);
        } else {
            throw new Exception("Handler inválido");
        }
    }

    /**
     * Lidar com rota não encontrada
     */
    private function handleNotFound() {
        http_response_code(404);
        
        // Tentar carregar página de erro personalizada
        $errorFile = __DIR__ . '/../views/errors/404.php';
        if (file_exists($errorFile)) {
            $controller = new HomeController();
            $controller->notFound();
        } else {
            echo "<h1>404 - Página não encontrada</h1>";
            echo "<p>A página que você está procurando não existe.</p>";
            echo "<a href='/'>Voltar ao início</a>";
        }
    }

    /**
     * Gerar URL para rota nomeada
     */
    public function url($name, $params = []) {
        // Implementação básica - pode ser expandida
        $url = $name;
        
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }
        
        return $url;
    }

    /**
     * Redirecionar para URL
     */
    public function redirect($url, $code = 302) {
        http_response_code($code);
        header("Location: " . $url);
        exit();
    }

    /**
     * Obter parâmetro da URL
     */
    public function getParam($key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    /**
     * Obter dados POST
     */
    public function getPost($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Verificar se é requisição AJAX
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Verificar se é requisição POST
     */
    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verificar se é requisição GET
     */
    public function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Obter método da requisição
     */
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Obter URI da requisição
     */
    public function getUri() {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Obter query string
     */
    public function getQueryString() {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    }

    /**
     * Resposta JSON
     */
    public function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Definir cabeçalho HTTP
     */
    public function setHeader($name, $value) {
        header($name . ': ' . $value);
    }

    /**
     * Obter cabeçalho da requisição
     */
    public function getHeader($name, $default = null) {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$key] ?? $default;
    }

    /**
     * Validar token CSRF
     */
    public function validateCsrf($token = null) {
        if ($token === null) {
            $token = $this->getPost('csrf_token');
        }
        
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            throw new Exception("Token CSRF inválido");
        }
        
        return true;
    }

    /**
     * Middleware de autenticação
     */
    public static function authMiddleware() {
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Você precisa estar logado para acessar esta página';
            redirect('/login');
        }
        
        if (!checkSessionTimeout()) {
            $_SESSION['error'] = 'Sua sessão expirou. Faça login novamente.';
            redirect('/login');
        }
    }

    /**
     * Middleware de guest (não logado)
     */
    public static function guestMiddleware() {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
    }

    /**
     * Middleware de CORS
     */
    public static function corsMiddleware() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /**
     * Middleware de rate limiting básico
     */
    public static function rateLimitMiddleware($maxRequests = 60, $timeWindow = 60) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = 'rate_limit_' . $ip;
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'start_time' => time()
            ];
            return;
        }
        
        $data = $_SESSION[$key];
        $elapsed = time() - $data['start_time'];
        
        if ($elapsed > $timeWindow) {
            // Reset counter
            $_SESSION[$key] = [
                'count' => 1,
                'start_time' => time()
            ];
            return;
        }
        
        if ($data['count'] >= $maxRequests) {
            http_response_code(429);
            header('Retry-After: ' . ($timeWindow - $elapsed));
            echo json_encode([
                'error' => 'Muitas requisições. Tente novamente em ' . ($timeWindow - $elapsed) . ' segundos.'
            ]);
            exit();
        }
        
        $_SESSION[$key]['count']++;
    }
}

?>
