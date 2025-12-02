import { useState } from 'react';
import { FileText, Download, TrendingUp, Users, Calendar, CheckCircle, XCircle, Clock } from 'lucide-react';
import { mockPatients, mockDoctors, mockAppointments, mockMedicalRecords } from '../data/mockData';

type ReportType = 'patient' | 'doctor' | null;

export function AdminReports() {
  const [reportType, setReportType] = useState<ReportType>(null);
  const [selectedPatient, setSelectedPatient] = useState('');
  const [selectedDoctor, setSelectedDoctor] = useState('');
  const [dateRange, setDateRange] = useState({ start: '', end: '' });
  const [generatedReport, setGeneratedReport] = useState<any>(null);

  // Patient Report Generation
  const generatePatientReport = () => {
    if (!selectedPatient) return;

    const patient = mockPatients.find(p => p.id === selectedPatient);
    if (!patient) return;

    const patientAppointments = mockAppointments.filter(apt => apt.patientId === selectedPatient);
    const patientRecords = mockMedicalRecords.filter(rec => rec.patientId === selectedPatient);

    // Apply date filter if provided
    let filteredAppointments = patientAppointments;
    let filteredRecords = patientRecords;

    if (dateRange.start) {
      filteredAppointments = filteredAppointments.filter(apt => apt.date >= dateRange.start);
      filteredRecords = filteredRecords.filter(rec => rec.date >= dateRange.start);
    }
    if (dateRange.end) {
      filteredAppointments = filteredAppointments.filter(apt => apt.date <= dateRange.end);
      filteredRecords = filteredRecords.filter(rec => rec.date <= dateRange.end);
    }

    const report = {
      type: 'patient',
      generatedAt: new Date().toLocaleString(),
      patient: {
        name: patient.name,
        email: patient.email,
        phone: patient.phone,
        dateOfBirth: patient.dateOfBirth,
      },
      statistics: {
        totalAppointments: filteredAppointments.length,
        completedAppointments: filteredAppointments.filter(apt => apt.status === 'completed').length,
        scheduledAppointments: filteredAppointments.filter(apt => apt.status === 'scheduled').length,
        cancelledAppointments: filteredAppointments.filter(apt => apt.status === 'cancelled').length,
        totalMedicalRecords: filteredRecords.length,
      },
      appointments: filteredAppointments,
      medicalRecords: filteredRecords,
    };

    setGeneratedReport(report);
  };

  // Doctor Performance Report Generation
  const generateDoctorReport = () => {
    if (!selectedDoctor) return;

    const doctor = mockDoctors.find(d => d.id === selectedDoctor);
    if (!doctor) return;

    const doctorAppointments = mockAppointments.filter(apt => apt.doctorId === selectedDoctor);

    // Apply date filter if provided
    let filteredAppointments = doctorAppointments;
    if (dateRange.start) {
      filteredAppointments = filteredAppointments.filter(apt => apt.date >= dateRange.start);
    }
    if (dateRange.end) {
      filteredAppointments = filteredAppointments.filter(apt => apt.date <= dateRange.end);
    }

    const completed = filteredAppointments.filter(apt => apt.status === 'completed').length;
    const cancelled = filteredAppointments.filter(apt => apt.status === 'cancelled').length;
    const scheduled = filteredAppointments.filter(apt => apt.status === 'scheduled').length;
    const total = filteredAppointments.length;

    // Calculate performance metrics
    const completionRate = total > 0 ? ((completed / total) * 100).toFixed(1) : '0';
    const cancellationRate = total > 0 ? ((cancelled / total) * 100).toFixed(1) : '0';
    
    // Get unique patients
    const uniquePatients = new Set(filteredAppointments.map(apt => apt.patientId)).size;

    // Count medical records created by this doctor
    const medicalRecordsCreated = mockMedicalRecords.filter(
      rec => rec.doctorName === doctor.name
    ).length;

    const report = {
      type: 'doctor',
      generatedAt: new Date().toLocaleString(),
      doctor: {
        name: doctor.name,
        specialization: doctor.specialization,
        email: doctor.email,
        phone: doctor.phone,
      },
      statistics: {
        totalAppointments: total,
        completedAppointments: completed,
        scheduledAppointments: scheduled,
        cancelledAppointments: cancelled,
        completionRate: `${completionRate}%`,
        cancellationRate: `${cancellationRate}%`,
        uniquePatients,
        medicalRecordsCreated,
      },
      appointments: filteredAppointments,
    };

    setGeneratedReport(report);
  };

  const handleGenerateReport = () => {
    if (reportType === 'patient') {
      generatePatientReport();
    } else if (reportType === 'doctor') {
      generateDoctorReport();
    }
  };

  const handleDownloadReport = () => {
    if (!generatedReport) return;

    const reportContent = JSON.stringify(generatedReport, null, 2);
    const blob = new Blob([reportContent], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${generatedReport.type}_report_${Date.now()}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  };

  const resetReport = () => {
    setReportType(null);
    setSelectedPatient('');
    setSelectedDoctor('');
    setDateRange({ start: '', end: '' });
    setGeneratedReport(null);
  };

  return (
    <div className="space-y-6">
      {/* Report Type Selection */}
      {!reportType && (
        <div>
          <h3 className="text-gray-900 mb-6">Select Report Type</h3>
          <div className="grid md:grid-cols-2 gap-6">
            <button
              onClick={() => setReportType('patient')}
              className="bg-white rounded-lg shadow-sm border-2 border-gray-200 p-8 hover:border-blue-600 hover:shadow-md transition text-left group"
            >
              <div className="flex items-center gap-4 mb-4">
                <div className="bg-blue-100 p-3 rounded-lg group-hover:bg-blue-600 transition">
                  <Users className="w-8 h-8 text-blue-600 group-hover:text-white" />
                </div>
                <h3 className="text-gray-900">Patient Medical History Report</h3>
              </div>
              <p className="text-gray-600">
                Generate comprehensive reports about patient medical history, appointments, and treatments
              </p>
            </button>

            <button
              onClick={() => setReportType('doctor')}
              className="bg-white rounded-lg shadow-sm border-2 border-gray-200 p-8 hover:border-blue-600 hover:shadow-md transition text-left group"
            >
              <div className="flex items-center gap-4 mb-4">
                <div className="bg-green-100 p-3 rounded-lg group-hover:bg-green-600 transition">
                  <TrendingUp className="w-8 h-8 text-green-600 group-hover:text-white" />
                </div>
                <h3 className="text-gray-900">Doctor Performance Report</h3>
              </div>
              <p className="text-gray-600">
                Evaluate doctor performance with appointment statistics, completion rates, and patient metrics
              </p>
            </button>
          </div>
        </div>
      )}

      {/* Report Configuration */}
      {reportType && !generatedReport && (
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-6">
            <h3 className="text-gray-900">
              {reportType === 'patient' ? 'Patient Medical History Report' : 'Doctor Performance Report'}
            </h3>
            <button
              onClick={resetReport}
              className="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
            >
              Cancel
            </button>
          </div>

          <div className="space-y-6">
            {/* Select Patient or Doctor */}
            {reportType === 'patient' ? (
              <div>
                <label htmlFor="patient" className="block text-gray-700 mb-2">
                  Select Patient
                </label>
                <select
                  id="patient"
                  value={selectedPatient}
                  onChange={(e) => setSelectedPatient(e.target.value)}
                  className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                  required
                >
                  <option value="">Choose a patient</option>
                  {mockPatients.map((patient) => (
                    <option key={patient.id} value={patient.id}>
                      {patient.name} - {patient.email}
                    </option>
                  ))}
                </select>
              </div>
            ) : (
              <div>
                <label htmlFor="doctor" className="block text-gray-700 mb-2">
                  Select Doctor
                </label>
                <select
                  id="doctor"
                  value={selectedDoctor}
                  onChange={(e) => setSelectedDoctor(e.target.value)}
                  className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                  required
                >
                  <option value="">Choose a doctor</option>
                  {mockDoctors.map((doctor) => (
                    <option key={doctor.id} value={doctor.id}>
                      {doctor.name} - {doctor.specialization}
                    </option>
                  ))}
                </select>
              </div>
            )}

            {/* Date Range Filter */}
            <div>
              <label className="block text-gray-700 mb-2">Date Range (Optional)</label>
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <label htmlFor="startDate" className="block text-gray-600 mb-1">
                    Start Date
                  </label>
                  <input
                    id="startDate"
                    type="date"
                    value={dateRange.start}
                    onChange={(e) => setDateRange({ ...dateRange, start: e.target.value })}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                  />
                </div>
                <div>
                  <label htmlFor="endDate" className="block text-gray-600 mb-1">
                    End Date
                  </label>
                  <input
                    id="endDate"
                    type="date"
                    value={dateRange.end}
                    onChange={(e) => setDateRange({ ...dateRange, end: e.target.value })}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                  />
                </div>
              </div>
            </div>

            <button
              onClick={handleGenerateReport}
              disabled={reportType === 'patient' ? !selectedPatient : !selectedDoctor}
              className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <FileText className="w-5 h-5" />
              Generate Report
            </button>
          </div>
        </div>
      )}

      {/* Generated Report Display */}
      {generatedReport && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h3 className="text-gray-900">Generated Report</h3>
            <div className="flex items-center gap-2">
              <button
                onClick={handleDownloadReport}
                className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
              >
                <Download className="w-5 h-5" />
                Download
              </button>
              <button
                onClick={resetReport}
                className="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
              >
                New Report
              </button>
            </div>
          </div>

          {/* Report Header */}
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center gap-3">
                <div className={`p-2 rounded-lg ${
                  generatedReport.type === 'patient' ? 'bg-blue-100' : 'bg-green-100'
                }`}>
                  {generatedReport.type === 'patient' ? (
                    <Users className={`w-6 h-6 ${
                      generatedReport.type === 'patient' ? 'text-blue-600' : 'text-green-600'
                    }`} />
                  ) : (
                    <TrendingUp className="w-6 h-6 text-green-600" />
                  )}
                </div>
                <div>
                  <h4 className="text-gray-900">
                    {generatedReport.type === 'patient' ? 'Patient Medical History' : 'Doctor Performance'}
                  </h4>
                  <p className="text-gray-600">Generated on {generatedReport.generatedAt}</p>
                </div>
              </div>
            </div>

            {/* Subject Info */}
            <div className="bg-gray-50 rounded-lg p-4">
              {generatedReport.type === 'patient' ? (
                <div className="grid md:grid-cols-2 gap-4">
                  <div>
                    <p className="text-gray-600 mb-1">Patient Name</p>
                    <p className="text-gray-900">{generatedReport.patient.name}</p>
                  </div>
                  <div>
                    <p className="text-gray-600 mb-1">Email</p>
                    <p className="text-gray-900">{generatedReport.patient.email}</p>
                  </div>
                  <div>
                    <p className="text-gray-600 mb-1">Phone</p>
                    <p className="text-gray-900">{generatedReport.patient.phone}</p>
                  </div>
                  <div>
                    <p className="text-gray-600 mb-1">Date of Birth</p>
                    <p className="text-gray-900">
                      {new Date(generatedReport.patient.dateOfBirth).toLocaleDateString()}
                    </p>
                  </div>
                </div>
              ) : (
                <div className="grid md:grid-cols-2 gap-4">
                  <div>
                    <p className="text-gray-600 mb-1">Doctor Name</p>
                    <p className="text-gray-900">{generatedReport.doctor.name}</p>
                  </div>
                  <div>
                    <p className="text-gray-600 mb-1">Specialization</p>
                    <p className="text-gray-900">{generatedReport.doctor.specialization}</p>
                  </div>
                  <div>
                    <p className="text-gray-600 mb-1">Email</p>
                    <p className="text-gray-900">{generatedReport.doctor.email}</p>
                  </div>
                  <div>
                    <p className="text-gray-600 mb-1">Phone</p>
                    <p className="text-gray-900">{generatedReport.doctor.phone}</p>
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Statistics */}
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h4 className="text-gray-900 mb-4">Statistics Overview</h4>
            {generatedReport.type === 'patient' ? (
              <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div className="bg-blue-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <Calendar className="w-5 h-5 text-blue-600" />
                    <p className="text-blue-600">Total Appointments</p>
                  </div>
                  <p className="text-blue-900">{generatedReport.statistics.totalAppointments}</p>
                </div>
                <div className="bg-green-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <CheckCircle className="w-5 h-5 text-green-600" />
                    <p className="text-green-600">Completed</p>
                  </div>
                  <p className="text-green-900">{generatedReport.statistics.completedAppointments}</p>
                </div>
                <div className="bg-yellow-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <Clock className="w-5 h-5 text-yellow-600" />
                    <p className="text-yellow-600">Scheduled</p>
                  </div>
                  <p className="text-yellow-900">{generatedReport.statistics.scheduledAppointments}</p>
                </div>
                <div className="bg-red-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <XCircle className="w-5 h-5 text-red-600" />
                    <p className="text-red-600">Cancelled</p>
                  </div>
                  <p className="text-red-900">{generatedReport.statistics.cancelledAppointments}</p>
                </div>
                <div className="bg-purple-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <FileText className="w-5 h-5 text-purple-600" />
                    <p className="text-purple-600">Medical Records</p>
                  </div>
                  <p className="text-purple-900">{generatedReport.statistics.totalMedicalRecords}</p>
                </div>
              </div>
            ) : (
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div className="bg-blue-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <Calendar className="w-5 h-5 text-blue-600" />
                    <p className="text-blue-600">Total Appointments</p>
                  </div>
                  <p className="text-blue-900">{generatedReport.statistics.totalAppointments}</p>
                </div>
                <div className="bg-green-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <CheckCircle className="w-5 h-5 text-green-600" />
                    <p className="text-green-600">Completion Rate</p>
                  </div>
                  <p className="text-green-900">{generatedReport.statistics.completionRate}</p>
                </div>
                <div className="bg-red-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <XCircle className="w-5 h-5 text-red-600" />
                    <p className="text-red-600">Cancellation Rate</p>
                  </div>
                  <p className="text-red-900">{generatedReport.statistics.cancellationRate}</p>
                </div>
                <div className="bg-purple-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <Users className="w-5 h-5 text-purple-600" />
                    <p className="text-purple-600">Unique Patients</p>
                  </div>
                  <p className="text-purple-900">{generatedReport.statistics.uniquePatients}</p>
                </div>
                <div className="bg-yellow-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <Clock className="w-5 h-5 text-yellow-600" />
                    <p className="text-yellow-600">Scheduled</p>
                  </div>
                  <p className="text-yellow-900">{generatedReport.statistics.scheduledAppointments}</p>
                </div>
                <div className="bg-indigo-50 p-4 rounded-lg">
                  <div className="flex items-center gap-2 mb-2">
                    <FileText className="w-5 h-5 text-indigo-600" />
                    <p className="text-indigo-600">Records Created</p>
                  </div>
                  <p className="text-indigo-900">{generatedReport.statistics.medicalRecordsCreated}</p>
                </div>
              </div>
            )}
          </div>

          {/* Detailed Data */}
          {generatedReport.type === 'patient' && generatedReport.medicalRecords.length > 0 && (
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h4 className="text-gray-900 mb-4">Medical Records</h4>
              <div className="space-y-4">
                {generatedReport.medicalRecords.map((record: any) => (
                  <div key={record.id} className="border border-gray-200 rounded-lg p-4">
                    <div className="flex items-start justify-between mb-3">
                      <span className="text-gray-900">
                        {new Date(record.date).toLocaleDateString('en-US', {
                          year: 'numeric',
                          month: 'long',
                          day: 'numeric',
                        })}
                      </span>
                      <span className="text-gray-600">{record.doctorName}</span>
                    </div>
                    <div className="space-y-2">
                      <div>
                        <p className="text-gray-700">Diagnosis:</p>
                        <p className="text-gray-900">{record.diagnosis}</p>
                      </div>
                      <div>
                        <p className="text-gray-700">Prescription:</p>
                        <p className="text-gray-900">{record.prescription}</p>
                      </div>
                      <div>
                        <p className="text-gray-700">Notes:</p>
                        <p className="text-gray-600">{record.notes}</p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Appointments List */}
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h4 className="text-gray-900 mb-4">Appointments</h4>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th className="px-6 py-3 text-left text-gray-700">Date</th>
                    <th className="px-6 py-3 text-left text-gray-700">Time</th>
                    {generatedReport.type === 'patient' ? (
                      <th className="px-6 py-3 text-left text-gray-700">Doctor</th>
                    ) : (
                      <th className="px-6 py-3 text-left text-gray-700">Patient</th>
                    )}
                    <th className="px-6 py-3 text-left text-gray-700">Reason</th>
                    <th className="px-6 py-3 text-left text-gray-700">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {generatedReport.appointments.map((appointment: any) => (
                    <tr key={appointment.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 text-gray-900">
                        {new Date(appointment.date).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 text-gray-600">{appointment.time}</td>
                      <td className="px-6 py-4 text-gray-900">
                        {generatedReport.type === 'patient' ? appointment.doctorName : appointment.patientName}
                      </td>
                      <td className="px-6 py-4 text-gray-600">{appointment.reason}</td>
                      <td className="px-6 py-4">
                        <span
                          className={`px-3 py-1 rounded-full ${
                            appointment.status === 'scheduled'
                              ? 'bg-green-100 text-green-800'
                              : appointment.status === 'completed'
                              ? 'bg-blue-100 text-blue-800'
                              : 'bg-red-100 text-red-800'
                          }`}
                        >
                          {appointment.status}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
