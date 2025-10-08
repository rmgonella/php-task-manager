<?php
$breadcrumb = [
    ['title' => 'Dashboard']
];
?>

<div class="dashboard-header">
    <div class="welcome-section">
        <h1 class="dashboard-title">
            <span class="welcome-icon">👋</span>
            Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
        </h1>
        <p class="dashboard-subtitle">
            Bem-vindo ao seu painel de controle. Aqui você pode gerenciar todas as suas tarefas.
        </p>
    </div>
    
    <div class="quick-actions">
        <a href="/tasks/create" class="btn btn-primary">
            <span class="btn-icon">➕</span>
            Nova Tarefa
        </a>
    </div>
</div>

<!-- Estatísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary">📋</div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['total_tasks']; ?></div>
            <div class="stat-label">Total de Tarefas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-warning">⏳</div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['pending_tasks']; ?></div>
            <div class="stat-label">Pendentes</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-success">✅</div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['completed_tasks']; ?></div>
            <div class="stat-label">Concluídas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-danger">🚨</div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['overdue_tasks']; ?></div>
            <div class="stat-label">Em Atraso</div>
        </div>
    </div>
</div>

<!-- Alertas importantes -->
<?php if (!empty($overdue_tasks)): ?>
<div class="alert alert-danger">
    <span class="alert-icon">⚠️</span>
    <div class="alert-content">
        <strong>Atenção!</strong> Você tem <?php echo count($overdue_tasks); ?> tarefa(s) em atraso.
        <a href="#overdue-tasks" class="alert-link">Ver tarefas em atraso</a>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($upcoming_tasks)): ?>
<div class="alert alert-warning">
    <span class="alert-icon">📅</span>
    <div class="alert-content">
        <strong>Lembrete:</strong> Você tem <?php echo count($upcoming_tasks); ?> tarefa(s) com vencimento nos próximos 7 dias.
        <a href="#upcoming-tasks" class="alert-link">Ver próximas tarefas</a>
    </div>
</div>
<?php endif; ?>

