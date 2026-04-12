import requests
import json
import os
import time
from config import PLATE_RECOGNIZER_TOKEN, LARAVEL_API_URL, OCR_CONFIDENCE_THRESHOLD

def scan_plate_with_api(image_path):
    """
    Sends cropped plate image to Plate Recognizer API directly
    """
    with open(image_path, 'rb') as fp:
        response = requests.post(
            'https://api.platerecognizer.com/v1/plate-reader/',
            data=dict(mmc='true'),
            files=dict(upload=fp),
            headers={'Authorization': 'Token ' + PLATE_RECOGNIZER_TOKEN}
        )
    
    if response.status_code != 201 and response.status_code != 200:
        print(f"Error from Plate Recognizer: {response.text}")
        return None
        
    return response.json()

def send_to_laravel(data, image_path=None):
    """
    Sends processed data from Plate Recognizer to Laravel Backend
    """
    # Extract results from Plate Recognizer structure
    results = data.get('results', [])
    if not results:
        return False
        
    plate_data = results[0]
    
    # Prepare payload for Laravel handleDetection endpoint
    payload = {
        'plate': plate_data.get('plate', '').upper(),
        'confidence': plate_data.get('score', 0),
        'vehicle_type': plate_data.get('vehicle', {}).get('type', [{}])[0].get('type', 'mobil'),
        'vehicle_color': plate_data.get('vehicle', {}).get('color', [{}])[0].get('color', 'unknown'),
        'vehicle_make': plate_data.get('vehicle', {}).get('make', [{}])[0].get('make', ''),
        'vehicle_model': plate_data.get('vehicle', {}).get('model', [{}])[0].get('model', ''),
        'raw_json': json.dumps(data) # Store full response for logging
    }

    # If confidence is below threshold, skip sending to Laravel for Entry/Exit
    if payload['confidence'] < OCR_CONFIDENCE_THRESHOLD:
        print(f"Skipping Laravel sync: Confidence too low ({payload['confidence']})")
        return False

    files = {}
    if image_path and os.path.exists(image_path):
        files = {'image': open(image_path, 'rb')}

    try:
        # Use LARAVEL_API_URL which points to /api/anpr-detection
        response = requests.post(
            LARAVEL_API_URL,
            data=payload,
            files=files,
            timeout=10
        )
        if response.status_code == 200 or response.status_code == 201:
            print(f"Successfully synced with Laravel: {payload['plate']}")
            return True
        else:
            print(f"Laravel API Error: {response.status_code} - {response.text}")
            return False
    except Exception as e:
        print(f"Failed to connect to Laravel: {e}")
        return False
