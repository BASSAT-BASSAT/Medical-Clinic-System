@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Patient Portal</h1>
                        <p class="text-sm font-bold text-gray-600">Welcome, {{ Auth::user()->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Notifications Dropdown -->
                    <div class="relative" id="notification-dropdown">
                        <button onclick="toggleNotifications()" class="relative p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span id="notification-badge" class="hidden absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                        </button>
                        
                        <!-- Notification Panel -->
                        <div id="notification-panel" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[600px] flex flex-col">
                            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">Notifications</h3>
                                <button onclick="markAllAsRead()" class="text-sm text-blue-600 hover:text-blue-700 transition">
                                    Mark all as read
                                </button>
                            </div>
                            <div id="notification-list" class="overflow-y-auto flex-1 max-h-96">
                                <div class="text-center py-12">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <p class="text-gray-600">No notifications</p>
                                </div>
                            </div>
                            <div id="notification-footer" class="px-4 py-3 border-t border-gray-200 text-center hidden">
                                <p class="text-sm text-gray-600">All caught up!</p>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Tabs -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-8">
                <button onclick="switchTab('appointments')" data-tab="appointments" class="tab-btn py-4 border-b-2 border-blue-600 text-blue-600 transition font-medium">
                    My Appointments
                </button>
                <button onclick="switchTab('records')" data-tab="records" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition font-medium">
                    Medical Records
                </button>
                <button onclick="switchTab('book')" data-tab="book" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition font-medium">
                    Book Appointment
                </button>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- My Appointments Tab -->
        <div id="tab-appointments" class="tab-content space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">My Appointments</h2>
                <button onclick="switchTab('book')" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Book New Appointment
                </button>
            </div>

            <!-- Search -->
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="searchAppointments" oninput="filterAppointments()" placeholder="Search appointments by doctor or reason..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>

            <!-- Appointments List -->
            <div id="appointments-list" class="grid gap-4">
                <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-600">Loading appointments...</p>
                </div>
            </div>
        </div>

        <!-- Medical Records Tab -->
        <div id="tab-records" class="tab-content hidden space-y-6">
            <h2 class="text-xl font-semibold text-gray-900">Medical Records</h2>
            <div id="records-list" class="grid gap-4">
                <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-600">Loading records...</p>
                </div>
            </div>
        </div>

        <!-- Book Appointment Tab -->
        <div id="tab-book" class="tab-content hidden space-y-6">
            <h2 class="text-xl font-semibold text-gray-900">Book New Appointment</h2>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form id="bookingForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Specialty</label>
                        <select id="booking_specialty" onchange="loadDoctorsBySpecialty()" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                            <option value="">-- Choose a Specialty --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Doctor</label>
                        <select id="booking_doctor" onchange="enableDateSelection()" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" disabled>
                            <option value="">-- Choose a Doctor --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                        <input type="date" id="booking_date" onchange="loadAvailableSlots()" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" disabled>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Time</label>
                        <select id="booking_time" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" disabled>
                            <option value="">-- Choose a Time --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Visit</label>
                        <textarea id="booking_reason" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none" placeholder="Describe your symptoms or reason for visit..."></textarea>
                    </div>

                    <button type="submit" id="bookBtn" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                        Book Appointment
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- Toast Notification -->
<div id="toast-container" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl border border-gray-200 p-4 min-w-[320px] max-w-md flex items-start gap-3">
        <div id="toast-icon" class="flex-shrink-0 w-6 h-6"></div>
        <div class="flex-1">
            <h4 id="toast-title" class="font-semibold text-gray-900"></h4>
            <p id="toast-message" class="text-sm text-gray-600 mt-1"></p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Confirm Dialog -->
<div id="confirm-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeConfirmModal(false)"></div>
    <div class="relative bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 id="confirm-title" class="text-lg font-semibold text-gray-900 mb-2">Confirm Action</h3>
        <p id="confirm-message" class="text-gray-600 mb-6">Are you sure you want to proceed?</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeConfirmModal(false)" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">Cancel</button>
            <button onclick="closeConfirmModal(true)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Confirm</button>
        </div>
    </div>
</div>

<script>
const patientId = {{ Auth::user()->patient->patient_id ?? 'null' }};
let allAppointments = [];
let allNotifications = [];

document.addEventListener('DOMContentLoaded', function() {
    loadAppointments();
    loadMedicalRecords();
    loadNotifications();
    loadSpecialties();
    
    // Set min date for booking
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('booking_date').min = today;
    
    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notification-dropdown');
        if (!dropdown.contains(e.target)) {
            document.getElementById('notification-panel').classList.add('hidden');
        }
    });
});

