import { useState, useEffect } from 'react';
import { User, Appointment, MedicalRecord } from '../App';
import { Calendar, Clock, FileText, LogOut, Plus, Search, X } from 'lucide-react';
import { AppointmentCalendar } from './AppointmentCalendar';
import { BookAppointment } from './BookAppointment';
import { Notifications, Notification } from './Notifications';
import { mockAppointments, mockDoctors, mockMedicalRecords } from '../data/mockData';

interface PatientDashboardProps {
  user: User;
  onLogout: () => void;
}

export function PatientDashboard({ user, onLogout }: PatientDashboardProps) {
  const [activeTab, setActiveTab] = useState<'appointments' | 'medical-records' | 'book'>('appointments');
  const [appointments, setAppointments] = useState<Appointment[]>(
    mockAppointments.filter(apt => apt.patientId === user.id)
  );
  const [medicalRecords] = useState<MedicalRecord[]>(
    mockMedicalRecords.filter(record => record.patientId === user.id)
  );
  const [searchQuery, setSearchQuery] = useState('');
  const [showBooking, setShowBooking] = useState(false);
  const [notifications, setNotifications] = useState<Notification[]>([]);

  // Generate notifications on component mount and when appointments change
  useEffect(() => {
    const newNotifications: Notification[] = [];

    // Add login notification
    newNotifications.push({
      id: `login-${Date.now()}`,
      type: 'login',
      title: 'Welcome back!',
      message: `You logged in at ${new Date().toLocaleTimeString()}`,
      timestamp: new Date(),
      read: false,
    });

    // Check for upcoming appointments (today or tomorrow)
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    appointments.forEach((apt) => {
      const aptDate = new Date(apt.date);
      aptDate.setHours(0, 0, 0, 0);

      if (apt.status === 'scheduled') {
        if (aptDate.getTime() === today.getTime()) {
          newNotifications.push({
            id: `upcoming-today-${apt.id}`,
            type: 'upcoming',
            title: 'Appointment Today!',
            message: `You have an appointment with ${apt.doctorName} today at ${apt.time}`,
            timestamp: new Date(Date.now() - 3600000), // 1 hour ago
            read: false,
          });
        } else if (aptDate.getTime() === tomorrow.getTime()) {
          newNotifications.push({
            id: `upcoming-tomorrow-${apt.id}`,
            type: 'upcoming',
            title: 'Appointment Tomorrow',
            message: `You have an appointment with ${apt.doctorName} tomorrow at ${apt.time}`,
            timestamp: new Date(Date.now() - 7200000), // 2 hours ago
            read: false,
          });
        }
      }
    });

    // Only update if we don't have notifications yet (initial load)
    if (notifications.length === 0) {
      setNotifications(newNotifications);
    }
  }, []);

  // Update notifications when appointments are cancelled
  useEffect(() => {
    if (notifications.length === 0) return; // Skip if notifications haven't been initialized

    const cancelledAppointments = appointments.filter(apt => apt.status === 'cancelled');
    const existingNotificationIds = new Set(notifications.map(n => n.id));

    cancelledAppointments.forEach((apt) => {
      const notificationId = `cancelled-${apt.id}-${Date.now()}`;
      const oldNotificationId = `cancelled-${apt.id}`;
      
      // Check if we already have a notification for this cancelled appointment
      const hasExistingNotification = Array.from(existingNotificationIds).some(
        id => id.startsWith(`cancelled-${apt.id}`)
      );

      if (!hasExistingNotification) {
        const newNotification: Notification = {
          id: notificationId,
          type: 'cancelled',
          title: 'Appointment Cancelled',
          message: `Your appointment with ${apt.doctorName} on ${new Date(apt.date).toLocaleDateString()} at ${apt.time} has been cancelled`,
          timestamp: new Date(),
          read: false,
        };
        setNotifications(prev => [newNotification, ...prev]);
      }
    });
  }, [appointments, notifications.length]);

  const handleCancelAppointment = (appointmentId: string) => {
    setAppointments(prev =>
      prev.map(apt =>
        apt.id === appointmentId ? { ...apt, status: 'cancelled' as const } : apt
      )
    );
  };

  const handleBookAppointment = (appointment: Appointment) => {
    setAppointments(prev => [...prev, appointment]);
    setShowBooking(false);

    // Add confirmation notification
    const newNotification: Notification = {
      id: `confirmed-${appointment.id}`,
      type: 'confirmed',
      title: 'Appointment Confirmed',
      message: `Your appointment with ${appointment.doctorName} on ${new Date(appointment.date).toLocaleDateString()} at ${appointment.time} has been confirmed`,
      timestamp: new Date(),
      read: false,
    };
    setNotifications(prev => [newNotification, ...prev]);
  };

  const handleMarkAsRead = (id: string) => {
    setNotifications(prev =>
      prev.map(n => n.id === id ? { ...n, read: true } : n)
    );
  };

  const handleMarkAllAsRead = () => {
    setNotifications(prev => prev.map(n => ({ ...n, read: true })));
  };

  const handleClearNotification = (id: string) => {
    setNotifications(prev => prev.filter(n => n.id !== id));
  };

  const filteredAppointments = appointments.filter(apt =>
    apt.doctorName.toLowerCase().includes(searchQuery.toLowerCase()) ||
    apt.reason.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="bg-blue-600 p-2 rounded-lg">
                <FileText className="w-6 h-6 text-white" />
              </div>
              <div>
                <h1 className="text-gray-900">Patient Portal</h1>
                <p className="text-gray-600 font-bold">Welcome, {user.name}</p>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <Notifications
                notifications={notifications}
                onMarkAsRead={handleMarkAsRead}
                onMarkAllAsRead={handleMarkAllAsRead}
                onClearNotification={handleClearNotification}
              />
              <button
                onClick={onLogout}
                className="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
              >
                <LogOut className="w-5 h-5" />
                Logout
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* Navigation Tabs */}
      <div className="bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <nav className="flex gap-8">
            <button
              onClick={() => setActiveTab('appointments')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'appointments'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              My Appointments
            </button>
            <button
              onClick={() => setActiveTab('medical-records')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'medical-records'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Medical Records
            </button>
            <button
              onClick={() => setActiveTab('book')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'book'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Book Appointment
            </button>
          </nav>
        </div>
      </div>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {activeTab === 'appointments' && (
          <div className="space-y-6">
            <div className="flex items-center justify-between">
              <h2 className="text-gray-900 font-bold">My Appointments</h2>
              <button
                onClick={() => setShowBooking(true)}
                className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
              >
                <Plus className="w-5 h-5" />
                Book New Appointment
              </button>
            </div>

            {/* Search */}
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search appointments by doctor or reason..."
                className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
              />
            </div>

            {/* Appointments List */}
            <div className="grid gap-4">
              {filteredAppointments.map((appointment) => (
                <div
                  key={appointment.id}
                  className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition"
                >
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <div className="flex items-center gap-3 mb-2">
                        <h3 className="text-gray-900">{appointment.doctorName}</h3>
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
                      </div>
                      <div className="space-y-2 text-gray-600">
                        <div className="flex items-center gap-2">
                          <Calendar className="w-4 h-4" />
                          {new Date(appointment.date).toLocaleDateString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                          })}
                        </div>
                        <div className="flex items-center gap-2">
                          <Clock className="w-4 h-4" />
                          {appointment.time}
                        </div>
                        <p className="text-gray-900">Reason: {appointment.reason}</p>
                        {appointment.notes && (
                          <p className="text-gray-600">Notes: {appointment.notes}</p>
                        )}
                      </div>
                    </div>
                    {appointment.status === 'scheduled' && (
                      <button
                        onClick={() => handleCancelAppointment(appointment.id)}
                        className="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                      >
                        Cancel
                      </button>
                    )}
                  </div>
                </div>
              ))}
              {filteredAppointments.length === 0 && (
                <div className="text-center py-12 bg-white rounded-lg border border-gray-200">
                  <Calendar className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-600">No appointments found</p>
                </div>
              )}
            </div>
          </div>
        )}

        {activeTab === 'medical-records' && (
          <div className="space-y-6">
            <h2 className="text-gray-900">Medical Records</h2>
            <div className="grid gap-4">
              {medicalRecords.map((record) => (
                <div
                  key={record.id}
                  className="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                >
                  <div className="flex items-start justify-between mb-4">
                    <div>
                      <h3 className="text-gray-900 mb-1">{record.diagnosis}</h3>
                      <p className="text-gray-600">Dr. {record.doctorName}</p>
                    </div>
                    <span className="text-gray-600">
                      {new Date(record.date).toLocaleDateString()}
                    </span>
                  </div>
                  <div className="space-y-3">
                    <div>
                      <p className="text-gray-700 mb-1">Prescription:</p>
                      <p className="text-gray-600">{record.prescription}</p>
                    </div>
                    {record.notes && (
                      <div>
                        <p className="text-gray-700 mb-1">Notes:</p>
                        <p className="text-gray-600">{record.notes}</p>
                      </div>
                    )}
                  </div>
                </div>
              ))}
              {medicalRecords.length === 0 && (
                <div className="text-center py-12 bg-white rounded-lg border border-gray-200">
                  <FileText className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-600">No medical records found</p>
                </div>
              )}
            </div>
          </div>
        )}

        {activeTab === 'book' && (
          <div className="space-y-6">
            <h2 className="text-gray-900">Book New Appointment</h2>
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <BookAppointment
                user={user}
                existingAppointments={appointments}
                doctors={mockDoctors}
                onBook={handleBookAppointment}
              />
            </div>
          </div>
        )}
      </main>

      {/* Booking Modal */}
      {showBooking && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div className="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
              <h2 className="text-gray-900">Book New Appointment</h2>
              <button
                onClick={() => setShowBooking(false)}
                className="p-2 hover:bg-gray-100 rounded-lg transition"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
            <div className="p-6">
              <BookAppointment
                user={user}
                existingAppointments={appointments}
                doctors={mockDoctors}
                onBook={handleBookAppointment}
              />
            </div>
          </div>
        </div>
      )}
    </div>
  );
}