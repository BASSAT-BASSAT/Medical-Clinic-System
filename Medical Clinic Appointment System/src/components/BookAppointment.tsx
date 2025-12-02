import { useState } from 'react';
import { User, Appointment } from '../App';
import { Calendar, Clock, AlertCircle } from 'lucide-react';

interface Doctor {
  id: string;
  name: string;
  specialization: string;
  email: string;
  phone: string;
}

interface BookAppointmentProps {
  user: User;
  existingAppointments: Appointment[];
  doctors: Doctor[];
  onBook: (appointment: Appointment) => void;
}

export function BookAppointment({ user, existingAppointments, doctors, onBook }: BookAppointmentProps) {
  const [selectedDoctor, setSelectedDoctor] = useState('');
  const [date, setDate] = useState('');
  const [time, setTime] = useState('');
  const [reason, setReason] = useState('');
  const [error, setError] = useState('');
  const [success, setSuccess] = useState(false);

  const timeSlots = [
    '09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM',
    '12:00 PM', '12:30 PM', '02:00 PM', '02:30 PM', '03:00 PM', '03:30 PM',
    '04:00 PM', '04:30 PM', '05:00 PM'
  ];

  const isSlotAvailable = (doctorId: string, selectedDate: string, selectedTime: string) => {
    return !existingAppointments.some(
      apt => apt.doctorId === doctorId && 
             apt.date === selectedDate && 
             apt.time === selectedTime &&
             apt.status !== 'cancelled'
    );
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setSuccess(false);

    if (!selectedDoctor || !date || !time || !reason) {
      setError('Please fill in all fields');
      return;
    }

    // Validate that the date is not in the past
    const selectedDate = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
      setError('Cannot book appointments in the past');
      return;
    }

    // Check for conflicts
    if (!isSlotAvailable(selectedDoctor, date, time)) {
      setError('This time slot is already booked. Please select another time.');
      return;
    }

    const doctor = doctors.find(d => d.id === selectedDoctor);
    if (!doctor) {
      setError('Selected doctor not found');
      return;
    }

    const newAppointment: Appointment = {
      id: `apt-${Date.now()}`,
      patientId: user.id,
      patientName: user.name,
      doctorId: selectedDoctor,
      doctorName: doctor.name,
      date,
      time,
      reason,
      status: 'scheduled',
    };

    onBook(newAppointment);
    setSuccess(true);
    
    // Reset form
    setSelectedDoctor('');
    setDate('');
    setTime('');
    setReason('');

    // Clear success message after 3 seconds
    setTimeout(() => setSuccess(false), 3000);
  };

  const availableTimeSlots = selectedDoctor && date
    ? timeSlots.filter(slot => isSlotAvailable(selectedDoctor, date, slot))
    : timeSlots;

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {success && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
          <Calendar className="w-5 h-5" />
          Appointment booked successfully! You will receive a confirmation notification.
        </div>
      )}

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
          <AlertCircle className="w-5 h-5" />
          {error}
        </div>
      )}

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
          {doctors.map((doctor) => (
            <option key={doctor.id} value={doctor.id}>
              Dr. {doctor.name} - {doctor.specialization}
            </option>
          ))}
        </select>
      </div>

      <div>
        <label htmlFor="date" className="block text-gray-700 mb-2">
          Select Date
        </label>
        <div className="relative">
          <Calendar className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            id="date"
            type="date"
            value={date}
            onChange={(e) => setDate(e.target.value)}
            min={new Date().toISOString().split('T')[0]}
            className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
            required
          />
        </div>
      </div>

      <div>
        <label htmlFor="time" className="block text-gray-700 mb-2">
          Select Time
        </label>
        <div className="relative">
          <Clock className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
          <select
            id="time"
            value={time}
            onChange={(e) => setTime(e.target.value)}
            className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
            required
          >
            <option value="">Choose a time slot</option>
            {availableTimeSlots.map((slot) => (
              <option key={slot} value={slot}>
                {slot}
              </option>
            ))}
          </select>
        </div>
        {selectedDoctor && date && availableTimeSlots.length === 0 && (
          <p className="mt-2 text-orange-600">No available time slots for this date. Please select another date.</p>
        )}
      </div>

      <div>
        <label htmlFor="reason" className="block text-gray-700 mb-2">
          Reason for Visit
        </label>
        <textarea
          id="reason"
          value={reason}
          onChange={(e) => setReason(e.target.value)}
          rows={4}
          className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"
          placeholder="Describe your symptoms or reason for visit..."
          required
        />
      </div>

      <button
        type="submit"
        className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition"
      >
        Book Appointment
      </button>
    </form>
  );
}
