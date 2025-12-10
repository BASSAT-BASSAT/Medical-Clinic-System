"""
MediCare Clinic AI Chatbot Service
Uses Google Gemini API with LangChain to help patients find the right doctor
"""

import os
import json
import requests
from datetime import datetime, timedelta
from flask import Flask, request, jsonify
from flask_cors import CORS
from dotenv import load_dotenv
from langchain_google_genai import ChatGoogleGenerativeAI
from langchain.prompts import ChatPromptTemplate, MessagesPlaceholder
from langchain.schema import HumanMessage, AIMessage, SystemMessage
from langchain.memory import ConversationBufferWindowMemory

load_dotenv()

app = Flask(__name__)
CORS(app, origins=["http://127.0.0.1:8000", "http://localhost:8000"])

# Initialize Gemini model
llm = ChatGoogleGenerativeAI(
    model="gemini-2.5-flash",
    google_api_key=os.getenv("GOOGLE_API_KEY"),
    temperature=0.7,
    convert_system_message_to_human=True
)

LARAVEL_API_URL = os.getenv("LARAVEL_API_URL", "http://127.0.0.1:8000/api")

# Store conversation history per session
conversations = {}

# Day name mapping
DAY_NAMES = {
    'monday': 0, 'tuesday': 1, 'wednesday': 2, 'thursday': 3,
    'friday': 4, 'saturday': 5, 'sunday': 6,
    'mon': 0, 'tue': 1, 'wed': 2, 'thu': 3, 'fri': 4, 'sat': 5, 'sun': 6
}

# Medical specialty mapping
SPECIALTY_KEYWORDS = {
    "cardiology": ["heart", "chest pain", "blood pressure", "hypertension", "palpitations", "cardiac", "cardiovascular", "cardiologist"],
    "dermatology": ["skin", "rash", "acne", "eczema", "hair loss", "psoriasis", "mole", "dermatitis", "dermatologist"],
    "neurology": ["headache", "migraine", "seizure", "numbness", "dizziness", "memory", "nerve", "brain", "neurologist"],
    "pediatrics": ["child", "baby", "infant", "kid", "vaccination", "growth", "pediatric", "pediatrician"],
    "orthopedics": ["bone", "joint", "back pain", "fracture", "arthritis", "spine", "knee", "shoulder", "muscle", "orthopedic"],
    "ophthalmology": ["eye", "vision", "glasses", "blurry", "cataract", "glaucoma", "ophthalmologist"],
    "ent": ["ear", "nose", "throat", "hearing", "sinus", "tonsil", "allergy"],
    "gastroenterology": ["stomach", "digestion", "nausea", "vomiting", "diarrhea", "constipation", "liver", "abdomen"],
    "pulmonology": ["breathing", "lungs", "cough", "asthma", "respiratory", "shortness of breath"],
    "urology": ["kidney", "bladder", "urination", "prostate"],
    "gynecology": ["menstrual", "pregnancy", "women", "pelvic", "gynecological"],
    "psychiatry": ["anxiety", "depression", "stress", "mental health", "sleep", "insomnia", "mood"],
    "general medicine": ["fever", "cold", "flu", "fatigue", "general checkup", "weakness", "checkup", "general"]
}


def get_all_doctors() -> list:
    """Fetch all doctors from Laravel API"""
    try:
        response = requests.get(f"{LARAVEL_API_URL}/doctors", timeout=5)
        if response.status_code == 200:
            data = response.json()
            # Handle paginated response
            if isinstance(data, dict) and 'data' in data:
                return data['data']
            elif isinstance(data, list):
                return data
            return []
    except Exception as e:
        print(f"Error fetching doctors: {e}")
    return []


def get_doctors_by_specialty(specialty_name: str) -> list:
    """Fetch doctors from Laravel API by specialty"""
    try:
        doctors = get_all_doctors()
        # Filter by specialty (case-insensitive partial match)
        filtered = [
            d for d in doctors 
            if specialty_name.lower() in d.get('specialty', {}).get('name', '').lower()
        ]
        return filtered[:5]  # Return top 5
    except Exception as e:
        print(f"Error fetching doctors: {e}")
    return []


