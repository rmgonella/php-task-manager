<?php
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '/dashboard'],
    ['title' => 'Meu Perfil']
];

$errors = $_SESSION['errors'] ?? [];
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <h1 class="text-center mb-4">
                <span class="text-primary">👤</span>
                Meu Perfil
            </h1>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Informações Pessoais</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/profile" id="profile-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input 
                                type="text" 
                                class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                id="name" 
                                name="name" 
                                value="<?php echo htmlspecialchars($user->name); ?>"
                                required 
                                autofocus
                                maxlength="100"
                            >
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                id="email" 
                                name="email" 
                                value="<?php echo htmlspecialchars($user->email); ?>"
                                required
                                maxlength="150"
                            >
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['email']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Seu email de login.</div>
                        </div>
                        
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">
                                <span class="btn-icon">💾</span>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Alterar Senha</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/profile/password" id="password-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="current_password" class="form-label">Senha Atual</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="current_password" 
                                name="current_password" 
                                required
                                placeholder="Sua senha atual"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password" class="form-label">Nova Senha</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="new_password" 
                                name="new_password" 
                                required
                                placeholder="Mínimo <?php echo MIN_PASSWORD_LENGTH; ?> caracteres"
                                minlength="<?php echo MIN_PASSWORD_LENGTH; ?>"
                            >
                            <div class="form-text">A nova senha deve ter pelo menos <?php echo MIN_PASSWORD_LENGTH; ?> caracteres.</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required
                                placeholder="Repita a nova senha"
                            >
                        </div>
                        
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-warning">
                                <span class="btn-icon">🔑</span>
                                Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title text-white">Zona de Perigo</h4>
                </div>
                <div class="card-body">
                    <p class="text-danger">
                        A exclusão da sua conta é uma ação **irreversível**. Todos os seus dados, incluindo todas as suas tarefas, serão permanentemente apagados.
                    </p>
                    <button type="button" class="btn btn-danger" data-modal="delete-account-modal">
                        <span class="btn-icon">🗑️</span>
                        Excluir Minha Conta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal" id="delete-account-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmação de Exclusão de Conta</h5>
                <button type="button" class="close" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <p>
                    Você tem certeza que deseja excluir sua conta? Esta ação é **irreversível** e todos os seus dados serão perdidos.
                </p>
                <p>
                    Para confirmar, digite seu email (<strong><?php echo htmlspecialchars($user->email); ?></strong>) no campo abaixo:
                </p>
                <input type="email" id="confirm-email" class="form-control" placeholder="Digite seu email">
                <div class="invalid-feedback" id="email-match-error" style="display: none;">
                    O email digitado não corresponde.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Cancelar</button>
                <form method="POST" action="/profile/delete" class="d-inline" id="delete-account-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <button type="submit" class="btn btn-danger" id="confirm-delete-btn" disabled>
                        Excluir Permanentemente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.text-right {
    text-align: right;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1050;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal.active {
    display: flex;
    opacity: 1;
}

.modal-dialog {
    background: var(--white);
    border-radius: var(--border-radius);
    max-width: 500px;
    width: 90%;
    box-shadow: var(--shadow-lg);
    transform: translateY(-50px);
    transition: transform 0.3s ease;
}

.modal.active .modal-dialog {
    transform: translateY(0);
}

.modal-content {
    display: flex;
    flex-direction: column;
}

.modal-header {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin-bottom: 0;
}

.modal-body {
    padding: 1rem;
}

.modal-footer {
    padding: 1rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.modal-footer .d-inline {
    margin: 0;
}

.close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.7;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmEmailField = document.getElementById('confirm-email');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const emailMatchError = document.getElementById('email-match-error');
    const userEmail = '<?php echo $user->email; ?>';

    if (confirmEmailField && confirmDeleteBtn) {
        confirmEmailField.addEventListener('input', function() {
            const isMatch = this.value.trim() === userEmail;
            confirmDeleteBtn.disabled = !isMatch;
            
            if (this.value.trim().length > 0 && !isMatch) {
                emailMatchError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                emailMatchError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }
    
    // Validação de senhas no formulário de alteração de senha
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const current = document.getElementById('current_password').value;
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            const minLength = <?php echo MIN_PASSWORD_LENGTH; ?>;
            
            if (newPass !== confirmPass) {
                e.preventDefault();
                showNotification('A nova senha e a confirmação não coincidem.', 'error');
                document.getElementById('confirm_password').focus();
                return;
            }
            
            if (newPass.length < minLength) {
                e.preventDefault();
                showNotification(`A nova senha deve ter pelo menos ${minLength} caracteres.`, 'error');
                document.getElementById('new_password').focus();
                return;
            }
            
            // Mostrar loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="btn-icon">⏳</span>Alterando...';
            submitBtn.disabled = true;
            
            // Restaurar botão em caso de erro (será redirecionado se sucesso)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    }
});
</script>

<?php
// Limpar dados antigos da sessão
unset($_SESSION['errors']);
unset($_SESSION['old_data']);
?>
