@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Patient Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome, {{ Auth::user()->name }}!</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Upcoming Appointments</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2" id="upcoming-count">0</p>
                    </div>
                    <div class="text-4xl text-blue-200">📅</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Medical Records</p>
                        <p class="text-3xl font-bold text-green-600 mt-2" id="records-count">0</p>
                    </div>
                    <div class="text-4xl text-green-200">📋</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">New Notifications</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2" id="notification-count">0</p>
                    </div>
                    <div class="text-4xl text-orange-200">🔔</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Upcoming Appointments -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Upcoming Appointments</h2>
                        <a href="{{ route('patient.appointments.book') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            + Book Appointment
                        </a>
                    </div>
                    <div id="appointments-list" class="divide-y divide-gray-200">
                        <p class="p-6 text-center text-gray-500">Loading appointments...</p>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('patient.appointments.book') }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Book Appointment
                        </a>
                        <a href="{{ route('patient.records') }}" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            View Medical Records
                        </a>
                        <a href="{{ route('patient.notifications') }}" class="block w-full text-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            View Notifications
                        </a>
                    </div>
                </div>

                <!-- Recent Notifications -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Notifications</h3>
                    <div id="notifications-list" class="space-y-3">
                        <p class="text-center text-gray-500 text-sm">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Book Appointment Modal -->
<div id="bookModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" role="dialog" aria-labelledby="bookModalTitle" aria-modal="true">
    <div class="relative top-20 mx-auto p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closeBookModal()" aria-label="Close appointment booking">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h3 id="bookModalTitle" class="text-lg font-medium text-gray-900 mb-4">Book an Appointment</h3>
        <form id="appointmentForm" class="space-y-4">
            @csrf
            <div>
                <label for="modal_doctor_id" class="block text-sm font-medium text-gray-700">Select Doctor</label>
                <select id="modal_doctor_id" name="doctor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required aria-label="Select doctor">
                    <option value="">-- Choose a Doctor --</option>
                </select>
            </div>

            <div>
                <label for="modal_appointment_date" class="block text-sm font-medium text-gray-700">Select Date</label>
                <input type="date" id="modal_appointment_date" name="appointment_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required aria-label="Select appointment date">
            </div>

            <div>
                <label for="modal_appointment_time" class="block text-sm font-medium text-gray-700">Select Time</label>
                <select id="modal_appointment_time" name="appointment_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required aria-label="Select appointment time">
                    <option value="">-- Choose a Time --</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Reason for Visit</label>
                <textarea name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Describe your symptoms or reason for visit..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Book Appointment
                </button>
                <button type="button" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400" onclick="closeBookModal()">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadUpcomingAppointments();
    loadNotifications();
    loadMedicalRecords();
    loadDoctors();
});

function loadUpcomingAppointments() {
    const patientId = {{ Auth::user()->patient->patient_id ?? 'null' }};
    if (!patientId) return;

    fetch(`/api/appointments/upcoming/patient/${patientId}`)
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('appointments-list');
            document.getElementById('upcoming-count').textContent = data.length;

            if (data.length === 0) {
                list.innerHTML = '<p class="p-6 text-center text-gray-500">No upcoming appointments</p>';
                return;
            }

            list.innerHTML = data.map(apt => `
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-gray-900">Dr. ${apt.doctor.first_name} ${apt.doctor.last_name}</p>
                            <p class="text-sm text-gray-600 mt-1">${new Date(apt.start_time).toLocaleString()}</p>
                            <p class="text-sm text-gray-600">${apt.reason || 'General Appointment'}</p>
                        </div>
                        <button onclick="cancelAppointment(${apt.appointment_id})" class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Cancel
                        </button>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => console.error('Error loading appointments:', err));
}

function loadNotifications() {
    const patientId = {{ Auth::user()->patient->patient_id ?? 'null' }};
    if (!patientId) return;

    fetch(`/api/notifications/patient/${patientId}?limit=5`)
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('notifications-list');
            const data_array = data.data || data;
            document.getElementById('notification-count').textContent = data_array.length;

            if (data_array.length === 0) {
                list.innerHTML = '<p class="text-center text-gray-500 text-sm">No notifications</p>';
                return;
            }

            list.innerHTML = data_array.map(notif => `
                <div class="p-3 bg-blue-50 rounded">
                    <p class="text-sm text-gray-900">${notif.message}</p>
                    <p class="text-xs text-gray-500 mt-1">${new Date(notif.created_at).toLocaleDateString()}</p>
                </div>
            `).join('');
        })
        .catch(err => console.error('Error loading notifications:', err));
}

function loadMedicalRecords() {
    const patientId = {{ Auth::user()->patient->patient_id ?? 'null' }};
    if (!patientId) return;

    fetch(`/api/patients/${patientId}/medical-records`)
        .then(res => res.json())
        .then(data => {
            const data_array = data.data || data;
            document.getElementById('records-count').textContent = data_array.length;
        })
        .catch(err => console.error('Error loading records:', err));
}

function loadDoctors() {
    fetch('/api/doctors')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('modal_doctor_id');
            const data_array = data.data || data;
            select.innerHTML = '<option value="">-- Choose a Doctor --</option>' + 
                data_array.map(doc => `<option value="${doc.doctor_id}">Dr. ${doc.first_name} ${doc.last_name}</option>`).join('');
        })
        .catch(err => console.error('Error loading doctors:', err));
}

function cancelAppointment(appointmentId) {
    if (!confirm('Are you sure you want to cancel this appointment?')) return;

    fetch(`/api/appointments/${appointmentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: 'cancelled' })
    })
    .then(res => res.json())
    .then(data => {
        alert('Appointment cancelled successfully');
        loadUpcomingAppointments();
    })
    .catch(err => console.error('Error cancelling appointment:', err));
}

function closeBookModal() {
    document.getElementById('bookModal').classList.add('hidden');
}

document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const doctorId = document.getElementById('modal_doctor_id').value;
    const date = document.getElementById('modal_appointment_date').value;
    const time = document.getElementById('modal_appointment_time').value;

    if (!doctorId || !date || !time) {
        alert('Please fill all fields');
        return;
    }

    const patientId = {{ Auth::user()->patient->patient_id ?? 'null' }};
    const startTime = `${date} ${time}:00`;
    const endTime = new Date(new Date(`${date}T${time}:00`) + 60*60*1000).toISOString().slice(0, 19).replace('T', ' ');

    fetch('/api/appointments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            patient_id: patientId,
            doctor_id: doctorId,
            start_time: startTime,
            end_time: endTime,
            reason: document.querySelector('textarea[name="reason"]').value || 'General Appointment'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
        } else {
            alert('Appointment booked successfully!');
            closeBookModal();
            loadUpcomingAppointments();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error booking appointment');
    });
});

document.getElementById('modal_appointment_date')?.addEventListener('change', function() {
    const doctorId = document.getElementById('modal_doctor_id').value;
    const date = this.value;

    if (!doctorId || !date) return;

    fetch(`/api/doctors/${doctorId}/available-slots/${date}`)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('modal_appointment_time');
            select.innerHTML = '<option value="">-- Choose a Time --</option>' + 
                data.slots.map(slot => {
                    const time = slot.start_time.split(' ')[1].substring(0, 5);
                    return `<option value="${time}">${time}</option>`;
                }).join('');
        })
        .catch(err => console.error('Error loading slots:', err));
});
</script>
@endsection