def get_doctor_availability(doctor_id: int) -> list:
    """Fetch doctor's availability schedule"""
    try:
        response = requests.get(f"{LARAVEL_API_URL}/doctors/{doctor_id}/availability", timeout=5)
        if response.status_code == 200:
            data = response.json()
            # Handle paginated response
            if isinstance(data, dict) and 'data' in data:
                return data['data']
            elif isinstance(data, list):
                return data
            return []
    except Exception as e:
        print(f"Error fetching availability: {e}")
    return []


def get_doctors_available_on_day(day_name: str) -> list:
    """Get all doctors available on a specific day"""
    day_num = DAY_NAMES.get(day_name.lower())
    if day_num is None:
        return []
    
    available_doctors = []
    doctors = get_all_doctors()
    
    for doctor in doctors:
        doctor_id = doctor.get('doctor_id') or doctor.get('id')
        if not doctor_id:
            continue
            
        availability = get_doctor_availability(doctor_id)
        
        # Check if doctor works on this day
        for slot in availability:
            slot_day = slot.get('day_of_week')
            # Handle both string and int day formats
            if isinstance(slot_day, str):
                slot_day = DAY_NAMES.get(slot_day.lower(), -1)
            
            if slot_day == day_num:
                doctor_info = {
                    "id": doctor_id,
                    "name": f"Dr. {doctor.get('first_name', '')} {doctor.get('last_name', '')}".strip(),
                    "specialty": doctor.get('specialty', {}).get('name', 'General'),
                    "start_time": slot.get('start_time', '09:00'),
                    "end_time": slot.get('end_time', '17:00'),
                    "consultation_fee": doctor.get('consultation_fee', 0)
                }
                if doctor_info not in available_doctors:
                    available_doctors.append(doctor_info)
                break
    
    return available_doctors


def detect_day_query(text: str) -> str | None:
    """Detect if user is asking about availability on a specific day"""
    text_lower = text.lower()
    
    # Check for day names
    for day_name in DAY_NAMES.keys():
        if day_name in text_lower:
            return day_name
    
    # Check for "today" or "tomorrow"
    if 'today' in text_lower:
        return datetime.now().strftime('%A').lower()
    if 'tomorrow' in text_lower:
        tomorrow = datetime.now() + timedelta(days=1)
        return tomorrow.strftime('%A').lower()
    
    return None


def detect_specialty(text: str) -> str | None:
    """Detect medical specialty from text using keyword matching"""
    text_lower = text.lower()
    scores = {}
    
    for specialty, keywords in SPECIALTY_KEYWORDS.items():
        score = sum(1 for keyword in keywords if keyword in text_lower)
        if score > 0:
            scores[specialty] = score
    
    if scores:
        return max(scores, key=scores.get)
    return None


def get_conversation_history(session_id: str) -> list:
    """Get or create conversation history for a session"""
    if session_id not in conversations:
        conversations[session_id] = []
    return conversations[session_id]


def add_to_history(session_id: str, role: str, content: str):
    """Add message to conversation history"""
    history = get_conversation_history(session_id)
    history.append({"role": role, "content": content})
    # Keep only last 10 messages to avoid token limits
    if len(history) > 10:
        conversations[session_id] = history[-10:]


def format_doctors_for_ai(doctors: list, context: str = "") -> str:
    """Format doctor list for AI to include in response"""
    if not doctors:
        return "No doctors found matching the criteria."
    
    result = f"Here are the available doctors{context}:\n\n"
    for doc in doctors:
        result += f"• **{doc['name']}** - {doc['specialty']}"
        if doc.get('start_time') and doc.get('end_time'):
            result += f" (Available: {doc['start_time']} - {doc['end_time']})"
        if doc.get('consultation_fee'):
            result += f" - Fee: ${doc['consultation_fee']}"
        result += "\n"
    
    return result


@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({"status": "ok", "service": "MediCare Chatbot"})


