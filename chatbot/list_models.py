"""
Script to list all available Gemini models for your API key
"""
import os
from dotenv import load_dotenv
import google.generativeai as genai

# Load environment variables
load_dotenv()

# Configure API key
api_key = os.getenv("GOOGLE_API_KEY")
if not api_key:
    print("❌ Error: GOOGLE_API_KEY not found in .env file")
    exit(1)

genai.configure(api_key=api_key)

print("🔍 Fetching available Gemini models...\n")
print("=" * 70)

try:
    models = genai.list_models()
    
    chat_models = []
    for model in models:
        # Filter models that support generateContent
        if 'generateContent' in model.supported_generation_methods:
            chat_models.append(model)
            print(f"✅ Model: {model.name}")
            print(f"   Display Name: {model.display_name}")
            print(f"   Description: {model.description}")
            print(f"   Supported Methods: {', '.join(model.supported_generation_methods)}")
            print("-" * 70)
    
    print(f"\n📊 Total models supporting generateContent: {len(chat_models)}")
    
    if chat_models:
        print("\n💡 Recommended models for your chatbot:")
        for model in chat_models[:3]:  # Show top 3
            # Extract just the model name (e.g., "gemini-pro" from "models/gemini-pro")
            model_name = model.name.replace("models/", "")
            print(f"   - {model_name}")
    
except Exception as e:
    print(f"❌ Error: {e}")
    print("\n💡 This usually means:")
    print("   1. Your API key is invalid")
    print("   2. You need to install: pip install google-generativeai")
