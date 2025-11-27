@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Notifications</h1>
                <p class="text-gray-600 mt-2">Stay updated on your appointments and medical information</p>
            </div>
            <div class="flex gap-2">
                <button onclick="markAllAsRead()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Mark All as Read
                </button>
                <a href="{{ route('patient.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="notificationTypeFilter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Type</label>
                    <select id="notificationTypeFilter" name="notificationType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500" aria-label="Filter notifications by type">
                        <option value="">-- All Types --</option>
                        <option value="appointment">Appointment</option>
                        <option value="reminder">Reminder</option>
                        <option value="cancellation">Cancellation</option>
                        <option value="medical">Medical</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="notificationStatusFilter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                    <select id="notificationStatusFilter" name="notificationStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500" aria-label="Filter notifications by status">
                        <option value="">-- All Status --</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="filterNotifications()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium" aria-label="Apply notification filters">
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Total Notifications</p>
                <p class="text-3xl font-bold text-blue-600 mt-2" id="total-notifications">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Unread</p>
                <p class="text-3xl font-bold text-red-600 mt-2" id="unread-count">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">This Week</p>
                <p class="text-3xl font-bold text-green-600 mt-2" id="this-week-count">0</p>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4" id="notifications-container">
            <div class="text-center py-8 text-gray-500">Loading notifications...</div>
        </div>

        <!-- Notification Detail Modal -->
        <div id="notificationDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
            <div class="relative top-20 mx-auto p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closeNotificationModal()" aria-label="Close notification details">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 mb-4">Notification Details</h3>
                <div id="notification-detail-content" class="space-y-4">
                    <!-- Loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const patientId = {{ auth()->user()->patient->patient_id ?? 'null' }};
let allNotifications = [];

document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
});

function loadNotifications() {
    fetch(`/api/notifications/patient/${patientId}`)
        .then(res => res.json())
        .then(data => {
            allNotifications = data.data || data;
            updateStatistics();
            displayNotifications(allNotifications);
        })
        .catch(err => console.error('Error:', err));
}

function updateStatistics() {
    const total = allNotifications.length;
    const unread = allNotifications.filter(n => !n.is_sent).length;
    const thisWeek = allNotifications.filter(n => {
        const date = new Date(n.created_at);
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        return date > weekAgo;
    }).length;
    
    document.getElementById('total-notifications').textContent = total;
    document.getElementById('unread-count').textContent = unread;
    document.getElementById('this-week-count').textContent = thisWeek;
}

function displayNotifications(notifications) {
    const container = document.getElementById('notifications-container');
    
    if (notifications.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">No notifications</div>';
        return;
    }
    
    container.innerHTML = notifications.map(notif => {
        const date = new Date(notif.created_at);
        const isUnread = !notif.is_sent;
        const bgColor = isUnread ? 'bg-blue-50 border-blue-200' : 'bg-white';
        
        let icon = '📧';
        let typeLabel = 'Notification';
        if (notif.notification_type === 'appointment') {
            icon = '📅';
            typeLabel = 'Appointment';
        } else if (notif.notification_type === 'reminder') {
            icon = '⏰';
            typeLabel = 'Reminder';
        } else if (notif.notification_type === 'cancellation') {
            icon = '❌';
            typeLabel = 'Cancellation';
        }
        
        return `
            <div class="bg-white rounded-lg shadow p-6 border-l-4 ${isUnread ? 'border-blue-500' : 'border-gray-200'} hover:shadow-md transition cursor-pointer" onclick="viewNotificationDetail(${notif.id})">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">${icon}</span>
                            <div>
                                <h4 class="font-semibold text-gray-900">${typeLabel}</h4>
                                <p class="text-sm text-gray-600 mt-1">${notif.message}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">${date.toLocaleString()}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        ${isUnread ? '<span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>' : ''}
                        <span class="text-xs px-2 py-1 rounded-full ${isUnread ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">
                            ${isUnread ? 'Unread' : 'Read'}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function viewNotificationDetail(notificationId) {
    const notif = allNotifications.find(n => n.id === notificationId);
    if (!notif) return;
    
    const content = document.getElementById('notification-detail-content');
    content.innerHTML = `
        <div class="space-y-4">
            <div>
                <h4 class="font-semibold text-gray-900">Type</h4>
                <p class="text-gray-700">${notif.notification_type}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Message</h4>
                <p class="text-gray-700">${notif.message}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Related Appointment</h4>
                <p class="text-gray-700">${notif.appointment_id ? `Appointment #${notif.appointment_id}` : 'N/A'}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Status</h4>
                <p class="text-gray-700">${notif.is_sent ? 'Sent' : 'Pending'}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Date</h4>
                <p class="text-gray-700">${new Date(notif.created_at).toLocaleString()}</p>
            </div>
            ${notif.sent_at ? `
            <div>
                <h4 class="font-semibold text-gray-900">Sent At</h4>
                <p class="text-gray-700">${new Date(notif.sent_at).toLocaleString()}</p>
            </div>
            ` : ''}
        </div>
    `;
    
    // Mark as read if unread
    if (!notif.is_sent) {
        markAsRead(notif.id);
    }
    
    document.getElementById('notificationDetailModal').classList.remove('hidden');
}

function closeNotificationModal() {
    document.getElementById('notificationDetailModal').classList.add('hidden');
}

function markAsRead(notificationId) {
    fetch(`/api/notifications/${notificationId}/mark-sent`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(() => {
        loadNotifications();
    })
    .catch(err => console.error('Error:', err));
}

function markAllAsRead() {
    const unreadIds = allNotifications
        .filter(n => !n.is_sent)
        .map(n => n.id);
    
    Promise.all(unreadIds.map(id => 
        fetch(`/api/notifications/${id}/mark-sent`, { method: 'PATCH', headers: { 'Content-Type': 'application/json' } })
    ))
    .then(() => {
        alert('All notifications marked as read!');
        loadNotifications();
    })
    .catch(err => console.error('Error:', err));
}

function filterNotifications() {
    const typeFilter = document.getElementById('notificationTypeFilter').value;
    const statusFilter = document.getElementById('notificationStatusFilter').value;
    
    let filtered = allNotifications;
    
    if (typeFilter) {
        filtered = filtered.filter(n => n.notification_type === typeFilter);
    }
    
    if (statusFilter === 'unread') {
        filtered = filtered.filter(n => !n.is_sent);
    } else if (statusFilter === 'read') {
        filtered = filtered.filter(n => n.is_sent);
    }
    
    displayNotifications(filtered);
}
</script>
@endsection