@app.route('/chat', methods=['POST'])
def chat():
    """Main chat endpoint"""
    try:
        data = request.json
        user_message = data.get('message', '').strip()
        session_id = data.get('session_id', 'default')
        patient_id = data.get('patient_id')
        
        if not user_message:
            return jsonify({"error": "Message is required"}), 400
        
        # Get conversation history
        history = get_conversation_history(session_id)
        
        # Build context from history
        context = "\n".join([
            f"{'Patient' if m['role'] == 'user' else 'MediBot'}: {m['content']}" 
            for m in history[-5:]
        ])
        
        # Detect queries
        detected_specialty = detect_specialty(user_message)
        detected_day = detect_day_query(user_message)
        
        # Gather real data based on query
        doctors_info = ""
        available_doctors = []
        
        if detected_day:
            # User asking about availability on specific day
            doctors_on_day = get_doctors_available_on_day(detected_day)
            if doctors_on_day:
                doctors_info = format_doctors_for_ai(doctors_on_day, f" on {detected_day.capitalize()}")
                available_doctors = doctors_on_day
            else:
                doctors_info = f"Unfortunately, I couldn't find any doctors with availability listed for {detected_day.capitalize()}. You may want to call the clinic directly."
        elif detected_specialty:
            # User describing symptoms - find specialists
            specialty_doctors = get_doctors_by_specialty(detected_specialty)
            if specialty_doctors:
                available_doctors = [
                    {
                        "id": d.get("doctor_id") or d.get("id"),
                        "name": f"Dr. {d.get('first_name', '')} {d.get('last_name', '')}".strip(),
                        "specialty": d.get("specialty", {}).get("name", detected_specialty),
                        "consultation_fee": d.get("consultation_fee", 0)
                    }
                    for d in specialty_doctors
                ]
                doctors_info = format_doctors_for_ai(available_doctors, f" for {detected_specialty.capitalize()}")
        
        # Build enhanced prompt with real data
        enhanced_prompt = f"""You are MediBot, a friendly AI medical assistant for MediCare Clinic.

Guidelines:
- Be empathetic, professional, and concise (2-3 sentences)
- Never diagnose - only recommend specialists
- If symptoms sound urgent, advise emergency care
- Use the REAL doctor data provided below in your response

Conversation history:
{context if context else "New conversation"}

{"REAL DOCTOR DATA FROM SYSTEM:" if doctors_info else ""}
{doctors_info}

Patient's message: {user_message}

Respond helpfully. If doctor data is provided above, include the actual doctor names and times in your response."""

        # Generate response with real data
        prompt = ChatPromptTemplate.from_messages([
            ("human", "{input}")
        ])
        
        chain = prompt | llm
        response = chain.invoke({"input": enhanced_prompt})
        
        bot_response = response.content
        
        # Add to history
        add_to_history(session_id, "user", user_message)
        add_to_history(session_id, "assistant", bot_response)
        
        # Prepare response data
        response_data = {
            "message": bot_response,
            "session_id": session_id
        }
        
        # Include doctor cards if we found any
        if available_doctors:
            response_data["specialty_detected"] = detected_specialty or "available"
            response_data["available_doctors"] = available_doctors
        
        return jsonify(response_data)
        
    except Exception as e:
        print(f"Chat error: {e}")
        import traceback
        traceback.print_exc()
        return jsonify({
            "message": "I apologize, but I'm having trouble processing your request. Please try again or contact the clinic directly.",
            "error": str(e)
        }), 500


@app.route('/doctors/<int:doctor_id>/availability', methods=['GET'])
def doctor_availability(doctor_id):
    """Get specific doctor's availability"""
    availability = get_doctor_availability(doctor_id)
    return jsonify(availability)


@app.route('/reset', methods=['POST'])
def reset_conversation():
    """Reset conversation history for a session"""
    data = request.json
    session_id = data.get('session_id', 'default')
    
    if session_id in conversations:
        del conversations[session_id]
    
    return jsonify({"status": "ok", "message": "Conversation reset"})


if __name__ == '__main__':
    print("🤖 MediCare Chatbot Service Starting...")
    print(f"📡 Laravel API URL: {LARAVEL_API_URL}")
    print("🚀 Running on http://127.0.0.1:5000")
    app.run(host='127.0.0.1', port=5000, debug=True)
