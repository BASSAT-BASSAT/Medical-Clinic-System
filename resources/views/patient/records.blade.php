@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Medical Records</h1>
                <p class="text-gray-600 mt-2">View your complete medical history</p>
            </div>
            <a href="{{ route('patient.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Total Records</p>
                <p class="text-3xl font-bold text-blue-600 mt-2" id="total-records">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Last Appointment</p>
                <p class="text-lg font-bold text-gray-900 mt-2" id="last-appointment">-</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Active Conditions</p>
                <p class="text-3xl font-bold text-orange-600 mt-2" id="active-conditions">0</p>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Records</label>
                    <input type="text" id="searchInput" placeholder="Search by diagnosis..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Doctor</label>
                    <select id="doctorFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                        <option value="">-- All Doctors --</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="filterRecords()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Medical Records Timeline -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Medical Records</h2>
            </div>
            <div id="records-timeline" class="divide-y divide-gray-200">
                <div class="p-6 text-center text-gray-500">Loading records...</div>
            </div>
        </div>

        <!-- Record Detail Modal -->
        <div id="recordDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white max-h-96 overflow-y-auto">
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closeRecordModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Medical Record Details</h3>
                <div id="record-detail-content" class="space-y-4">
                    <!-- Loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const patientId = {{ auth()->user()->patient->patient_id ?? 'null' }};
let allRecords = [];

document.addEventListener('DOMContentLoaded', function() {
    loadRecords();
    loadDoctorFilter();
});

function loadRecords() {
    fetch(`/api/patients/${patientId}/medical-records`)
        .then(res => res.json())
        .then(data => {
            allRecords = data.data || data;
            document.getElementById('total-records').textContent = allRecords.length;
            
            // Count active conditions (assuming recent records within last year)
            const oneYearAgo = new Date();
            oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);
            const activeCount = allRecords.filter(r => new Date(r.created_at) > oneYearAgo).length;
            document.getElementById('active-conditions').textContent = activeCount;
            
            // Get last appointment
            if (allRecords.length > 0) {
                const lastRecord = allRecords[0];
                const lastDate = new Date(lastRecord.created_at);
                document.getElementById('last-appointment').textContent = lastDate.toLocaleDateString();
            }
            
            displayRecords(allRecords);
        })
        .catch(err => console.error('Error:', err));
}

function loadDoctorFilter() {
    fetch('/api/doctors')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('doctorFilter');
            const doctors = data.data || data;
            select.innerHTML = '<option value="">-- All Doctors --</option>' +
                doctors.map(doc => `<option value="${doc.doctor_id}">Dr. ${doc.first_name} ${doc.last_name}</option>`).join('');
        })
        .catch(err => console.error('Error:', err));
}

function displayRecords(records) {
    const timeline = document.getElementById('records-timeline');
    
    if (records.length === 0) {
        timeline.innerHTML = '<div class="p-6 text-center text-gray-500">No medical records found</div>';
        return;
    }
    
    timeline.innerHTML = records.map((record, index) => `
        <div class="p-6 hover:bg-gray-50 cursor-pointer" onclick="viewRecordDetail(${index})">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-4">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">${record.diagnosis || 'General Checkup'}</h4>
                    <p class="text-sm text-gray-600">Dr. ${record.doctor?.first_name || '-'} ${record.doctor?.last_name || ''}</p>
                    <p class="text-xs text-gray-500 mt-1">${new Date(record.created_at).toLocaleString()}</p>
                </div>
            </div>
        </div>
    `).join('');
}

function viewRecordDetail(index) {
    const record = allRecords[index];
    const content = document.getElementById('record-detail-content');
    
    content.innerHTML = `
        <div class="space-y-4">
            <div>
                <h4 class="font-semibold text-gray-900">Diagnosis</h4>
                <p class="text-gray-700">${record.diagnosis || 'N/A'}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Treatment</h4>
                <p class="text-gray-700">${record.treatment || 'N/A'}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Prescription</h4>
                <p class="text-gray-700">${record.prescription || 'N/A'}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Doctor</h4>
                <p class="text-gray-700">Dr. ${record.doctor?.first_name || '-'} ${record.doctor?.last_name || ''}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Date</h4>
                <p class="text-gray-700">${new Date(record.created_at).toLocaleString()}</p>
            </div>
            <div class="pt-4 border-t border-gray-200">
                <h4 class="font-semibold text-gray-900 mb-2">Notes</h4>
                <p class="text-gray-700 whitespace-pre-wrap">${record.notes || 'No additional notes'}</p>
            </div>
        </div>
    `;
    
    document.getElementById('recordDetailModal').classList.remove('hidden');
}

function closeRecordModal() {
    document.getElementById('recordDetailModal').classList.add('hidden');
}

function filterRecords() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const doctorId = document.getElementById('doctorFilter').value;
    
    let filtered = allRecords;
    
    if (search) {
        filtered = filtered.filter(r => (r.diagnosis || '').toLowerCase().includes(search));
    }
    
    if (doctorId) {
        filtered = filtered.filter(r => r.doctor_id == doctorId);
    }
    
    displayRecords(filtered);
}
</script>
@endsection
