/**
 * JavaScript Principal - PHP Task Manager
 * Sistema de Gerenciamento de Tarefas
 */

// Aguardar carregamento do DOM
document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar componentes
    initNavigation();
    initAlerts();
    initForms();
    initTables();
    initModals();
    initTooltips();
    
});

/**
 * Inicializar navegação
 */
function initNavigation() {
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
    }
    
    // Fechar menu ao clicar em um link (mobile)
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });
    });
    
    // Dropdown do usuário
    const userDropdown = document.getElementById('user-dropdown');
    const userDropdownMenu = document.getElementById('user-dropdown-menu');
    
    if (userDropdown && userDropdownMenu) {
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.closest('.dropdown');
            dropdown.classList.toggle('active');
        });
        
        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                const dropdown = userDropdown.closest('.dropdown');
                dropdown.classList.remove('active');
            }
        });
    }
}

/**
 * Inicializar alertas
 */
function initAlerts() {
    // Auto-fechar alertas após 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            fadeOut(alert);
        }, 5000);
    });
}

/**
 * Fechar alerta
 */
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        fadeOut(alert);
    }
}

/**
 * Efeito fade out
 */
function fadeOut(element) {
    element.style.opacity = '0';
    element.style.transform = 'translateY(-10px)';
    setTimeout(() => {
        element.remove();
    }, 300);
}

/**
 * Inicializar formulários
 */
function initForms() {
    // Validação em tempo real
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
        
        // Validação no submit
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
    
    // Confirmação de exclusão
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Tem certeza que deseja excluir?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Contador de caracteres
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        createCharCounter(textarea);
    });
}

/**
 * Validar campo individual
 */
function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    
    // Limpar erros anteriores
    clearFieldError(field);
    
    // Verificar campo obrigatório
    if (required && !value) {
        showFieldError(field, 'Este campo é obrigatório');
        return false;
    }
    
    // Validações específicas por tipo
    if (value) {
        switch (type) {
            case 'email':
                if (!isValidEmail(value)) {
                    showFieldError(field, 'Email inválido');
                    return false;
                }
                break;
                
            case 'password':
                if (value.length < 6) {
                    showFieldError(field, 'Senha deve ter pelo menos 6 caracteres');
                    return false;
                }
                break;
                
            case 'date':
                if (!isValidDate(value)) {
                    showFieldError(field, 'Data inválida');
                    return false;
                }
                break;
        }
    }
    
    // Validação de confirmação de senha
    if (field.name === 'confirm_password') {
        const passwordField = document.querySelector('input[name="password"]');
        if (passwordField && value !== passwordField.value) {
            showFieldError(field, 'As senhas não coincidem');
            return false;
        }
    }
    
    showFieldSuccess(field);
    return true;
}

/**
 * Validar formulário completo
 */
function validateForm(form) {
    const fields = form.querySelectorAll('input, textarea, select');
    let isValid = true;
    
    fields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Mostrar erro no campo
 */
function showFieldError(field, message) {
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');
    
    // Remover feedback anterior
    const existingFeedback = field.parentNode.querySelector('.invalid-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    // Adicionar novo feedback
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = message;
    field.parentNode.appendChild(feedback);
}

/**
 * Mostrar sucesso no campo
 */
function showFieldSuccess(field) {
    field.classList.add('is-valid');
    field.classList.remove('is-invalid');
    
    // Remover feedback de erro
    const existingFeedback = field.parentNode.querySelector('.invalid-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
}

/**
 * Limpar erro do campo
 */
function clearFieldError(field) {
    field.classList.remove('is-invalid', 'is-valid');
    
    const existingFeedback = field.parentNode.querySelector('.invalid-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
}

/**
 * Validar email
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validar data
 */
function isValidDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

/**
 * Criar contador de caracteres
 */
function createCharCounter(textarea) {
    const maxLength = parseInt(textarea.getAttribute('maxlength'));
    const counter = document.createElement('div');
    counter.className = 'char-counter text-muted';
    counter.style.fontSize = '0.875rem';
    counter.style.textAlign = 'right';
    counter.style.marginTop = '0.25rem';
    
    function updateCounter() {
        const currentLength = textarea.value.length;
        counter.textContent = `${currentLength}/${maxLength}`;
        
        if (currentLength > maxLength * 0.9) {
            counter.style.color = '#dc3545';
        } else if (currentLength > maxLength * 0.8) {
            counter.style.color = '#ffc107';
        } else {
            counter.style.color = '#6c757d';
        }
    }
    
    textarea.addEventListener('input', updateCounter);
    textarea.parentNode.appendChild(counter);
    updateCounter();
}

/**
 * Inicializar tabelas
 */
function initTables() {
    // Ordenação de tabelas
    const sortableHeaders = document.querySelectorAll('th[data-sort]');
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(this);
        });
    });
    
    // Seleção múltipla
    const selectAllCheckbox = document.querySelector('input[data-select-all]');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[data-select-item]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }
    
    const itemCheckboxes = document.querySelectorAll('input[data-select-item]');
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
}

