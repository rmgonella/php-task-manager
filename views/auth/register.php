<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="card-title">
                        <span class="brand-icon">📝</span>
                        Criar Conta
                    </h3>
                    <p class="text-muted">Registre-se no <?php echo APP_NAME; ?> e comece a organizar suas tarefas</p>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="/register" id="register-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input 
                                type="text" 
                                class="form-control <?php echo isset($_SESSION['errors']['name']) ? 'is-invalid' : ''; ?>" 
                                id="name" 
                                name="name" 
                                value="<?php echo isset($_SESSION['old_data']['name']) ? htmlspecialchars($_SESSION['old_data']['name']) : ''; ?>"
                                required 
                                autofocus
                                placeholder="Seu nome completo"
                                minlength="2"
                                maxlength="100"
                            >
                            <?php if (isset($_SESSION['errors']['name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($_SESSION['errors']['name']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Mínimo 2 caracteres, máximo 100</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control <?php echo isset($_SESSION['errors']['email']) ? 'is-invalid' : ''; ?>" 
                                id="email" 
                                name="email" 
                                value="<?php echo isset($_SESSION['old_data']['email']) ? htmlspecialchars($_SESSION['old_data']['email']) : ''; ?>"
                                required
                                placeholder="seu@email.com"
                                maxlength="150"
                            >
                            <?php if (isset($_SESSION['errors']['email'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($_SESSION['errors']['email']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Será usado para fazer login na sua conta</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
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
                                            minlength="6"
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
                                    <div class="form-text">Mínimo 6 caracteres</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">Confirmar Senha</label>
                                    <div class="password-input-group">
                                        <input 
                                            type="password" 
                                            class="form-control <?php echo isset($_SESSION['errors']['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                            id="confirm_password" 
                                            name="confirm_password" 
                                            required
                                            placeholder="Confirme sua senha"
                                        >
                                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                            👁️
                                        </button>
                                    </div>
                                    <?php if (isset($_SESSION['errors']['confirm_password'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo htmlspecialchars($_SESSION['errors']['confirm_password']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-text">Repita a senha acima</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Indicador de força da senha -->
                        <div class="password-strength" id="password-strength" style="display: none;">
                            <div class="strength-label">Força da senha:</div>
                            <div class="strength-bar">
                                <div class="strength-fill" id="strength-fill"></div>
                            </div>
                            <div class="strength-text" id="strength-text"></div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Eu concordo com os 
                                    <a href="/terms" target="_blank" class="text-primary">Termos de Uso</a> 
                                    e 
                                    <a href="/privacy" target="_blank" class="text-primary">Política de Privacidade</a>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Quero receber novidades e dicas por email (opcional)
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-block" id="submit-btn">
                                <span class="btn-icon">🚀</span>
                                Criar Conta
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-center">
                    <p class="mb-0">
                        Já tem uma conta? 
                        <a href="/login" class="text-primary font-weight-bold">Faça login aqui</a>
                    </p>
                </div>
            </div>
            
            <!-- Benefícios do sistema -->
            <div class="benefits-section mt-4">
                <h5 class="text-center text-muted mb-3">Por que usar o <?php echo APP_NAME; ?>?</h5>
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="benefit-item">
                            <div class="benefit-icon">📋</div>
                            <h6>Organização</h6>
                            <p class="small text-muted">Mantenha todas suas tarefas organizadas em um só lugar</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="benefit-item">
                            <div class="benefit-icon">🔒</div>
                            <h6>Segurança</h6>
                            <p class="small text-muted">Seus dados protegidos com criptografia avançada</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="benefit-item">
                            <div class="benefit-icon">📱</div>
                            <h6>Acessibilidade</h6>
                            <p class="small text-muted">Acesse de qualquer dispositivo, a qualquer hora</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.password-strength {
    margin-bottom: 1rem;
    padding: 0.5rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
}

.strength-label {
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.strength-bar {
    height: 6px;
    background: var(--border-color);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.strength-fill {
    height: 100%;
    transition: width 0.3s ease, background-color 0.3s ease;
    border-radius: 3px;
}

.strength-text {
    font-size: 0.75rem;
    font-weight: 500;
}

.benefits-section {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1.5rem;
}

.benefit-item {
    padding: 1rem;
}

.benefit-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.benefit-item h6 {
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

@media (max-width: 768px) {
    .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
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

// Verificar força da senha
function checkPasswordStrength(password) {
    let score = 0;
    let feedback = [];
    
    // Comprimento
    if (password.length >= 8) score += 1;
    else feedback.push('pelo menos 8 caracteres');
    
    // Letras minúsculas
    if (/[a-z]/.test(password)) score += 1;
    else feedback.push('letras minúsculas');
    
    // Letras maiúsculas
    if (/[A-Z]/.test(password)) score += 1;
    else feedback.push('letras maiúsculas');
    
    // Números
    if (/\d/.test(password)) score += 1;
    else feedback.push('números');
    
    // Caracteres especiais
    if (/[^A-Za-z0-9]/.test(password)) score += 1;
    else feedback.push('caracteres especiais');
    
    return { score, feedback };
}

function updatePasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthDiv = document.getElementById('password-strength');
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');
    
    if (password.length === 0) {
        strengthDiv.style.display = 'none';
        return;
    }
    
    strengthDiv.style.display = 'block';
    
    const { score, feedback } = checkPasswordStrength(password);
    const percentage = (score / 5) * 100;
    
    strengthFill.style.width = percentage + '%';
    
    let color, text;
    if (score <= 2) {
        color = '#dc3545';
        text = 'Fraca';
    } else if (score <= 3) {
        color = '#ffc107';
        text = 'Média';
    } else if (score <= 4) {
        color = '#28a745';
        text = 'Forte';
    } else {
        color = '#007bff';
        text = 'Muito Forte';
    }
    
    strengthFill.style.backgroundColor = color;
    strengthText.style.color = color;
    strengthText.textContent = text;
    
    if (feedback.length > 0 && score < 4) {
        strengthText.textContent += ' (adicione: ' + feedback.slice(0, 2).join(', ') + ')';
    }
}

// Validação em tempo real
document.getElementById('password').addEventListener('input', function() {
    updatePasswordStrength();
    validatePasswordMatch();
});

document.getElementById('confirm_password').addEventListener('input', validatePasswordMatch);

function validatePasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const confirmField = document.getElementById('confirm_password');
    
    if (confirmPassword.length === 0) {
        confirmField.classList.remove('is-valid', 'is-invalid');
        return;
    }
    
    if (password === confirmPassword) {
        confirmField.classList.remove('is-invalid');
        confirmField.classList.add('is-valid');
    } else {
        confirmField.classList.remove('is-valid');
        confirmField.classList.add('is-invalid');
    }
}

// Validação do formulário
document.getElementById('register-form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const terms = document.getElementById('terms').checked;
    
    let hasErrors = false;
    
    // Validar nome
    if (name.length < 2) {
        showNotification('Nome deve ter pelo menos 2 caracteres.', 'warning');
        hasErrors = true;
    }
    
    // Validar email
    if (!isValidEmail(email)) {
        showNotification('Por favor, insira um email válido.', 'warning');
        hasErrors = true;
    }
    
    // Validar senha
    if (password.length < 6) {
        showNotification('Senha deve ter pelo menos 6 caracteres.', 'warning');
        hasErrors = true;
    }
    
    // Validar confirmação de senha
    if (password !== confirmPassword) {
        showNotification('As senhas não coincidem.', 'warning');
        hasErrors = true;
    }
    
    // Validar termos
    if (!terms) {
        showNotification('Você deve concordar com os Termos de Uso.', 'warning');
        hasErrors = true;
    }
    
    if (hasErrors) {
        e.preventDefault();
        return;
    }
    
    // Mostrar loading
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="btn-icon">⏳</span>Criando conta...';
    submitBtn.disabled = true;
    
    // Restaurar botão em caso de erro (será redirecionado se sucesso)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});

// Validação de email em tempo real
document.getElementById('email').addEventListener('blur', function() {
    const email = this.value.trim();
    if (email && !isValidEmail(email)) {
        this.classList.add('is-invalid');
    } else if (email) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    }
});

// Validação de nome em tempo real
document.getElementById('name').addEventListener('blur', function() {
    const name = this.value.trim();
    if (name.length < 2) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    }
});
</script>

<?php
// Limpar dados antigos da sessão
unset($_SESSION['errors']);
unset($_SESSION['old_data']);
?>
