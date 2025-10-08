        </div> <!-- Fim do page-content -->
    </main> <!-- Fim do main-content -->

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title"><?php echo APP_NAME; ?></h3>
                    <p class="footer-description">
                        Sistema completo de gerenciamento de tarefas desenvolvido em PHP com MySQL.
                        Organize suas atividades de forma simples e eficiente.
                    </p>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Links Úteis</h4>
                    <ul class="footer-links">
                        <li><a href="/" class="footer-link">Início</a></li>
                        <li><a href="/about" class="footer-link">Sobre</a></li>
                        <li><a href="/contact" class="footer-link">Contato</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="/dashboard" class="footer-link">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="/login" class="footer-link">Entrar</a></li>
                            <li><a href="/register" class="footer-link">Registrar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Recursos</h4>
                    <ul class="footer-links">
                        <li><span class="footer-feature">✅ Gerenciamento de Tarefas</span></li>
                        <li><span class="footer-feature">🔐 Autenticação Segura</span></li>
                        <li><span class="footer-feature">📊 Filtros e Estatísticas</span></li>
                        <li><span class="footer-feature">📱 Interface Responsiva</span></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Informações</h4>
                    <ul class="footer-info">
                        <li><strong>Versão:</strong> <?php echo APP_VERSION; ?></li>
                        <li><strong>Tecnologia:</strong> PHP + MySQL</li>
                        <li><strong>Arquitetura:</strong> MVC</li>
                        <?php if (isLoggedIn()): ?>
                            <li><strong>Usuário:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos os direitos reservados.</p>
                    <p class="footer-tech">
                        Desenvolvido com ❤️ usando PHP, MySQL e arquitetura MVC
                    </p>
                </div>
                
                <div class="footer-stats">
                    <?php if (isLoggedIn()): ?>
                        <?php
                        // Buscar estatísticas rápidas do usuário
                        $user = new User();
                        $user->findById($_SESSION['user_id']);
                        $stats = $user->getStats();
                        ?>
                        <div class="quick-stats">
                            <span class="stat-item">
                                <strong><?php echo $stats['total_tasks']; ?></strong> tarefas
                            </span>
                            <span class="stat-separator">|</span>
                            <span class="stat-item">
                                <strong><?php echo $stats['pending_tasks']; ?></strong> pendentes
                            </span>
                            <span class="stat-separator">|</span>
                            <span class="stat-item">
                                <strong><?php echo $stats['completed_tasks']; ?></strong> concluídas
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
    
    <!-- JavaScript adicional para páginas específicas -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo htmlspecialchars($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Scripts inline (se definidos) -->
    <?php if (isset($inline_scripts)): ?>
        <script>
            <?php echo $inline_scripts; ?>
        </script>
    <?php endif; ?>

    <!-- Google Analytics ou outros scripts de tracking (se configurados) -->
    <?php if (defined('GOOGLE_ANALYTICS_ID') && GOOGLE_ANALYTICS_ID): ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo GOOGLE_ANALYTICS_ID; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo GOOGLE_ANALYTICS_ID; ?>');
        </script>
    <?php endif; ?>

    <!-- Service Worker para PWA (se configurado) -->
    <?php if (defined('ENABLE_PWA') && ENABLE_PWA): ?>
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('ServiceWorker registration successful');
                        })
                        .catch(function(err) {
                            console.log('ServiceWorker registration failed');
                        });
                });
            }
        </script>
    <?php endif; ?>

    <!-- Verificação de sessão automática (para usuários logados) -->
    <?php if (isLoggedIn()): ?>
        <script>
            // Verificar sessão a cada 5 minutos
            setInterval(function() {
                fetch('/api/check-session')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.valid) {
                            alert('Sua sessão expirou. Você será redirecionado para a página de login.');
                            window.location.href = '/login';
                        }
                    })
                    .catch(error => {
                        console.log('Erro ao verificar sessão:', error);
                    });
            }, 300000); // 5 minutos
        </script>
    <?php endif; ?>

</body>
</html>
