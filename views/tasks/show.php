<?php
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '/dashboard'],
    ['title' => 'Tarefa: ' . htmlspecialchars($task->title)]
];

$priority_class = $task->priority === 'alta' ? 'danger' : ($task->priority === 'media' ? 'warning' : 'secondary');
$status_class = $task->status === 'concluida' ? 'success' : 'warning';
$status_text = $task->status === 'concluida' ? 'Concluída' : 'Pendente';
$priority_text = ucfirst($task->priority);

$due_date_obj = $task->due_date ? new DateTime($task->due_date) : null;
$today = new DateTime();
$is_overdue = $due_date_obj && $due_date_obj < $today && $task->status === 'pendente';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card task-details-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <span class="card-icon">📋</span>
                        Detalhes da Tarefa #<?php echo $task->id; ?>
                    </h3>
                    <div class="task-actions-header">
                        <a href="/tasks/<?php echo $task->id; ?>/edit" class="btn btn-sm btn-outline-secondary" data-tooltip="Editar">
                            ✏️ Editar
                        </a>
                        
                        <?php if ($task->status === 'pendente'): ?>
                            <form method="POST" action="/tasks/<?php echo $task->id; ?>/complete" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="redirect_url" value="/tasks/<?php echo $task->id; ?>">
                                <button type="submit" class="btn btn-sm btn-success" data-tooltip="Marcar como concluída">
                                    ✅ Concluir
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="/tasks/<?php echo $task->id; ?>/reopen" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="redirect_url" value="/tasks/<?php echo $task->id; ?>">
                                <button type="submit" class="btn btn-sm btn-warning" data-tooltip="Reabrir tarefa">
                                    🔄 Reabrir
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <form method="POST" action="/tasks/<?php echo $task->id; ?>/delete" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <button type="submit" 
                                    class="btn btn-sm btn-danger" 
                                    data-tooltip="Excluir"
                                    data-confirm="Tem certeza que deseja excluir esta tarefa? Esta ação é irreversível.">
                                🗑️ Excluir
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card-body">
                    <h1 class="task-title-main"><?php echo htmlspecialchars($task->title); ?></h1>
                    
                    <div class="task-meta-info">
                        <span class="badge badge-<?php echo $status_class; ?>">
                            Status: <?php echo $status_text; ?>
                        </span>
                        <span class="badge badge-<?php echo $priority_class; ?>">
                            Prioridade: <?php echo $priority_text; ?>
                        </span>
                        
                        <?php if ($task->due_date): ?>
                            <span class="meta-item <?php echo $is_overdue ? 'text-danger font-weight-bold' : 'text-muted'; ?>">
                                <span class="meta-icon">📅</span>
                                Vencimento: <?php echo $due_date_obj->format('d/m/Y'); ?>
                                <?php if ($is_overdue): ?>
                                    <span class="text-danger">(Em Atraso)</span>
                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span class="meta-item text-muted">
                                <span class="meta-icon">📅</span>
                                Sem data de vencimento
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <h4 class="mt-4">Descrição</h4>
                    <div class="task-description-content">
                        <?php if (!empty($task->description)): ?>
                            <p><?php echo nl2br(htmlspecialchars($task->description)); ?></p>
                        <?php else: ?>
                            <p class="text-muted">Nenhuma descrição fornecida para esta tarefa.</p>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <div class="task-timestamps">
                        <p class="text-muted small">
                            Criada em: <?php echo date('d/m/Y H:i:s', strtotime($task->created_at)); ?>
                        </p>
                        <p class="text-muted small">
                            Última atualização: <?php echo date('d/m/Y H:i:s', strtotime($task->updated_at)); ?>
                        </p>
                    </div>
                </div>
                
                <div class="card-footer">
                    <a href="/dashboard" class="btn btn-secondary">
                        <span class="btn-icon">←</span>
                        Voltar para o Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.task-details-card {
    margin-top: 1rem;
}

.task-title-main {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.task-meta-info {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.meta-icon {
    margin-right: 0.25rem;
}

.task-description-content {
    min-height: 100px;
    padding: 1rem;
    background-color: var(--light-color);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    white-space: pre-wrap;
}

.task-description-content p {
    margin-bottom: 0;
}

.task-timestamps {
    margin-top: 1.5rem;
    border-top: 1px solid var(--border-color);
    padding-top: 1rem;
}

.task-actions-header {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .task-details-card .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .task-actions-header {
        margin-top: 1rem;
    }
    
    .task-title-main {
        font-size: 1.5rem;
    }
}
</style>

<?php
// Limpar dados antigos da sessão
unset($_SESSION['errors']);
unset($_SESSION['old_data']);
?>
