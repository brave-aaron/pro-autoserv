// Main JavaScript file for AutoService MVC Application

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des formulaires AJAX
    handleAjaxForms();
    
    // Gestion des notifications
    initNotifications();
});

/**
 * Gestion des formulaires AJAX
 */
function handleAjaxForms() {
    const ajaxForms = document.querySelectorAll('.ajax-form');
    
    ajaxForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Désactivation du bouton
            submitBtn.disabled = true;
            submitBtn.textContent = 'Chargement...';
            
            fetch(this.action, {
                method: this.method,
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    
                    // Redirection si spécifiée
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Une erreur est survenue', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                // Réactivation du bouton
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    });
}

/**
 * Initialisation du système de notifications
 */
function initNotifications() {
    // Création du conteneur de notifications s'il n'existe pas
    if (!document.getElementById('notifications')) {
        const container = document.createElement('div');
        container.id = 'notifications';
        container.className = 'notifications-container';
        document.body.appendChild(container);
    }
}

/**
 * Affiche une notification
 */
function showNotification(message, type = 'info') {
    const container = document.getElementById('notifications');
    const notification = document.createElement('div');
    
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button class="notification-close">&times;</button>
    `;
    
    // Ajout de l'événement de fermeture
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.remove();
    });
    
    container.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Suppression automatique après 5 secondes
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 5000);
}

/**
 * Confirmation de suppression
 */
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}

/**
 * Gestion des changements de statut
 */
function changeStatus(type, id, newStatus) {
    const url = type === 'user' ? '/changer_statut.php' : '/admin/change_vehicle_status.php';
    const data = type === 'user' ? 
        { user_id: id, statut: newStatus } : 
        { vehicle_id: id, statut: newStatus };
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            // Actualiser la page après 1 seconde
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Une erreur est survenue', 'error');
        console.error('Error:', error);
    });
}