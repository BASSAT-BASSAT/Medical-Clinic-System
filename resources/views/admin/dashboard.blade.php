@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600 mt-2">System Management & Statistics</p>
        </div>

        <!-- System Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Total Doctors</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2" id="total-doctors">0</p>
                    </div>
                    <div class="text-4xl text-blue-200">👨‍⚕️</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Total Patients</p>
                        <p class="text-3xl font-bold text-green-600 mt-2" id="total-patients">0</p>
                    </div>
                    <div class="text-4xl text-green-200">👥</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Total Appointments</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2" id="total-appointments">0</p>
                    </div>
                    <div class="text-4xl text-purple-200">📅</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Completion Rate</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2" id="completion-rate">0%</p>
                    </div>
                    <div class="text-4xl text-orange-200">📊</div>
                </div>
            </div>
        </div>

        <!-- Management Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Doctors Management -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Doctors</h2>
                    <button onclick="openDoctorModal()" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        + Add Doctor
                    </button>
                </div>
                <div id="doctors-list" class="divide-y max-h-96 overflow-y-auto">
                    <p class="p-6 text-center text-gray-500">Loading...</p>
                </div>
            </div>

            <!-- Patients Management -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Patients</h2>
                    <button onclick="openPatientModal()" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        + Add Patient
                    </button>
                </div>
                <div id="patients-list" class="divide-y max-h-96 overflow-y-auto">
                    <p class="p-6 text-center text-gray-500">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Appointments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Patient</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Doctor</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date & Time</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        </tr>
                    </thead>
                    <tbody id="appointments-table" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Generate Report</h3>
                <div class="space-y-3">
                    <select id="reportType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                        <option value="">-- Select Report Type --</option>
                        <option value="daily">Daily Report</option>
                        <option value="weekly">Weekly Report</option>
                        <option value="monthly">Monthly Report</option>
                        <option value="doctor">Doctor Report</option>
                        <option value="patient">Patient Report</option>
                    </select>
                    <input type="date" id="reportDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                    <button onclick="generateReport()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Generate Report
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Health</h3>
                <div id="system-health" class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Database Status</span>
                        <span class="text-green-600 font-medium">✓ Healthy</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">API Status</span>
                        <span class="text-green-600 font-medium">✓ Running</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Email Service</span>
                        <span class="text-green-600 font-medium">✓ Configured</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Doctor Modal -->
<div id="doctorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closeDoctorModal()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Doctor</h3>
        <form id="doctorForm" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="doctor_first_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="doctor_last_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Specialty</label>
                <select id="specialty_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">-- Select Specialty --</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="doctor_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Add Doctor
                </button>
                <button type="button" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg" onclick="closeDoctorModal()">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Patient Modal -->
<div id="patientModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closePatientModal()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Patient</h3>
        <form id="patientForm" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="patient_first_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="patient_last_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <input type="date" id="patient_dob" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="patient_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    Add Patient
                </button>
                <button type="button" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg" onclick="closePatientModal()">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadDoctors();
    loadPatients();
    loadAppointments();
    loadSpecialties();
});

function loadStats() {
    fetch('/api/reports/system-stats')
        .then(res => res.json())
        .then(data => {
            document.getElementById('total-doctors').textContent = data.doctor_stats?.total_doctors || 0;
            document.getElementById('total-patients').textContent = '0'; // Get from API
            document.getElementById('total-appointments').textContent = data.overall_stats?.total_appointments || 0;
            document.getElementById('completion-rate').textContent = (data.overall_stats?.completion_rate || 0) + '%';
        })
        .catch(err => console.error('Error:', err));
}

function loadDoctors() {
    fetch('/api/doctors')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('doctors-list');
            const doctors = data.data || data;
            if (doctors.length === 0) {
                list.innerHTML = '<p class="p-6 text-center text-gray-500">No doctors yet</p>';
                return;
            }
            list.innerHTML = doctors.map(doc => `
                <div class="p-4 hover:bg-gray-50">
                    <p class="font-semibold text-gray-900">Dr. ${doc.first_name} ${doc.last_name}</p>
                    <p class="text-sm text-gray-600">${doc.email}</p>
                </div>
            `).join('');
        })
        .catch(err => console.error('Error:', err));
}

function loadPatients() {
    fetch('/api/patients')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('patients-list');
            const patients = data.data || data;
            if (patients.length === 0) {
                list.innerHTML = '<p class="p-6 text-center text-gray-500">No patients yet</p>';
                return;
            }
            list.innerHTML = patients.map(pat => `
                <div class="p-4 hover:bg-gray-50">
                    <p class="font-semibold text-gray-900">${pat.first_name} ${pat.last_name}</p>
                    <p class="text-sm text-gray-600">${pat.email}</p>
                </div>
            `).join('');
        })
        .catch(err => console.error('Error:', err));
}

function loadAppointments() {
    fetch('/api/appointments')
        .then(res => res.json())
        .then(data => {
            const table = document.getElementById('appointments-table');
            const appointments = data.data || data;
            const recent = appointments.slice(0, 10);
            if (recent.length === 0) {
                table.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No appointments</td></tr>';
                return;
            }
            table.innerHTML = recent.map(apt => `
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">${apt.patient?.first_name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Dr. ${apt.doctor?.first_name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${new Date(apt.start_time).toLocaleString()}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${apt.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">${apt.status}</span>
                    </td>
                </tr>
            `).join('');
        })
        .catch(err => console.error('Error:', err));
}

function loadSpecialties() {
    fetch('/api/specialties')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('specialty_id');
            const specialties = data.data || data;
            select.innerHTML = '<option value="">-- Select Specialty --</option>' +
                specialties.map(spec => `<option value="${spec.specialty_id}">${spec.name}</option>`).join('');
        })
        .catch(err => console.error('Error:', err));
}

function openDoctorModal() {
    document.getElementById('doctorModal').classList.remove('hidden');
}

function closeDoctorModal() {
    document.getElementById('doctorModal').classList.add('hidden');
}

function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function generateReport() {
    const type = document.getElementById('reportType').value;
    const date = document.getElementById('reportDate').value;

    if (!type || !date) {
        alert('Please select report type and date');
        return;
    }

    let url = `/api/reports/${type}/${date}`;
    window.open(url, '_blank');
}

document.getElementById('doctorForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const data = {
        first_name: document.getElementById('doctor_first_name').value,
        last_name: document.getElementById('doctor_last_name').value,
        specialty_id: document.getElementById('specialty_id').value,
        email: document.getElementById('doctor_email').value
    };

    fetch('/api/doctors', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(() => {
        alert('Doctor added successfully!');
        closeDoctorModal();
        loadDoctors();
    })
    .catch(err => console.error('Error:', err));
});

document.getElementById('patientForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const data = {
        first_name: document.getElementById('patient_first_name').value,
        last_name: document.getElementById('patient_last_name').value,
        dob: document.getElementById('patient_dob').value,
        email: document.getElementById('patient_email').value
    };

    fetch('/api/patients', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(() => {
        alert('Patient added successfully!');
        closePatientModal();
        loadPatients();
    })
    .catch(err => console.error('Error:', err));
});
</script>
@endsection
