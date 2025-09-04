<!-- Notification Component -->
<div class="notification-widget" id="notificationWidget">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-bell me-2"></i>Notifications
                </h6>
                <span class="badge bg-primary" id="notificationCount">0</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="notificationList" class="list-group list-group-flush">
                <div class="list-group-item text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin mb-2"></i>
                    <div>Loading notifications...</div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent text-center">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                <i class="fas fa-check me-1"></i>Mark All as Read
            </button>
        </div>
    </div>
</div>

<style>
.notification-widget {
    position: sticky;
    top: 20px;
}

.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-urgent {
    border-left: 4px solid #dc3545;
}

.notification-warning {
    border-left: 4px solid #ffc107;
}

.notification-info {
    border-left: 4px solid #0dcaf0;
}

.notification-success {
    border-left: 4px solid #198754;
}

.notification-danger {
    border-left: 4px solid #dc3545;
}
</style>

<script>
let notificationInterval;

function loadNotifications() {
    fetch('/api/notifications')
        .then(response => response.json())
        .then(data => {
            const notificationList = document.getElementById('notificationList');
            const notificationCount = document.getElementById('notificationCount');
            
            notificationCount.textContent = data.count;
            
            if (data.notifications.length === 0) {
                notificationList.innerHTML = `
                    <div class="list-group-item text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <div>No new notifications</div>
                    </div>
                `;
            } else {
                notificationList.innerHTML = data.notifications.map(notification => `
                    <div class="list-group-item notification-item notification-${notification.type}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${notification.title}</h6>
                                <p class="mb-1 small">${notification.message}</p>
                                ${notification.deadline ? `<small class="text-muted">Due: ${new Date(notification.deadline).toLocaleDateString()}</small>` : ''}
                                ${notification.reviewed_at ? `<small class="text-muted">${new Date(notification.reviewed_at).toLocaleDateString()}</small>` : ''}
                            </div>
                            <div class="flex-shrink-0 ms-2">
                                ${notification.action ? `
                                    <a href="${notification.action}" class="btn btn-sm btn-outline-primary">
                                        ${notification.action_text}
                                    </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            document.getElementById('notificationList').innerHTML = `
                <div class="list-group-item text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle mb-2"></i>
                    <div>Error loading notifications</div>
                </div>
            `;
        });
}

function markAllAsRead() {
    fetch('/api/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        }
    });
}

// Load notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    
    // Refresh notifications every 30 seconds
    notificationInterval = setInterval(loadNotifications, 30000);
});

// Clear interval when page unloads
window.addEventListener('beforeunload', function() {
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
});
</script>
