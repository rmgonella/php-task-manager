<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="card-title">
                        <span class="brand-icon">🔐</span>
                        Entrar
                    </h3>
                    <p class="text-muted">Acesse sua conta no <?php echo APP_NAME; ?></p>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="/login" id="login-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control <?php echo isset($_SESSION['errors']['email']) ? 'is-invalid' : ''; ?>" 
                                id="email" 
                                name="email" 
                                value="<?php echo isset($_SESSION['old_data']['email']) ? htmlspecialchars($_SESSION['old_data']['email']) : ''; ?>"
                                required 
                                autofocus
                                placeholder="seu@email.com"
                            >
                            <?php if (isset($_SESSION['errors']['email'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($_SESSION['errors']['email']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Senha</label>
                            <div class="password-input-group">
                                <input 
                                    type="password" 
                                    class="form-control <?php echo isset($_SESSION['errors']['password']) ? 'is-invalid' : ''; ?>" 
                                    id="password" 
                                    name="password" 
                                    required
                                    placeholder="Sua senha"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    👁️
                                </button>
                            </div>
                            <?php if (isset($_SESSION['errors']['password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($_SESSION['errors']['password']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Lembrar de mim
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-block">
                                <span class="btn-icon">🚀</span>
                                Entrar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-center">
                    <p class="mb-2">
                        <a href="/forgot-password" class="text-primary">Esqueceu sua senha?</a>
                    </p>
                    <p class="mb-0">
                        Não tem uma conta? 
                        <a href="/register" class="text-primary font-weight-bold">Registre-se aqui</a>
                    </p>
                </div>
            </div>
            
            <!-- Informações adicionais -->
            <div class="text-center mt-4">
                <div class="demo-info">
                    <h6 class="text-muted">Conta de Demonstração</h6>
                    <p class="small text-muted">
                        <strong>Email:</strong> teste@exemplo.com<br>
                        <strong>Senha:</strong> 123456
                    </p>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fillDemoCredentials()">
                        Usar Credenciais de Demo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.justify-content-center {
    justify-content: center;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 0 15px;
}

.col-lg-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
}

.password-input-group {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    padding: 0;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.password-toggle:hover {
    opacity: 1;
}

.form-check {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.form-check-input {
    margin-right: 0.5rem;
}

.form-check-label {
    margin-bottom: 0;
    cursor: pointer;
}

.btn-icon {
    margin-right: 0.5rem;
}

.demo-info {
    background: var(--light-color);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-top: 1rem;
}

.font-weight-bold {
    font-weight: 600;
}

.small {
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .container {
        padding: 1rem;
    }
}

@media (max-width: 992px) {
    .col-lg-4 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}
</style>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        button.textContent = '🙈';
    } else {
        field.type = 'password';
        button.textContent = '👁️';
    }
}

function fillDemoCredentials() {
    document.getElementById('email').value = 'teste@exemplo.com';
    document.getElementById('password').value = '123456';
    
    // Mostrar feedback visual
    showNotification('Credenciais de demonstração preenchidas!', 'info');
}

// Validação adicional do formulário
document.getElementById('login-form').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        showNotification('Por favor, preencha todos os campos.', 'warning');
        return;
    }
    
    if (!isValidEmail(email)) {
        e.preventDefault();
        showNotification('Por favor, insira um email válido.', 'warning');
        return;
    }
    
    // Mostrar loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="btn-icon">⏳</span>Entrando...';
    submitBtn.disabled = true;
    
    // Restaurar botão em caso de erro (será redirecionado se sucesso)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

// Focar no campo de senha se email já estiver preenchido
document.addEventListener('DOMContentLoaded', function() {
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    
    if (emailField.value.trim() !== '') {
        passwordField.focus();
    }
});
</script>

<?php
// Limpar dados antigos da sessão
unset($_SESSION['errors']);
unset($_SESSION['old_data']);
?>
