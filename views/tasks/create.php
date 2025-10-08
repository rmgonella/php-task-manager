<?php
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '/dashboard'],
    ['title' => 'Nova Tarefa']
];

$old_data = $_SESSION['old_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="card-icon">➕</span>
                        Criar Nova Tarefa
                    </h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="/tasks/create" id="task-create-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="title" class="form-label">Título da Tarefa</label>
                            <input 
                                type="text" 
                                class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" 
                                id="title" 
                                name="title" 
                                value="<?php echo htmlspecialchars($old_data['title'] ?? ''); ?>"
                                required 
                                autofocus
                                placeholder="Ex: Estudar PHP MVC"
                                maxlength="<?php echo MAX_TITLE_LENGTH; ?>"
                            >
                            <?php if (isset($errors['title'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['title']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Máximo <?php echo MAX_TITLE_LENGTH; ?> caracteres.</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label">Descrição (Opcional)</label>
                            <textarea 
                                class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" 
                                id="description" 
                                name="description" 
                                rows="4"
                                placeholder="Detalhes da tarefa..."
                                maxlength="<?php echo MAX_DESCRIPTION_LENGTH; ?>"
                            ><?php echo htmlspecialchars($old_data['description'] ?? ''); ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['description']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Máximo <?php echo MAX_DESCRIPTION_LENGTH; ?> caracteres.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority" class="form-label">Prioridade</label>
                                    <select 
                                        class="form-control <?php echo isset($errors['priority']) ? 'is-invalid' : ''; ?>" 
                                        id="priority" 
                                        name="priority" 
                                        required
                                    >
                                        <option value="baixa" <?php echo ($old_data['priority'] ?? 'media') === 'baixa' ? 'selected' : ''; ?>>Baixa</option>
                                        <option value="media" <?php echo ($old_data['priority'] ?? 'media') === 'media' ? 'selected' : ''; ?>>Média</option>
                                        <option value="alta" <?php echo ($old_data['priority'] ?? 'media') === 'alta' ? 'selected' : ''; ?>>Alta</option>
                                    </select>
                                    <?php if (isset($errors['priority'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo htmlspecialchars($errors['priority']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date" class="form-label">Data de Vencimento (Opcional)</label>
                                    <input 
                                        type="date" 
                                        class="form-control <?php echo isset($errors['due_date']) ? 'is-invalid' : ''; ?>" 
                                        id="due_date" 
                                        name="due_date" 
                                        value="<?php echo htmlspecialchars($old_data['due_date'] ?? ''); ?>"
                                    >
                                    <?php if (isset($errors['due_date'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo htmlspecialchars($errors['due_date']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-0 d-flex justify-content-between">
                            <a href="/dashboard" class="btn btn-secondary">
                                <span class="btn-icon">←</span>
                                Voltar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <span class="btn-icon">💾</span>
                                Salvar Tarefa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.col-md-6 {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

.row {
    margin-left: -0.5rem;
    margin-right: -0.5rem;
}
</style>

<?php
// Limpar dados antigos da sessão
unset($_SESSION['errors']);
unset($_SESSION['old_data']);
?>
