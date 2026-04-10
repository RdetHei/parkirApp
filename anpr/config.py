import os
from dotenv import load_dotenv

# Load .env from project root
# Assuming config.py is in /anpr/ and .env is in /
load_dotenv(os.path.join(os.path.dirname(__file__), '..', '.env'))

# API Settings
PLATE_RECOGNIZER_TOKEN = os.getenv("PLATE_RECOGNIZER_KEY", "4adc37a587c417106221ce821d6c3dad3aca1d04")
LARAVEL_API_URL = os.getenv("APP_URL", "http://localhost").rstrip('/') + "/api/anpr-detection"
LARAVEL_API_TOKEN = os.getenv("ANPR_API_TOKEN", "")

# YOLO Settings
# Use 'yolov8n.pt' for better CPU performance (Nano model)
YOLO_MODEL_PATH = 'yolov8n.pt'
CONFIDENCE_THRESHOLD = 0.5
OCR_CONFIDENCE_THRESHOLD = 0.8

# Camera Settings
CAMERA_INDEX = 0 # 0 for default webcam
SCAN_INTERVAL = 2 # Seconds

# Paths
SAVE_CROP_DIR = 'crops'
if not os.path.exists(SAVE_CROP_DIR):
    os.makedirs(SAVE_CROP_DIR)
