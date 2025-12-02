import { useState } from 'react';
import { Bell, Send, Users, User as UserIcon, Calendar, FileText } from 'lucide-react';
import { mockDoctors, mockPatients } from '../data/mockData';

interface SentNotification {
  id: string;
  recipientType: 'doctor' | 'patient' | 'all';
  recipientId?: string;
  recipientName?: string;
  title: string;
  message: string;
  sentAt: Date;
}

export function NotificationManager() {
  const [recipientType, setRecipientType] = useState<'doctor' | 'patient' | 'all'>('patient');
  const [selectedRecipient, setSelectedRecipient] = useState('');
  const [title, setTitle] = useState('');
  const [message, setMessage] = useState('');
  const [sentNotifications, setSentNotifications] = useState<SentNotification[]>([]);
  const [success, setSuccess] = useState('');

  const recipients = recipientType === 'doctor' ? mockDoctors : recipientType === 'patient' ? mockPatients : [];

  const handleSendNotification = (e: React.FormEvent) => {
    e.preventDefault();

    if (!title || !message) {
      return;
    }

    if (recipientType !== 'all' && !selectedRecipient) {
      return;
    }

    let recipientName = 'All Users';
    if (recipientType !== 'all') {
      const recipient = recipients.find(r => r.id === selectedRecipient);
      recipientName = recipient?.name || 'Unknown';
    }

    const newNotification: SentNotification = {
      id: `notif-${Date.now()}`,
      recipientType,
      recipientId: recipientType !== 'all' ? selectedRecipient : undefined,
      recipientName,
      title,
      message,
      sentAt: new Date(),
    };

    setSentNotifications(prev => [newNotification, ...prev]);
    
    // Show success message
    setSuccess(`Notification sent to ${recipientName} successfully!`);
    setTimeout(() => setSuccess(''), 3000);

    // Reset form
    setTitle('');
    setMessage('');
    setSelectedRecipient('');
  };

  const quickTemplates = [
    {
      title: 'Appointment Reminder',
      message: 'This is a reminder about your upcoming appointment. Please arrive 10 minutes early.',
    },
    {
      title: 'Appointment Confirmation',
      message: 'Your appointment has been confirmed. We look forward to seeing you.',
    },
    {
      title: 'Schedule Change',
      message: 'There has been a change to your appointment schedule. Please check your appointments.',
    },
    {
      title: 'Lab Results Available',
      message: 'Your lab results are now available. Please log in to view them.',
    },
  ];

  const useTemplate = (template: typeof quickTemplates[0]) => {
    setTitle(template.title);
    setMessage(template.message);
  };

  return (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div className="flex items-center gap-3 mb-6">
          <div className="bg-blue-100 p-2 rounded-lg">
            <Bell className="w-6 h-6 text-blue-600" />
          </div>
          <div>
            <h3 className="text-gray-900">Send Notification</h3>
            <p className="text-gray-600">Send notifications to doctors and patients</p>
          </div>
        </div>

        {success && (
          <div className="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <Send className="w-5 h-5" />
            {success}
          </div>
        )}

        <form onSubmit={handleSendNotification} className="space-y-6">
          {/* Recipient Type */}
          <div>
            <label className="block text-gray-700 mb-2">Send To</label>
            <div className="grid grid-cols-3 gap-3">
              <button
                type="button"
                onClick={() => {
                  setRecipientType('patient');
                  setSelectedRecipient('');
                }}
                className={`p-4 rounded-lg border-2 transition ${
                  recipientType === 'patient'
                    ? 'border-blue-600 bg-blue-50'
                    : 'border-gray-200 hover:border-gray-300'
                }`}
              >
                <UserIcon className="w-6 h-6 mx-auto mb-2 text-blue-600" />
                <p className={recipientType === 'patient' ? 'text-blue-600' : 'text-gray-700'}>
                  Patient
                </p>
              </button>
              <button
                type="button"
                onClick={() => {
                  setRecipientType('doctor');
                  setSelectedRecipient('');
                }}
                className={`p-4 rounded-lg border-2 transition ${
                  recipientType === 'doctor'
                    ? 'border-blue-600 bg-blue-50'
                    : 'border-gray-200 hover:border-gray-300'
                }`}
              >
                <FileText className="w-6 h-6 mx-auto mb-2 text-blue-600" />
                <p className={recipientType === 'doctor' ? 'text-blue-600' : 'text-gray-700'}>
                  Doctor
                </p>
              </button>
              <button
                type="button"
                onClick={() => {
                  setRecipientType('all');
                  setSelectedRecipient('');
                }}
                className={`p-4 rounded-lg border-2 transition ${
                  recipientType === 'all'
                    ? 'border-blue-600 bg-blue-50'
                    : 'border-gray-200 hover:border-gray-300'
                }`}
              >
                <Users className="w-6 h-6 mx-auto mb-2 text-blue-600" />
                <p className={recipientType === 'all' ? 'text-blue-600' : 'text-gray-700'}>
                  All Users
                </p>
              </button>
            </div>
          </div>

          {/* Select Specific Recipient */}
          {recipientType !== 'all' && (
            <div>
              <label htmlFor="recipient" className="block text-gray-700 mb-2">
                Select {recipientType === 'doctor' ? 'Doctor' : 'Patient'}
              </label>
              <select
                id="recipient"
                value={selectedRecipient}
                onChange={(e) => setSelectedRecipient(e.target.value)}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                required
              >
                <option value="">Choose a {recipientType}</option>
                {recipients.map((recipient) => (
                  <option key={recipient.id} value={recipient.id}>
                    {recipient.name} - {recipient.email}
                  </option>
                ))}
              </select>
            </div>
          )}

          {/* Quick Templates */}
          <div>
            <label className="block text-gray-700 mb-2">Quick Templates</label>
            <div className="grid grid-cols-2 gap-2">
              {quickTemplates.map((template, index) => (
                <button
                  key={index}
                  type="button"
                  onClick={() => useTemplate(template)}
                  className="px-3 py-2 text-left border border-gray-300 rounded-lg hover:bg-gray-50 transition text-gray-700"
                >
                  {template.title}
                </button>
              ))}
            </div>
          </div>

          {/* Notification Title */}
          <div>
            <label htmlFor="title" className="block text-gray-700 mb-2">
              Notification Title
            </label>
            <input
              id="title"
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
              placeholder="Enter notification title"
              required
            />
          </div>

          {/* Notification Message */}
          <div>
            <label htmlFor="message" className="block text-gray-700 mb-2">
              Message
            </label>
            <textarea
              id="message"
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              rows={4}
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"
              placeholder="Enter your message..."
              required
            />
          </div>

          <button
            type="submit"
            className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2"
          >
            <Send className="w-5 h-5" />
            Send Notification
          </button>
        </form>
      </div>

      {/* Sent Notifications History */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 className="text-gray-900 mb-4">Sent Notifications History</h3>
        {sentNotifications.length === 0 ? (
          <div className="text-center py-8">
            <Bell className="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <p className="text-gray-600">No notifications sent yet</p>
          </div>
        ) : (
          <div className="space-y-4">
            {sentNotifications.map((notif) => (
              <div
                key={notif.id}
                className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition"
              >
                <div className="flex items-start justify-between mb-2">
                  <div className="flex items-center gap-2">
                    <h4 className="text-gray-900">{notif.title}</h4>
                    <span
                      className={`px-2 py-1 rounded-full text-xs ${
                        notif.recipientType === 'doctor'
                          ? 'bg-purple-100 text-purple-800'
                          : notif.recipientType === 'patient'
                          ? 'bg-blue-100 text-blue-800'
                          : 'bg-green-100 text-green-800'
                      }`}
                    >
                      {notif.recipientType === 'all' ? 'All Users' : notif.recipientType}
                    </span>
                  </div>
                  <span className="text-gray-500">
                    {notif.sentAt.toLocaleTimeString()}
                  </span>
                </div>
                <p className="text-gray-600 mb-2">{notif.message}</p>
                <p className="text-gray-500">
                  Sent to: {notif.recipientName}
                </p>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}