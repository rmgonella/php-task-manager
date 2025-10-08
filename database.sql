-- Script SQL para criação do banco de dados
-- PHP Task Manager - Sistema de Gerenciamento de Tarefas
-- Versão: 1.0.0

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS php_task_manager 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Usar o banco de dados
USE php_task_manager;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de tarefas
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pendente', 'concluida') DEFAULT 'pendente',
    priority ENUM('baixa', 'media', 'alta') DEFAULT 'media',
    due_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_due_date (due_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir usuário de teste (senha: 123456)
INSERT INTO users (name, email, password) VALUES 
('Usuário Teste', 'teste@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Inserir algumas tarefas de exemplo
INSERT INTO tasks (user_id, title, description, status, priority, due_date) VALUES 
(1, 'Estudar PHP', 'Revisar conceitos de orientação a objetos em PHP', 'pendente', 'alta', '2025-10-15'),
(1, 'Implementar autenticação', 'Criar sistema de login e registro de usuários', 'concluida', 'alta', '2025-10-10'),
(1, 'Criar interface do usuário', 'Desenvolver páginas HTML e CSS para o sistema', 'pendente', 'media', '2025-10-20'),
(1, 'Testar funcionalidades', 'Realizar testes de todas as funcionalidades do sistema', 'pendente', 'media', '2025-10-25'),
(1, 'Documentar projeto', 'Escrever documentação completa do projeto', 'pendente', 'baixa', '2025-10-30');

-- Criar índices adicionais para otimização
CREATE INDEX idx_tasks_user_status ON tasks(user_id, status);
CREATE INDEX idx_tasks_user_priority ON tasks(user_id, priority);

-- Criar view para estatísticas de tarefas por usuário
CREATE VIEW user_task_stats AS
SELECT 
    u.id as user_id,
    u.name as user_name,
    u.email as user_email,
    COUNT(t.id) as total_tasks,
    SUM(CASE WHEN t.status = 'pendente' THEN 1 ELSE 0 END) as pending_tasks,
    SUM(CASE WHEN t.status = 'concluida' THEN 1 ELSE 0 END) as completed_tasks,
    SUM(CASE WHEN t.priority = 'alta' THEN 1 ELSE 0 END) as high_priority_tasks,
    SUM(CASE WHEN t.due_date < CURDATE() AND t.status = 'pendente' THEN 1 ELSE 0 END) as overdue_tasks
FROM users u
LEFT JOIN tasks t ON u.id = t.user_id
GROUP BY u.id, u.name, u.email;

-- Comentários sobre as tabelas
ALTER TABLE users COMMENT = 'Tabela para armazenar informações dos usuários do sistema';
ALTER TABLE tasks COMMENT = 'Tabela para armazenar as tarefas criadas pelos usuários';

-- Comentários sobre as colunas
ALTER TABLE users 
MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único do usuário',
MODIFY COLUMN name VARCHAR(100) NOT NULL COMMENT 'Nome completo do usuário',
MODIFY COLUMN email VARCHAR(150) NOT NULL UNIQUE COMMENT 'Email único do usuário para login',
MODIFY COLUMN password VARCHAR(255) NOT NULL COMMENT 'Senha criptografada do usuário',
MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação do registro',
MODIFY COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização';

ALTER TABLE tasks 
MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único da tarefa',
MODIFY COLUMN user_id INT NOT NULL COMMENT 'ID do usuário proprietário da tarefa',
MODIFY COLUMN title VARCHAR(255) NOT NULL COMMENT 'Título da tarefa',
MODIFY COLUMN description TEXT COMMENT 'Descrição detalhada da tarefa',
MODIFY COLUMN status ENUM('pendente', 'concluida') DEFAULT 'pendente' COMMENT 'Status atual da tarefa',
MODIFY COLUMN priority ENUM('baixa', 'media', 'alta') DEFAULT 'media' COMMENT 'Prioridade da tarefa',
MODIFY COLUMN due_date DATE NULL COMMENT 'Data limite para conclusão da tarefa',
MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação da tarefa',
MODIFY COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização da tarefa';

-- Verificar se as tabelas foram criadas corretamente
SHOW TABLES;

-- Mostrar estrutura das tabelas
DESCRIBE users;
DESCRIBE tasks;