/**
 * Ordenar tabela
 */
function sortTable(header) {
    const table = header.closest('table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const column = header.cellIndex;
    const currentOrder = header.getAttribute('data-order') || 'asc';
    const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
    
    rows.sort((a, b) => {
        const aValue = a.cells[column].textContent.trim();
        const bValue = b.cells[column].textContent.trim();
        
        if (newOrder === 'asc') {
            return aValue.localeCompare(bValue, 'pt-BR', { numeric: true });
        } else {
            return bValue.localeCompare(aValue, 'pt-BR', { numeric: true });
        }
    });
    
    // Atualizar DOM
    rows.forEach(row => tbody.appendChild(row));
    
    // Atualizar indicadores de ordenação
    const allHeaders = table.querySelectorAll('th[data-sort]');
    allHeaders.forEach(h => {
        h.removeAttribute('data-order');
        h.classList.remove('sort-asc', 'sort-desc');
    });
    
    header.setAttribute('data-order', newOrder);
    header.classList.add(`sort-${newOrder}`);
}

/**
 * Atualizar ações em lote
 */
function updateBulkActions() {
    const selectedItems = document.querySelectorAll('input[data-select-item]:checked');
    const bulkActions = document.querySelector('.bulk-actions');
    
    if (bulkActions) {
        if (selectedItems.length > 0) {
            bulkActions.style.display = 'block';
            const countElement = bulkActions.querySelector('.selected-count');
            if (countElement) {
                countElement.textContent = selectedItems.length;
            }
        } else {
            bulkActions.style.display = 'none';
        }
    }
}

/**
 * Inicializar modais
 */
function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            openModal(modalId);
        });
    });
    
    const modalCloses = document.querySelectorAll('[data-modal-close]');
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });
    
    // Fechar modal ao clicar no overlay
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });
    
    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.active');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
}

/**
 * Abrir modal
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Fechar modal
 */
function closeModal(modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

/**
 * Inicializar tooltips
 */
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

/**
 * Mostrar tooltip
 */
function showTooltip(e) {
    const element = e.target;
    const text = element.getAttribute('data-tooltip');
    
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.style.position = 'absolute';
    tooltip.style.background = '#333';
    tooltip.style.color = '#fff';
    tooltip.style.padding = '0.5rem';
    tooltip.style.borderRadius = '4px';
    tooltip.style.fontSize = '0.875rem';
    tooltip.style.zIndex = '1000';
    tooltip.style.pointerEvents = 'none';
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
    
    element._tooltip = tooltip;
}

/**
 * Esconder tooltip
 */
function hideTooltip(e) {
    const element = e.target;
    if (element._tooltip) {
        element._tooltip.remove();
        delete element._tooltip;
    }
}

/**
 * Utilitários
 */

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Formatar data
function formatDate(date, format = 'dd/mm/yyyy') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    
    return format
        .replace('dd', day)
        .replace('mm', month)
        .replace('yyyy', year);
}

// Formatar número
function formatNumber(number, decimals = 0) {
    return new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

// Copiar para clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copiado para a área de transferência!', 'success');
        });
    } else {
        // Fallback para navegadores mais antigos
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Copiado para a área de transferência!', 'success');
    }
}

// Mostrar notificação
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
        <span class="alert-message">${message}</span>
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        fadeOut(notification);
    }, 3000);
}

// Scroll suave para elemento
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Verificar se elemento está visível
function isElementVisible(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Lazy loading de imagens
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback para navegadores sem suporte
        images.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('lazy');
        });
    }
}

// Inicializar lazy loading quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', initLazyLoading);
