# 🏥 How to Use the Medical Clinic API

## Quick Start

### 1. Start the Server
```bash
php artisan serve
```
You'll see:
```
Starting Laravel development server: http://127.0.0.1:8000
```

---

## Option A: Using Postman (Easiest - GUI)

### 1. Download Postman
https://www.postman.com/downloads/

### 2. See All Doctors

**Step 1:** Create new request
- Click **+ New**
- Click **Request**
- Name it "Get All Doctors"

**Step 2:** Set up request
- Method: **GET**
- URL: `http://localhost:8000/api/doctors`
- Click **Send**

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "doctor_id": 1,
      "first_name": "Ahmed",
      "last_name": "Samir",
      "phone": "0101234567",
      "email": "ahmed.samir@clinic.com",
      "specialty_id": 1,
      "specialty": {
        "specialty_id": 1,
        "name": "Pediatrics"
      }
    },
    {
      "doctor_id": 2,
      "first_name": "Fatma",
      "last_name": "Yehia",
      "phone": "0102234567",
      "email": "fatma.yehia@clinic.com",
      "specialty_id": 2,
      "specialty": {
        "specialty_id": 2,
        "name": "Orthopedics"
      }
    }
    // ... 5 more doctors
  ],
  "last_page": 1,
  "total": 7
}
```

---

### 3. Add a New Doctor

**Step 1:** Create new request
- Click **+ New**
- Click **Request**
- Name it "Create Doctor"

**Step 2:** Set up request
- Method: **POST**
- URL: `http://localhost:8000/api/doctors`

**Step 3:** Add Headers
- Click **Headers** tab
- Add: `Content-Type: application/json`

**Step 4:** Add Body
- Click **Body** tab
- Select **raw** (JSON format)
- Paste this:

```json
{
  "first_name": "Ali",
  "last_name": "Hassan",
  "specialty_id": 1,
  "phone": "0105555555",
  "email": "ali.hassan@clinic.com"
}
```

**Step 5:** Click **Send**

**Response (Success):**
```json
{
  "first_name": "Ali",
  "last_name": "Hassan",
  "specialty_id": 1,
  "phone": "0105555555",
  "email": "ali.hassan@clinic.com",
  "doctor_id": 8,
  "updated_at": "2025-11-21T10:30:00.000000Z",
  "created_at": "2025-11-21T10:30:00.000000Z"
}
```

✅ **Doctor created successfully!** (Status: 201)

---

## Option B: Using Command Line (PowerShell)

### 1. See All Doctors

```powershell
curl -X GET "http://localhost:8000/api/doctors" -Headers @{"Content-Type"="application/json"}
```

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {"doctor_id": 1, "first_name": "Ahmed", "last_name": "Samir", ...},
    {"doctor_id": 2, "first_name": "Fatma", "last_name": "Yehia", ...},
    ...
  ],
  "total": 7
}
```

---

### 2. Add a New Doctor

```powershell
$body = @{
    first_name = "Ali"
    last_name = "Hassan"
    specialty_id = 1
    phone = "0105555555"
    email = "ali.hassan@clinic.com"
} | ConvertTo-Json

curl -X POST "http://localhost:8000/api/doctors" `
  -Headers @{"Content-Type"="application/json"} `
  -Body $body
```

**Response:**
```json
{
  "doctor_id": 8,
  "first_name": "Ali",
  "last_name": "Hassan",
  "specialty_id": 1,
  "phone": "0105555555",
  "email": "ali.hassan@clinic.com",
  "created_at": "2025-11-21T10:30:00.000000Z"
}
```

---

## Option C: Using a Simple HTML Form (In Browser)

Create a file named `test_api.html`:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Medical Clinic API Tester</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .container { max-width: 600px; }
        textarea { width: 100%; height: 200px; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏥 Medical Clinic API Tester</h1>
        
        <h2>1. Get All Doctors</h2>
        <button onclick="getAllDoctors()">Get All Doctors</button>
        
        <h2>2. Add New Doctor</h2>
        <input type="text" id="firstName" placeholder="First Name" value="Ali">
        <input type="text" id="lastName" placeholder="Last Name" value="Hassan">
        <input type="number" id="specialtyId" placeholder="Specialty ID (1-7)" value="1">
        <input type="text" id="phone" placeholder="Phone" value="0105555555">
        <input type="email" id="email" placeholder="Email" value="ali.hassan@clinic.com">
        <button onclick="addDoctor()">Add Doctor</button>
        
        <h2>Response:</h2>
        <textarea id="response" readonly></textarea>
    </div>

    <script>
        const API_URL = "http://localhost:8000/api";
        
        async function getAllDoctors() {
            try {
                const response = await fetch(`${API_URL}/doctors`);
                const data = await response.json();
                displayResponse(data);
            } catch (error) {
                displayError(error.message);
            }
        }
        
        async function addDoctor() {
            try {
                const doctor = {
                    first_name: document.getElementById('firstName').value,
                    last_name: document.getElementById('lastName').value,
                    specialty_id: document.getElementById('specialtyId').value,
                    phone: document.getElementById('phone').value,
                    email: document.getElementById('email').value
                };
                
                const response = await fetch(`${API_URL}/doctors`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(doctor)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    displayResponse(data);
                    alert('✅ Doctor added successfully!');
                } else {
                    displayError(data);
                }
            } catch (error) {
                displayError(error.message);
            }
        }
        
        function displayResponse(data) {
            document.getElementById('response').textContent = JSON.stringify(data, null, 2);
        }
        
        function displayError(error) {
            document.getElementById('response').textContent = "ERROR: " + JSON.stringify(error, null, 2);
        }
    </script>
</body>
</html>
```

