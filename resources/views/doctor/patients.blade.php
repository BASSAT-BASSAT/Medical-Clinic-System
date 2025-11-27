@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Patients</h1>
                <p class="text-gray-600 mt-2">View all your patients and their records</p>
            </div>
            <a href="{{ route('doctor.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex gap-4">
                <input type="text" id="searchInput" placeholder="Search patient..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                <button onclick="searchPatients()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Search
                </button>
            </div>
        </div>

        <!-- Patient Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Total Patients</p>
                <p class="text-3xl font-bold text-blue-600 mt-2" id="total-patients">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Upcoming Appointments</p>
                <p class="text-3xl font-bold text-green-600 mt-2" id="upcoming-count">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Medical Records</p>
                <p class="text-3xl font-bold text-purple-600 mt-2" id="records-count">0</p>
            </div>
        </div>

        <!-- Patients Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">All Patients</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Phone</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Appointments</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patients-table" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Patient Detail Modal -->
        <div id="patientDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closePatientModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Patient Details</h3>
                <div id="patient-detail-content" class="space-y-4">
                    <!-- Loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const doctorId = {{ auth()->user()->doctor->doctor_id ?? 'null' }};
let allPatients = [];

document.addEventListener('DOMContentLoaded', function() {
    loadPatients();
});

function loadPatients() {
    fetch(`/api/appointments/by-doctor/${doctorId}`)
        .then(res => res.json())
        .then(data => {
            const appointments = data.data || data;
            const patientMap = {};
            
            appointments.forEach(apt => {
                if (apt.patient && apt.patient.patient_id) {
                    if (!patientMap[apt.patient.patient_id]) {
                        patientMap[apt.patient.patient_id] = {
                            ...apt.patient,
                            appointments: 0,
                            upcoming: 0
                        };
                    }
                    patientMap[apt.patient.patient_id].appointments++;
                    if (new Date(apt.start_time) > new Date() && apt.status !== 'cancelled') {
                        patientMap[apt.patient.patient_id].upcoming++;
                    }
                }
            });
            
            allPatients = Object.values(patientMap);
            
            document.getElementById('total-patients').textContent = allPatients.length;
            document.getElementById('upcoming-count').textContent = allPatients.reduce((sum, p) => sum + p.upcoming, 0);
            document.getElementById('records-count').textContent = allPatients.length; // Placeholder
            
            displayPatients(allPatients);
        })
        .catch(err => console.error('Error:', err));
}

function displayPatients(patients) {
    const table = document.getElementById('patients-table');
    
    if (patients.length === 0) {
        table.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No patients</td></tr>';
        return;
    }
    
    table.innerHTML = patients.map(patient => `
        <tr>
            <td class="px-6 py-4 text-sm font-medium text-gray-900">${patient.first_name} ${patient.last_name}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${patient.email || '-'}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${patient.phone || '-'}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${patient.appointments}</td>
            <td class="px-6 py-4 text-sm">
                <button onclick="viewPatientDetails(${patient.patient_id})" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                    View
                </button>
            </td>
        </tr>
    `).join('');
}

function searchPatients() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const filtered = allPatients.filter(p => 
        p.first_name.toLowerCase().includes(query) || 
        p.last_name.toLowerCase().includes(query) ||
        (p.email && p.email.toLowerCase().includes(query))
    );
    displayPatients(filtered);
}

function viewPatientDetails(patientId) {
    fetch(`/api/patients/${patientId}`)
        .then(res => res.json())
        .then(patient => {
            const content = document.getElementById('patient-detail-content');
            content.innerHTML = `
                <div>
                    <h4 class="font-semibold text-gray-900">${patient.first_name} ${patient.last_name}</h4>
                    <p class="text-sm text-gray-600">${patient.email}</p>
                    <p class="text-sm text-gray-600">${patient.phone || 'No phone'}</p>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-2">Medical Records</h4>
                    <div id="medical-records-list" class="space-y-2 max-h-64 overflow-y-auto">
                        <p class="text-sm text-gray-500">Loading...</p>
                    </div>
                </div>
            `;
            
            // Load medical records
            fetch(`/api/patients/${patientId}/medical-records`)
                .then(res => res.json())
                .then(records => {
                    const recordsList = document.getElementById('medical-records-list');
                    const recordsData = records.data || records;
                    if (recordsData.length === 0) {
                        recordsList.innerHTML = '<p class="text-sm text-gray-500">No medical records</p>';
                    } else {
                        recordsList.innerHTML = recordsData.map(record => `
                            <div class="text-sm border border-gray-200 p-2 rounded">
                                <p class="font-medium">${record.diagnosis || 'N/A'}</p>
                                <p class="text-gray-600 text-xs">${new Date(record.created_at).toLocaleDateString()}</p>
                            </div>
                        `).join('');
                    }
                })
                .catch(err => console.error('Error loading records:', err));
            
            document.getElementById('patientDetailModal').classList.remove('hidden');
        })
        .catch(err => console.error('Error:', err));
}

function closePatientModal() {
    document.getElementById('patientDetailModal').classList.add('hidden');
}
</script>
@endsection
