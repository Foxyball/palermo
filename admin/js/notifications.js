    const POLL_INTERVAL = 30000; // 30 seconds
    const BASE_URL = document.getElementById('js-base-url')?.value || window.location.origin + '/admin/';


    function fetchNotifications() {
        $.ajax({
            url: BASE_URL + 'get_notifications.php',
            method: 'GET',
            dataType: 'json',
            cache: false
        })
        .done(function(response) {
            if (response.success) {
                updateNotificationBadge(response.pending_count);
                updateNotificationDropdown(response.orders, response.pending_count);
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Failed to fetch notifications:', error);
        });
    }


    function updateNotificationBadge(count) {
        const $badge = $('.navbar-badge');
        
        if (count > 0) {
            $badge.text(count).show();
        } else {
            $badge.text('0').hide();
        }
    }

    function updateNotificationDropdown(orders, count) {
        const $dropdown = $('#notifications-menu > .dropdown-menu');
        
        if (!$dropdown.length) return;

        let html = '';
        
        // Header
        if (count > 0) {
            html += `<span class="dropdown-item dropdown-header">${count} Pending Order${count !== 1 ? 's' : ''}</span>`;
            html += '<div class="dropdown-divider"></div>';
        } else {
            html += '<span class="dropdown-item dropdown-header">No Pending Orders</span>';
            html += '<div class="dropdown-divider"></div>';
        }

        // Orders list
        if (orders && orders.length > 0) {
            orders.forEach(function(order) {
                const timeAgo = getTimeAgo(order.created_at);
                const amount = parseFloat(order.amount).toFixed(2);
                
                html += `
                    <a href="order_show?id=${order.id}" class="dropdown-item">
                        <i class="bi bi-cart-fill me-2"></i> 
                        Order #${order.id} - ${amount} лв
                        <span class="float-end text-secondary fs-7">${timeAgo}</span>
                    </a>
                `;
            });
            html += '<div class="dropdown-divider"></div>';
        } else {
            html += '<a href="#" class="dropdown-item text-muted text-center">No pending orders</a>';
            html += '<div class="dropdown-divider"></div>';
        }

        html += '<a href="order_pending" class="dropdown-item dropdown-footer">View All Pending Orders</a>';

        $dropdown.html(html);
    }

    function getTimeAgo(timestamp) {
        const now = new Date();
        const past = new Date(timestamp);
        const diffMs = now - past;
        const diffMins = Math.floor(diffMs / 60000);
        
        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return diffMins + ' min' + (diffMins !== 1 ? 's' : '') + ' ago';
        
        const diffHours = Math.floor(diffMins / 60);
        if (diffHours < 24) return diffHours + ' hour' + (diffHours !== 1 ? 's' : '') + ' ago';
        
        const diffDays = Math.floor(diffHours / 24);
        return diffDays + ' day' + (diffDays !== 1 ? 's' : '') + ' ago';
    }


    function init() {
        fetchNotifications();
        setInterval(fetchNotifications, POLL_INTERVAL);
    }

    $(document).ready(function() {
        init();
    });