<!-- Filtros e busca -->
<div class="filters-section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <span class="card-icon">🔍</span>
                Filtrar Tarefas
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="/dashboard" class="filters-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="pendente" <?php echo ($filters['status'] === 'pendente') ? 'selected' : ''; ?>>
                                    Pendente
                                </option>
                                <option value="concluida" <?php echo ($filters['status'] === 'concluida') ? 'selected' : ''; ?>>
                                    Concluída
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="priority" class="form-label">Prioridade</label>
                            <select name="priority" id="priority" class="form-control">
                                <option value="">Todas</option>
                                <option value="alta" <?php echo ($filters['priority'] === 'alta') ? 'selected' : ''; ?>>
                                    Alta
                                </option>
                                <option value="media" <?php echo ($filters['priority'] === 'media') ? 'selected' : ''; ?>>
                                    Média
                                </option>
                                <option value="baixa" <?php echo ($filters['priority'] === 'baixa') ? 'selected' : ''; ?>>
                                    Baixa
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="order_by" class="form-label">Ordenar por</label>
                            <select name="order_by" id="order_by" class="form-control">
                                <option value="created_at" <?php echo ($filters['order_by'] === 'created_at') ? 'selected' : ''; ?>>
                                    Data de Criação
                                </option>
                                <option value="due_date" <?php echo ($filters['order_by'] === 'due_date') ? 'selected' : ''; ?>>
                                    Data de Vencimento
                                </option>
                                <option value="priority" <?php echo ($filters['order_by'] === 'priority') ? 'selected' : ''; ?>>
                                    Prioridade
                                </option>
                                <option value="title" <?php echo ($filters['order_by'] === 'title') ? 'selected' : ''; ?>>
                                    Título
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="order_direction" class="form-label">Direção</label>
                            <select name="order_direction" id="order_direction" class="form-control">
                                <option value="DESC" <?php echo ($filters['order_direction'] === 'DESC') ? 'selected' : ''; ?>>
                                    Decrescente
                                </option>
                                <option value="ASC" <?php echo ($filters['order_direction'] === 'ASC') ? 'selected' : ''; ?>>
                                    Crescente
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">🔍</span>
                        Filtrar
                    </button>
                    <a href="/dashboard" class="btn btn-secondary">
                        <span class="btn-icon">🔄</span>
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lista de tarefas -->
<div class="tasks-section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <span class="card-icon">📋</span>
                Suas Tarefas
                <?php if ($total_tasks > 0): ?>
                    <span class="badge badge-primary"><?php echo $total_tasks; ?></span>
                <?php endif; ?>
            </h5>
        </div>
        
        <?php if (empty($tasks)): ?>
            <div class="card-body text-center">
                <div class="empty-state">
                    <div class="empty-icon">📝</div>
                    <h6>Nenhuma tarefa encontrada</h6>
                    <p class="text-muted">
                        <?php if ($filters['status'] || $filters['priority']): ?>
                            Não há tarefas que correspondam aos filtros selecionados.
                        <?php else: ?>
                            Você ainda não criou nenhuma tarefa. Que tal começar agora?
                        <?php endif; ?>
                    </p>
                    <a href="/tasks/create" class="btn btn-primary">
                        <span class="btn-icon">➕</span>
                        Criar Primeira Tarefa
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Status</th>
                            <th>Prioridade</th>
                            <th>Vencimento</th>
                            <th>Criada em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr class="task-row" data-task-id="<?php echo $task['id']; ?>">
                                <td>
                                    <div class="task-title">
                                        <a href="/tasks/<?php echo $task['id']; ?>" class="task-link">
                                            <?php echo htmlspecialchars($task['title']); ?>
                                        </a>
                                        <?php if (!empty($task['description'])): ?>
                                            <div class="task-description">
                                                <?php echo htmlspecialchars(substr($task['description'], 0, 100)); ?>
                                                <?php if (strlen($task['description']) > 100): ?>...<?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $task['status'] === 'concluida' ? 'success' : 'warning'; ?>">
                                        <?php echo $task['status'] === 'concluida' ? '✅ Concluída' : '⏳ Pendente'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $task['priority'] === 'alta' ? 'danger' : 
                                            ($task['priority'] === 'media' ? 'warning' : 'secondary'); 
                                    ?>">
                                        <?php 
                                        $priority_icons = ['alta' => '🔴', 'media' => '🟡', 'baixa' => '🔵'];
                                        echo $priority_icons[$task['priority']] . ' ' . ucfirst($task['priority']); 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($task['due_date']): ?>
                                        <?php 
                                        $due_date = new DateTime($task['due_date']);
                                        $today = new DateTime();
                                        $is_overdue = $due_date < $today && $task['status'] === 'pendente';
                                        ?>
                                        <span class="due-date <?php echo $is_overdue ? 'overdue' : ''; ?>">
                                            <?php if ($is_overdue): ?>🚨<?php endif; ?>
                                            <?php echo $due_date->format('d/m/Y'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Sem prazo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="created-date">
                                        <?php echo date('d/m/Y H:i', strtotime($task['created_at'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="task-actions">
                                        <a href="/tasks/<?php echo $task['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-tooltip="Ver detalhes">
                                            👁️
                                        </a>
                                        
                                        <a href="/tasks/<?php echo $task['id']; ?>/edit" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           data-tooltip="Editar">
                                            ✏️
                                        </a>
                                        
                                        <?php if ($task['status'] === 'pendente'): ?>
                                            <form method="POST" action="/tasks/<?php echo $task['id']; ?>/complete" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="redirect_url" value="/dashboard">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-success" 
                                                        data-tooltip="Marcar como concluída">
                                                    ✅
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="/tasks/<?php echo $task['id']; ?>/reopen" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="redirect_url" value="/dashboard">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        data-tooltip="Reabrir tarefa">
                                                    🔄
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" action="/tasks/<?php echo $task['id']; ?>/delete" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-tooltip="Excluir"
                                                    data-confirm="Tem certeza que deseja excluir esta tarefa?">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
                <div class="card-footer">
                    <nav aria-label="Paginação de tarefas">
                        <ul class="pagination">
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&<?php echo http_build_query($filters); ?>">
                                        ← Anterior
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&<?php echo http_build_query($filters); ?>">
                                        Próxima →
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <div class="pagination-info">
                        Mostrando <?php echo count($tasks); ?> de <?php echo $total_tasks; ?> tarefas
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.welcome-section {
    flex: 1;
}

.dashboard-title {
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.welcome-icon {
    margin-right: 0.5rem;
    font-size: 1.2em;
}

.dashboard-subtitle {
    color: var(--secondary-color);
    margin-bottom: 0;
}

.quick-actions {
    margin-left: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    box-shadow: var(--shadow);
    transition: var(--transition);
}

.stat-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
    color: var(--white);
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-color);
    line-height: 1;
}

.stat-label {
    color: var(--secondary-color);
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.filters-section {
    margin-bottom: 2rem;
}

.filters-form .row {
    margin-bottom: 1rem;
}

.filters-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.tasks-section {
    margin-bottom: 2rem;
}

.card-icon {
    margin-right: 0.5rem;
}

.empty-state {
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.task-row {
    transition: var(--transition);
}

.task-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.task-title {
    max-width: 300px;
}

.task-link {
    font-weight: 500;
    color: var(--dark-color);
    text-decoration: none;
}

.task-link:hover {
    color: var(--primary-color);
    text-decoration: underline;
}

.task-description {
    font-size: 0.875rem;
    color: var(--secondary-color);
    margin-top: 0.25rem;
}

.due-date.overdue {
    color: var(--danger-color);
    font-weight: 600;
}

.created-date {
    font-size: 0.875rem;
    color: var(--secondary-color);
}

.task-actions {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.task-actions .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.pagination {
    margin-bottom: 0;
}

.pagination-info {
    color: var(--secondary-color);
    font-size: 0.875rem;
    text-align: center;
    margin-top: 1rem;
}

.alert-content {
    flex: 1;
}

.alert-link {
    color: inherit;
    text-decoration: underline;
    font-weight: 600;
}

.alert-link:hover {
    color: inherit;
    text-decoration: none;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    
    .quick-actions {
        margin-left: 0;
        margin-top: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .filters-form .row {
        flex-direction: column;
    }
    
    .filters-form .col-md-3 {
        margin-bottom: 1rem;
    }
    
    .task-actions {
        flex-direction: column;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>

<script>
// Atualização automática das estatísticas
function updateStats() {
    fetch('/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = data.total_tasks;
                document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = data.pending_tasks;
                document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = data.completed_tasks;
                document.querySelector('.stat-card:nth-child(4) .stat-number').textContent = data.overdue_tasks;
            }
        })
        .catch(error => console.log('Erro ao atualizar estatísticas:', error));
}

// Atualizar estatísticas a cada 5 minutos
setInterval(updateStats, 300000);

// Filtros automáticos
document.getElementById('status').addEventListener('change', function() {
    if (this.value !== '') {
        this.form.submit();
    }
});

document.getElementById('priority').addEventListener('change', function() {
    if (this.value !== '') {
        this.form.submit();
    }
});

// Ações rápidas com teclado
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N = Nova tarefa
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '/tasks/create';
    }
    
    // Ctrl/Cmd + F = Focar no filtro de status
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('status').focus();
    }
});

// Marcar múltiplas tarefas como concluídas
function markMultipleAsCompleted() {
    const selectedTasks = document.querySelectorAll('input[data-select-item]:checked');
    if (selectedTasks.length === 0) {
        showNotification('Selecione pelo menos uma tarefa.', 'warning');
        return;
    }
    
    if (confirm(`Marcar ${selectedTasks.length} tarefa(s) como concluída(s)?`)) {
        const promises = Array.from(selectedTasks).map(checkbox => {
            const taskId = checkbox.value;
            return fetch(`/tasks/${taskId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `csrf_token=${encodeURIComponent('<?php echo $csrf_token; ?>')}`
            });
        });
        
        Promise.all(promises)
            .then(() => {
                showNotification('Tarefas marcadas como concluídas!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(error => {
                showNotification('Erro ao atualizar tarefas.', 'error');
                console.error('Erro:', error);
            });
    }
}
</script>
