<?php
http_response_code(404);
?>

<div class="error-container text-center">
    <div class="error-icon">❓</div>
    <h1 class="error-title">404 - Página Não Encontrada</h1>
    <p class="error-message">
        A página que você está procurando não existe ou foi movida.
    </p>
    <p class="error-tip">
        Verifique o endereço digitado ou volte para a página inicial.
    </p>
    
    <div class="error-actions">
        <a href="/" class="btn btn-primary btn-lg">
            <span class="btn-icon">🏠</span>
            Ir para a Página Inicial
        </a>
        <a href="/dashboard" class="btn btn-secondary btn-lg">
            <span class="btn-icon">📋</span>
            Ir para o Dashboard
        </a>
    </div>
</div>

<style>
.error-container {
    padding: 4rem 1rem;
    max-width: 800px;
    margin: 0 auto;
}

.error-icon {
    font-size: 6rem;
    margin-bottom: 1rem;
    color: var(--warning-color);
}

.error-title {
    font-size: 3rem;
    color: var(--dark-color);
    margin-bottom: 1rem;
}

.error-message {
    font-size: 1.25rem;
    color: var(--secondary-color);
    margin-bottom: 1.5rem;
}

.error-tip {
    font-size: 1rem;
    color: var(--primary-color);
    font-weight: 500;
    margin-bottom: 2rem;
}

.error-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .error-title {
        font-size: 2.5rem;
    }
    
    .error-actions {
        flex-direction: column;
    }
    
    .error-actions .btn-lg {
        width: 100%;
    }
}
</style>
