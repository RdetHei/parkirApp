import requests
import json
import os
from config import PLATE_RECOGNIZER_TOKEN, LARAVEL_API_URL, OCR_CONFIDENCE_THRESHOLD

def scan_plate_with_api(image_path):
    """
    Sends cropped plate image to Plate Recognizer API
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
    Sends processed data to Laravel Backend
    """
    # Prepare payload based on the requested ALPR structure
    # and what Laravel expects
    payload = {
        'plate': data.get('plate', {}).get('props', {}).get('plate', [{}])[0].get('value', '').upper(),
        'confidence': data.get('plate', {}).get('score', 0),
        'vehicle_type': data.get('vehicle', {}).get('type', 'Unknown'),
        'vehicle_color': data.get('vehicle', {}).get('props', {}).get('color', [{}])[0].get('value', 'Unknown'),
        'vehicle_make': data.get('vehicle', {}).get('props', {}).get('make_model', [{}])[0].get('make', 'Unknown'),
        'vehicle_model': data.get('vehicle', {}).get('props', {}).get('make_model', [{}])[0].get('model', 'Unknown'),
        'timestamp': data.get('timestamp', ''),
        'raw_json': data # Full structure for logging
    }

    # If confidence is below threshold, skip sending to Laravel for Entry/Exit
    if payload['confidence'] < OCR_CONFIDENCE_THRESHOLD:
        print(f"Skipping Laravel sync: Confidence too low ({payload['confidence']})")
        return False

    files = {}
    if image_path and os.path.exists(image_path):
        files = {'image': open(image_path, 'rb')}

    try:
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