Save this file and open in browser → Click buttons to test!

---

## Understanding the Response

### Response Fields

**Doctor Object:**
```json
{
  "doctor_id": 1,                    // ← Unique ID
  "first_name": "Ahmed",             // ← Doctor's first name
  "last_name": "Samir",              // ← Doctor's last name
  "specialty_id": 1,                 // ← Medical specialty
  "phone": "0101234567",             // ← Contact number
  "email": "ahmed.samir@clinic.com", // ← Email address
  "specialty": {                     // ← Related specialty details
    "specialty_id": 1,
    "name": "Pediatrics"
  },
  "created_at": "2025-11-20T...",    // ← When created
  "updated_at": "2025-11-20T..."     // ← When last updated
}
```

---

## Common Errors & Solutions

### Error 1: Email Already Exists
```
{
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```
**Solution:** Use a different email (must be unique)

### Error 2: Specialty Doesn't Exist
```
{
  "message": "The specialty id field must exist in specialties table.",
  "errors": {
    "specialty_id": ["The specialty id field must exist in specialties table."]
  }
}
```
**Solution:** Use specialty_id 1-7 (valid specialties)

### Error 3: Missing Required Field
```
{
  "message": "The first name field is required.",
  "errors": {
    "first_name": ["The first name field is required."]
  }
}
```
**Solution:** Include all required fields

### Error 4: Server Not Running
```
Error: connect ECONNREFUSED 127.0.0.1:8000
```
**Solution:** Run `php artisan serve` first

---

## All Available Specialties (IDs 1-7)

| ID | Name |
|---|---|
| 1 | Pediatrics |
| 2 | Orthopedics |
| 3 | Neurology |
| 4 | Ophthalmology |
| 5 | Psychiatry |
| 6 | Dermatology |
| 7 | Cardiology |

---

## More API Examples

### Get Single Doctor
```
GET /api/doctors/{id}

Example:
GET http://localhost:8000/api/doctors/1
```

### Update Doctor
```
PUT /api/doctors/{id}

Body:
{
  "phone": "0105555555",
  "email": "newemail@clinic.com"
}
```

### Delete Doctor
```
DELETE /api/doctors/{id}

Example:
DELETE http://localhost:8000/api/doctors/1
```

### Get Doctors by Specialty
```
GET /api/specialties/{specialtyId}/doctors

Example:
GET http://localhost:8000/api/specialties/1/doctors

Returns all Pediatrics doctors
```

### Get Doctor's Appointments
```
GET /api/doctors/{id}/appointments

Example:
GET http://localhost:8000/api/doctors/1/appointments

Returns all appointments for doctor 1
```

---

## HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | ✅ Success (GET) | Doctor retrieved |
| 201 | ✅ Created (POST) | New doctor created |
| 400 | ❌ Bad Request | Invalid data |
| 404 | ❌ Not Found | Doctor doesn't exist |
| 409 | ❌ Conflict | Email already exists |
| 422 | ❌ Validation Error | Missing required fields |
| 500 | ❌ Server Error | Server problem |

---

## Quick Checklist

- [ ] Server running (`php artisan serve`)
- [ ] Postman downloaded/opened
- [ ] Create GET request to `http://localhost:8000/api/doctors`
- [ ] Create POST request to add doctor
- [ ] Add `Content-Type: application/json` header
- [ ] Add doctor data in body
- [ ] Click Send
- [ ] See response ✅

---

## Next Steps

Once you can add doctors:
1. **Book an appointment** - POST to `/api/appointments`
2. **Get patients** - GET `/api/patients`
3. **Create medical records** - POST to `/api/medical-records`
4. **Build frontend** - React/Vue interface for users

Enjoy! 🎉
