<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : APP_NAME; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- Meta tags -->
    <meta name="description" content="Sistema de gerenciamento de tarefas desenvolvido em PHP com MySQL">
    <meta name="author" content="PHP Task Manager">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo isset($title) ? htmlspecialchars($title) : APP_NAME; ?>">
    <meta property="og:description" content="Sistema de gerenciamento de tarefas desenvolvido em PHP com MySQL">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo APP_URL; ?>">
    
    <!-- CSS adicional para páginas específicas -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navegação -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/" class="brand-link">
                    <span class="brand-icon">📋</span>
                    <span class="brand-text"><?php echo APP_NAME; ?></span>
                </a>
            </div>
            
            <div class="nav-menu" id="nav-menu">
                <?php if (isLoggedIn()): ?>
                    <!-- Menu para usuários logados -->
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="/dashboard" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : ''; ?>">
                                <span class="nav-icon">🏠</span>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/tasks/create" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/tasks/create') !== false) ? 'active' : ''; ?>">
                                <span class="nav-icon">➕</span>
                                Nova Tarefa
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="user-dropdown">
                                <span class="nav-icon">👤</span>
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                <span class="dropdown-arrow">▼</span>
                            </a>
                            <ul class="dropdown-menu" id="user-dropdown-menu">
                                <li><a href="/profile" class="dropdown-link">Meu Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="/logout" class="logout-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <button type="submit" class="dropdown-link logout-btn">Sair</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <!-- Menu para visitantes -->
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="/" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/') ? 'active' : ''; ?>">
                                Início
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/about" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/about') !== false) ? 'active' : ''; ?>">
                                Sobre
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/contact" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/contact') !== false) ? 'active' : ''; ?>">
                                Contato
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/login" class="nav-link btn-primary <?php echo (strpos($_SERVER['REQUEST_URI'], '/login') !== false) ? 'active' : ''; ?>">
                                Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/register" class="nav-link btn-secondary <?php echo (strpos($_SERVER['REQUEST_URI'], '/register') !== false) ? 'active' : ''; ?>">
                                Registrar
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Botão do menu mobile -->
            <div class="nav-toggle" id="nav-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <!-- Container principal -->
    <main class="main-content">
        <!-- Mensagens de feedback -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" id="success-alert">
                <span class="alert-icon">✅</span>
                <span class="alert-message"><?php echo htmlspecialchars($_SESSION['success']); ?></span>
                <button type="button" class="alert-close" onclick="closeAlert('success-alert')">&times;</button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error" id="error-alert">
                <span class="alert-icon">❌</span>
                <span class="alert-message"><?php echo htmlspecialchars($_SESSION['error']); ?></span>
                <button type="button" class="alert-close" onclick="closeAlert('error-alert')">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning" id="warning-alert">
                <span class="alert-icon">⚠️</span>
                <span class="alert-message"><?php echo htmlspecialchars($_SESSION['warning']); ?></span>
                <button type="button" class="alert-close" onclick="closeAlert('warning-alert')">&times;</button>
            </div>
            <?php unset($_SESSION['warning']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['info'])): ?>
            <div class="alert alert-info" id="info-alert">
                <span class="alert-icon">ℹ️</span>
                <span class="alert-message"><?php echo htmlspecialchars($_SESSION['info']); ?></span>
                <button type="button" class="alert-close" onclick="closeAlert('info-alert')">&times;</button>
            </div>
            <?php unset($_SESSION['info']); ?>
        <?php endif; ?>

        <!-- Breadcrumb (se definido) -->
        <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
            <nav class="breadcrumb">
                <ol class="breadcrumb-list">
                    <?php foreach ($breadcrumb as $index => $item): ?>
                        <li class="breadcrumb-item <?php echo ($index === count($breadcrumb) - 1) ? 'active' : ''; ?>">
                            <?php if (isset($item['url']) && $index !== count($breadcrumb) - 1): ?>
                                <a href="<?php echo htmlspecialchars($item['url']); ?>" class="breadcrumb-link">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($item['title']); ?>
                            <?php endif; ?>
                        </li>
                        <?php if ($index < count($breadcrumb) - 1): ?>
                            <li class="breadcrumb-separator">›</li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>

        <!-- Conteúdo da página -->
        <div class="page-content">
