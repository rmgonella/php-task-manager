<div class="hero-section text-center">
    <div class="hero-content">
        <h1 class="hero-title">
            Organize Sua Vida com o <span class="text-primary"><?php echo APP_NAME; ?></span>
        </h1>
        <p class="hero-subtitle">
            O sistema de gerenciamento de tarefas simples e eficiente, construído com PHP e arquitetura MVC.
        </p>
        
        <div class="hero-actions">
            <?php if (!isLoggedIn()): ?>
                <a href="/register" class="btn btn-primary btn-lg">
                    <span class="btn-icon">🚀</span>
                    Comece Agora (Grátis)
                </a>
                <a href="/login" class="btn btn-secondary btn-lg">
                    <span class="btn-icon">🔑</span>
                    Já sou cliente
                </a>
            <?php else: ?>
                <a href="/dashboard" class="btn btn-success btn-lg">
                    <span class="btn-icon">🏠</span>
                    Ir para o Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="features-section">
    <h2 class="text-center mb-5">Por Que Escolher o <?php echo APP_NAME; ?>?</h2>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">📋</div>
            <h4 class="feature-title">Gerenciamento Completo</h4>
            <p class="feature-description">
                Crie, edite, visualize e exclua suas tarefas com facilidade. Mantenha o controle total sobre seus projetos.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h4 class="feature-title">Autenticação Segura</h4>
            <p class="feature-description">
                Registro e login protegidos com `password_hash()` e sessões seguras, garantindo a privacidade dos seus dados.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h4 class="feature-title">Filtros Inteligentes</h4>
            <p class="feature-description">
                Filtre suas tarefas por status (pendente/concluída) e prioridade (baixa/média/alta) para focar no que realmente importa.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">💻</div>
            <h4 class="feature-title">Arquitetura MVC</h4>
            <p class="feature-description">
                Código limpo e organizado, seguindo o padrão MVC, o que facilita a manutenção e o desenvolvimento futuro.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h4 class="feature-title">Design Responsivo</h4>
            <p class="feature-description">
                Interface simples e funcional, adaptável a qualquer dispositivo (desktop, tablet ou celular).
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">⚙️</div>
            <h4 class="feature-title">Tecnologia Sólida</h4>
            <p class="feature-description">
                Construído com PHP e MySQL, utilizando PDO para acesso seguro e eficiente ao banco de dados.
            </p>
        </div>
    </div>
</div>

<div class="cta-section text-center">
    <h2>Pronto para Organizar Suas Tarefas?</h2>
    <p class="cta-subtitle">
        Junte-se a centenas de usuários que já estão aproveitando a simplicidade do <?php echo APP_NAME; ?>.
    </p>
    <?php if (!isLoggedIn()): ?>
        <a href="/register" class="btn btn-success btn-lg">
            <span class="btn-icon">✅</span>
            Criar Minha Conta Grátis
        </a>
    <?php endif; ?>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: var(--white);
    padding: 6rem 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 3rem;
    box-shadow: var(--shadow-lg);
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--white);
}

.hero-subtitle {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    color: rgba(255, 255, 255, 0.9);
}

.hero-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.hero-actions .btn-secondary {
    background-color: var(--white);
    color: var(--primary-color);
    border-color: var(--white);
}

.hero-actions .btn-secondary:hover {
    background-color: var(--light-color);
    color: var(--primary-dark);
}

.features-section {
    padding: 3rem 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    text-align: center;
    transition: var(--transition);
    border-top: 4px solid var(--primary-color);
}

.feature-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
}

.feature-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.feature-title {
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
}

.feature-description {
    color: var(--secondary-color);
}

.cta-section {
    background-color: var(--light-color);
    padding: 4rem 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 3rem;
}

.cta-section h2 {
    font-size: 2rem;
    color: var(--dark-color);
}

.cta-subtitle {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    color: var(--secondary-color);
}

@media (max-width: 768px) {
    .hero-section {
        padding: 4rem 1rem;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-actions {
        flex-direction: column;
    }
    
    .hero-actions .btn-lg {
        width: 100%;
    }
}
</style>