// Tab switching
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-600');
    });
    
    document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-600');
    activeBtn.classList.add('border-blue-600', 'text-blue-600');
}

function loadAppointments() {
    if (!patientId) return;

    // Load all appointments (not just upcoming) to show history
    fetch(`/api/appointments/by-patient/${patientId}`)
        .then(res => res.json())
        .then(data => {
            allAppointments = data;
            renderAppointments(allAppointments);
        })
        .catch(err => console.error('Error loading appointments:', err));
}

function renderAppointments(appointments) {
    const list = document.getElementById('appointments-list');
    
    if (appointments.length === 0) {
        list.innerHTML = `
            <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-600">No appointments found</p>
            </div>
        `;
        return;
    }

    list.innerHTML = appointments.map(apt => {
        const statusColors = {
            'scheduled': 'bg-green-100 text-green-800',
            'completed': 'bg-blue-100 text-blue-800',
            'cancelled': 'bg-red-100 text-red-800'
        };
        const date = new Date(apt.start_time);
        
        return `
            <div id="appointment-${apt.appointment_id}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-semibold text-gray-900">Dr. ${apt.doctor?.first_name || ''} ${apt.doctor?.last_name || ''}</h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full ${statusColors[apt.status] || 'bg-gray-100 text-gray-800'}">
                                ${apt.status}
                            </span>
                        </div>
                        <div class="space-y-2 text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                ${date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                            </div>
                            <p class="text-gray-900">Reason: ${apt.reason || 'General Appointment'}</p>
                        </div>
                    </div>
                    ${apt.status === 'scheduled' ? `
                        <button onclick="cancelAppointment(${apt.appointment_id})" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                            Cancel
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function filterAppointments() {
    const query = document.getElementById('searchAppointments').value.toLowerCase();
    const filtered = allAppointments.filter(apt => 
        (apt.doctor?.first_name || '').toLowerCase().includes(query) ||
        (apt.doctor?.last_name || '').toLowerCase().includes(query) ||
        (apt.reason || '').toLowerCase().includes(query)
    );
    renderAppointments(filtered);
}

function cancelAppointment(appointmentId) {
    showConfirm('Cancel Appointment', 'Are you sure you want to cancel this appointment?', (confirmed) => {
        if (!confirmed) return;

        fetch(`/api/appointments/${appointmentId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'cancelled' })
        })
        .then(res => res.json())
        .then(data => {
            // Update the appointment in the local array
            const index = allAppointments.findIndex(apt => apt.appointment_id === appointmentId);
            if (index !== -1) {
                allAppointments[index].status = 'cancelled';
            }
            
            // Re-render without page refresh
            renderAppointments(allAppointments);
            
            // Add cancellation notification
            addLocalNotification('cancelled', 'Appointment Cancelled', 'Your appointment has been cancelled successfully.');
            
            // Show success message
            showToast('success', 'Cancelled', 'Appointment cancelled successfully');
        })
        .catch(err => {
            console.error('Error cancelling appointment:', err);
            showToast('error', 'Error', 'Error cancelling appointment. Please try again.');
        });
    });
}

function showToast(type, title, message, duration = 4000) {
    const container = document.getElementById('toast-container');
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');
    const toastIcon = document.getElementById('toast-icon');
    
    const icons = {
        success: '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        error: '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        warning: '<svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
        info: '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    };
    
    toastIcon.innerHTML = icons[type] || icons.info;
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    container.classList.remove('hidden');
    container.style.animation = 'slideIn 0.3s ease-out';
    
    if (window.toastTimeout) clearTimeout(window.toastTimeout);
    window.toastTimeout = setTimeout(hideToast, duration);
}

function hideToast() {
    const container = document.getElementById('toast-container');
    container.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => container.classList.add('hidden'), 250);
}

let confirmCallback = null;
function showConfirm(title, message, callback) {
    document.getElementById('confirm-title').textContent = title;
    document.getElementById('confirm-message').textContent = message;
    confirmCallback = callback;
    document.getElementById('confirm-modal').classList.remove('hidden');
}

function closeConfirmModal(confirmed) {
    document.getElementById('confirm-modal').classList.add('hidden');
    if (confirmCallback) confirmCallback(confirmed);
    confirmCallback = null;
}

function loadMedicalRecords() {
    if (!patientId) return;

    fetch(`/api/patients/${patientId}/medical-records`)
        .then(res => res.json())
        .then(data => {
            const records = data.data || data;
            const list = document.getElementById('records-list');
            
            if (records.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-600">No medical records found</p>
                    </div>
                `;
                return;
            }
            
            list.innerHTML = records.map(record => `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">${record.diagnosis || 'Diagnosis'}</h3>
                            <p class="text-sm text-gray-600">Dr. ${record.doctor?.first_name || ''} ${record.doctor?.last_name || ''}</p>
                        </div>
                        <span class="text-sm text-gray-600">${new Date(record.visit_date || record.created_at).toLocaleDateString()}</span>
                    </div>
                    <div class="space-y-3">
                        ${record.prescription ? `
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-1">Prescription:</p>
                                <p class="text-sm text-gray-600">${record.prescription}</p>
                            </div>
                        ` : ''}
                        ${record.notes ? `
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-1">Notes:</p>
                                <p class="text-sm text-gray-600">${record.notes}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        })
        .catch(err => console.error('Error loading records:', err));
}

function loadNotifications() {
    if (!patientId) return;

    fetch(`/api/notifications/patient/${patientId}`)
        .then(res => res.json())
        .then(data => {
            allNotifications = data.data || data;
            renderNotifications();
        })
        .catch(err => console.error('Error loading notifications:', err));
}

function renderNotifications() {
    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    const footer = document.getElementById('notification-footer');
    
    const unreadCount = allNotifications.filter(n => !n.is_sent).length;
    
    if (unreadCount > 0) {
        badge.classList.remove('hidden');
        badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
    } else {
        badge.classList.add('hidden');
    }
    
    if (allNotifications.length === 0) {
        list.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <p class="text-gray-600">No notifications</p>
            </div>
        `;
        footer.classList.add('hidden');
        return;
    }
    
    footer.classList.remove('hidden');
    footer.innerHTML = `<p class="text-sm text-gray-600">${unreadCount === 0 ? 'All caught up!' : `${unreadCount} unread notification${unreadCount > 1 ? 's' : ''}`}</p>`;
    
    list.innerHTML = `<div class="divide-y divide-gray-100">` + allNotifications.map(notif => {
        const isUnread = !notif.is_sent;
        const iconMap = {
            'appointment': '📅',
            'reminder': '⏰',
            'cancellation': '❌',
            'confirmed': '✅'
        };
        const icon = iconMap[notif.notification_type] || '📧';
        
        return `
            <div onclick="markNotificationAsRead(${notif.id})" class="px-4 py-3 hover:bg-gray-50 transition cursor-pointer ${isUnread ? 'bg-blue-50' : ''}">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-1 text-xl">${icon}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm ${isUnread ? 'font-semibold text-gray-900' : 'text-gray-700'}">${notif.notification_type || 'Notification'}</p>
                            <button onclick="event.stopPropagation(); clearNotification(${notif.id})" class="flex-shrink-0 p-1 hover:bg-gray-200 rounded transition">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${notif.message}</p>
                        <p class="text-xs text-gray-500 mt-1">${formatTimestamp(new Date(notif.created_at))}</p>
                    </div>
                </div>
            </div>
        `;
    }).join('') + `</div>`;
}

function formatTimestamp(date) {
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
}

function toggleNotifications() {
    const panel = document.getElementById('notification-panel');
    panel.classList.toggle('hidden');
}

function markNotificationAsRead(id) {
    const notif = allNotifications.find(n => n.id === id);
    if (!notif || notif.is_sent) return;
    
    fetch(`/api/notifications/${id}/mark-sent`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(() => {
        // Update local state
        notif.is_sent = true;
        renderNotifications();
    })
    .catch(err => console.error('Error:', err));
}

function markAllAsRead() {
    const unreadIds = allNotifications.filter(n => !n.is_sent).map(n => n.id);
    
    Promise.all(unreadIds.map(id => 
        fetch(`/api/notifications/${id}/mark-sent`, { method: 'PATCH', headers: { 'Content-Type': 'application/json' } })
    ))
    .then(() => {
        allNotifications.forEach(n => n.is_sent = true);
        renderNotifications();
    })
    .catch(err => console.error('Error:', err));
}

function clearNotification(id) {
    allNotifications = allNotifications.filter(n => n.id !== id);
    renderNotifications();
}

function addLocalNotification(type, title, message) {
    const newNotif = {
        id: Date.now(),
        notification_type: type,
        message: message,
        is_sent: false,
        created_at: new Date().toISOString()
    };
    allNotifications.unshift(newNotif);
    renderNotifications();
}

// Booking functions
function loadSpecialties() {
    fetch('/api/specialties')
        .then(res => res.json())
        .then(data => {
            const specialties = data.data || data;
            const select = document.getElementById('booking_specialty');
            select.innerHTML = '<option value="">-- Choose a Specialty --</option>' +
                specialties.map(spec => `<option value="${spec.specialty_id}">${spec.name}</option>`).join('');
        })
        .catch(err => console.error('Error:', err));
}

function loadDoctorsBySpecialty() {
    const specialtyId = document.getElementById('booking_specialty').value;
    const doctorSelect = document.getElementById('booking_doctor');
    
    if (!specialtyId) {
        doctorSelect.innerHTML = '<option value="">-- Choose a Doctor --</option>';
        doctorSelect.disabled = true;
        return;
    }
    
    fetch(`/api/doctors/specialty/${specialtyId}`)
        .then(res => res.json())
        .then(data => {
            const doctors = data.data || data;
            doctorSelect.innerHTML = '<option value="">-- Choose a Doctor --</option>' +
                doctors.map(doc => `<option value="${doc.doctor_id}">Dr. ${doc.first_name} ${doc.last_name}</option>`).join('');
            doctorSelect.disabled = false;
        })
        .catch(err => console.error('Error:', err));
}

function enableDateSelection() {
    const doctorId = document.getElementById('booking_doctor').value;
    const dateInput = document.getElementById('booking_date');
    
    if (doctorId) {
        dateInput.disabled = false;
        dateInput.value = '';
        document.getElementById('booking_time').innerHTML = '<option value="">-- Choose a Time --</option>';
        document.getElementById('booking_time').disabled = true;
    } else {
        dateInput.disabled = true;
    }
}

function loadAvailableSlots() {
    const doctorId = document.getElementById('booking_doctor').value;
    const date = document.getElementById('booking_date').value;
    const timeSelect = document.getElementById('booking_time');
    const bookBtn = document.getElementById('bookBtn');
    
    if (!doctorId || !date) return;
    
    timeSelect.innerHTML = '<option value="">Loading slots...</option>';
    timeSelect.disabled = true;
    
    fetch(`/api/doctors/${doctorId}/available-slots/${date}`)
        .then(res => res.json())
        .then(data => {
            console.log('Available slots response:', data);
            const slots = data.slots || [];
            const message = data.message || '';
            
            if (slots.length === 0) {
                timeSelect.innerHTML = `<option value="">${message || 'No available slots for this date'}</option>`;
                timeSelect.disabled = true;
                bookBtn.disabled = true;
            } else {
                // slots are just time strings like "09:00", "10:00", etc.
                timeSelect.innerHTML = '<option value="">-- Choose a Time --</option>' +
                    slots.map(slot => {
                        // Format time for display (convert 24h to 12h)
                        const [hours, minutes] = slot.split(':');
                        const hour = parseInt(hours);
                        const ampm = hour >= 12 ? 'PM' : 'AM';
                        const displayHour = hour % 12 || 12;
                        const displayTime = `${displayHour}:${minutes} ${ampm}`;
                        return `<option value="${slot}">${displayTime}</option>`;
                    }).join('');
                timeSelect.disabled = false;
            }
        })
        .catch(err => {
            console.error('Error loading slots:', err);
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            timeSelect.disabled = true;
        });
}

document.getElementById('booking_time')?.addEventListener('change', function() {
    document.getElementById('bookBtn').disabled = !this.value;
});

document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const doctorId = document.getElementById('booking_doctor').value;
    const date = document.getElementById('booking_date').value;
    const time = document.getElementById('booking_time').value;
    const reason = document.getElementById('booking_reason').value;
    
    if (!doctorId || !date || !time) {
        showToast('warning', 'Missing Fields', 'Please fill all required fields');
        return;
    }
    
    const startTime = `${date} ${time}:00`;
    const endDate = new Date(`${date}T${time}:00`);
    endDate.setHours(endDate.getHours() + 1);
    const endTime = endDate.toISOString().slice(0, 19).replace('T', ' ');
    
    fetch('/api/appointments', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            patient_id: patientId,
            doctor_id: doctorId,
            start_time: startTime,
            end_time: endTime,
            reason: reason || 'General Appointment'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            showToast('error', 'Error', data.error);
        } else {
            showToast('success', 'Success', 'Appointment booked successfully!');
            addLocalNotification('confirmed', 'Appointment Confirmed', 'Your appointment has been confirmed.');
            
            // Reset form
            document.getElementById('bookingForm').reset();
            document.getElementById('booking_doctor').disabled = true;
            document.getElementById('booking_date').disabled = true;
            document.getElementById('booking_time').disabled = true;
            document.getElementById('bookBtn').disabled = true;
            
            // Reload appointments and switch tab
            loadAppointments();
            switchTab('appointments');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('error', 'Error', 'Error booking appointment');
    });
});
</script>
@endsection
