import { useState } from 'react';
import { User, MedicalRecord } from '../App';
import { FileText, Plus, Calendar, Search, X } from 'lucide-react';
import { mockMedicalRecords, mockPatients, mockAppointments } from '../data/mockData';

interface PatientRecordsManagerProps {
  doctorId: string;
  doctorName: string;
}

interface PatientInfo {
  id: string;
  name: string;
  email: string;
  phone: string;
  dateOfBirth: string;
}

export function PatientRecordsManager({ doctorId, doctorName }: PatientRecordsManagerProps) {
  const [medicalRecords, setMedicalRecords] = useState<MedicalRecord[]>(mockMedicalRecords);
  const [selectedPatient, setSelectedPatient] = useState<string | null>(null);
  const [showAddForm, setShowAddForm] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  
  // Form state
  const [formData, setFormData] = useState({
    diagnosis: '',
    prescription: '',
    notes: '',
  });

  // Get unique patients who have appointments with this doctor
  const doctorPatients: PatientInfo[] = mockPatients.filter(patient => 
    mockAppointments.some(apt => 
      apt.patientId === patient.id && apt.doctorId === doctorId
    )
  );

  // Filter patients by search query
  const filteredPatients = doctorPatients.filter(patient =>
    patient.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    patient.email.toLowerCase().includes(searchQuery.toLowerCase())
  );

  // Get records for selected patient
  const patientRecords = selectedPatient
    ? medicalRecords.filter(record => record.patientId === selectedPatient)
    : [];

  const selectedPatientInfo = doctorPatients.find(p => p.id === selectedPatient);

  const handleAddRecord = (e: React.FormEvent) => {
    e.preventDefault();

    if (!selectedPatient) return;

    const newRecord: MedicalRecord = {
      id: `mr-${Date.now()}`,
      patientId: selectedPatient,
      date: new Date().toISOString().split('T')[0],
      diagnosis: formData.diagnosis,
      prescription: formData.prescription,
      doctorName: doctorName,
      notes: formData.notes,
    };

    setMedicalRecords(prev => [newRecord, ...prev]);
    setFormData({ diagnosis: '', prescription: '', notes: '' });
    setShowAddForm(false);
  };

  return (
    <div className="grid md:grid-cols-3 gap-6">
      {/* Patients List */}
      <div className="md:col-span-1">
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 className="text-gray-900 mb-4">Your Patients</h3>
          
          {/* Search */}
          <div className="relative mb-4">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input
              type="text"
              placeholder="Search patients..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
            />
          </div>

          {/* Patient List */}
          <div className="space-y-2 max-h-[600px] overflow-y-auto">
            {filteredPatients.length > 0 ? (
              filteredPatients.map((patient) => (
                <button
                  key={patient.id}
                  onClick={() => {
                    setSelectedPatient(patient.id);
                    setShowAddForm(false);
                  }}
                  className={`w-full text-left p-4 rounded-lg border-2 transition ${
                    selectedPatient === patient.id
                      ? 'border-blue-600 bg-blue-50'
                      : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                  }`}
                >
                  <p className={selectedPatient === patient.id ? 'text-blue-900' : 'text-gray-900'}>
                    {patient.name}
                  </p>
                  <p className="text-gray-600">{patient.email}</p>
                  <p className="text-gray-500">
                    DOB: {new Date(patient.dateOfBirth).toLocaleDateString()}
                  </p>
                </button>
              ))
            ) : (
              <div className="text-center py-8">
                <FileText className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                <p className="text-gray-600">No patients found</p>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Patient Records */}
      <div className="md:col-span-2">
        {selectedPatient ? (
          <div className="space-y-6">
            {/* Patient Info Header */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-start justify-between mb-4">
                <div>
                  <h3 className="text-gray-900 mb-2">{selectedPatientInfo?.name}</h3>
                  <div className="space-y-1 text-gray-600">
                    <p>Email: {selectedPatientInfo?.email}</p>
                    <p>Phone: {selectedPatientInfo?.phone}</p>
                    <p>Date of Birth: {selectedPatientInfo && new Date(selectedPatientInfo.dateOfBirth).toLocaleDateString()}</p>
                  </div>
                </div>
                <button
                  onClick={() => setShowAddForm(!showAddForm)}
                  className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                  {showAddForm ? (
                    <>
                      <X className="w-5 h-5" />
                      Cancel
                    </>
                  ) : (
                    <>
                      <Plus className="w-5 h-5" />
                      Add Record
                    </>
                  )}
                </button>
              </div>
            </div>

            {/* Add Record Form */}
            {showAddForm && (
              <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 className="text-gray-900 mb-4">Add New Medical Record</h3>
                <form onSubmit={handleAddRecord} className="space-y-4">
                  <div>
                    <label htmlFor="diagnosis" className="block text-gray-700 mb-2">
                      Diagnosis
                    </label>
                    <input
                      id="diagnosis"
                      type="text"
                      value={formData.diagnosis}
                      onChange={(e) => setFormData({ ...formData, diagnosis: e.target.value })}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                      placeholder="Enter diagnosis"
                      required
                    />
                  </div>

                  <div>
                    <label htmlFor="prescription" className="block text-gray-700 mb-2">
                      Prescription
                    </label>
                    <textarea
                      id="prescription"
                      value={formData.prescription}
                      onChange={(e) => setFormData({ ...formData, prescription: e.target.value })}
                      rows={3}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"
                      placeholder="Enter prescription details"
                      required
                    />
                  </div>

                  <div>
                    <label htmlFor="notes" className="block text-gray-700 mb-2">
                      Notes
                    </label>
                    <textarea
                      id="notes"
                      value={formData.notes}
                      onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                      rows={4}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"
                      placeholder="Additional notes and recommendations"
                      required
                    />
                  </div>

                  <button
                    type="submit"
                    className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition"
                  >
                    Save Record
                  </button>
                </form>
              </div>
            )}

            {/* Medical Records History */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 className="text-gray-900 mb-4">Medical History</h3>
              
              {patientRecords.length > 0 ? (
                <div className="space-y-4">
                  {patientRecords.map((record) => (
                    <div
                      key={record.id}
                      className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition"
                    >
                      <div className="flex items-start justify-between mb-3">
                        <div className="flex items-center gap-2">
                          <Calendar className="w-5 h-5 text-blue-600" />
                          <span className="text-gray-900">
                            {new Date(record.date).toLocaleDateString('en-US', {
                              year: 'numeric',
                              month: 'long',
                              day: 'numeric',
                            })}
                          </span>
                        </div>
                        <span className="text-gray-600">{record.doctorName}</span>
                      </div>
                      
                      <div className="space-y-3">
                        <div>
                          <p className="text-gray-700 mb-1">Diagnosis</p>
                          <p className="text-gray-900">{record.diagnosis}</p>
                        </div>
                        
                        <div>
                          <p className="text-gray-700 mb-1">Prescription</p>
                          <p className="text-gray-900">{record.prescription}</p>
                        </div>
                        
                        <div>
                          <p className="text-gray-700 mb-1">Notes</p>
                          <p className="text-gray-600">{record.notes}</p>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-12">
                  <FileText className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-600">No medical records found</p>
                  <p className="text-gray-500">Click "Add Record" to create the first entry</p>
                </div>
              )}
            </div>
          </div>
        ) : (
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <FileText className="w-16 h-16 text-gray-400 mx-auto mb-4" />
            <h3 className="text-gray-900 mb-2">Select a Patient</h3>
            <p className="text-gray-600">Choose a patient from the list to view and manage their medical records</p>
          </div>
        )}
      </div>
    </div>
  );
}
