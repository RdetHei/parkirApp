import cv2
import os
import time
from ultralytics import YOLO
from config import YOLO_MODEL_PATH, CAMERA_SOURCE, SCAN_INTERVAL, CONFIDENCE_THRESHOLD, SAVE_CROP_DIR
from plate_api import scan_plate_with_api, send_to_laravel

def run_anpr_service():
    """
    Main loop for YOLOv8 detection and Plate Recognizer API integration
    """
    # Load model (Nano version recommended for CPU)
    print(f"Loading YOLOv8 model: {YOLO_MODEL_PATH}...")
    model = YOLO(YOLO_MODEL_PATH)

    # Initialize Camera
    # Convert string source to integer if it represents a device index
    source = CAMERA_SOURCE
    if str(source).isdigit():
        source = int(source)
    
    print(f"Opening camera source: {source}...")
    cap = cv2.VideoCapture(source)
    if not cap.isOpened():
        print(f"Error: Could not open camera at {source}.")
        print("Tip: If using local webcam, try index 0, 1, or 2.")
        print("Tip: If using IP Webcam, ensure the URL is correct (e.g., http://localhost:8080/video).")
        return

    last_scan_time = 0
    scanned_plates = {} # Simple cache to avoid redundant scans (plate: last_scan_timestamp)

    # Class IDs for YOLOv8 (COCO dataset)
    # 2: car, 3: motorcycle, 5: bus, 7: truck
    vehicle_classes = [2, 3, 5, 7]

    print("ANPR Service Started. Press 'q' to quit.")

    while True:
        ret, frame = cap.read()
        if not ret:
            print("Error: Could not read frame.")
            break

        current_time = time.time()

        # Run YOLOv8 Detection
        results = model.predict(frame, conf=CONFIDENCE_THRESHOLD, verbose=False)

        detections = results[0].boxes

        for box in detections:
            cls = int(box.cls[0])
            if cls in vehicle_classes:
                # Vehicle detected
                x1, y1, x2, y2 = map(int, box.xyxy[0])

                # Draw Bounding Box for Vehicle
                label = f"{model.names[cls]}"
                cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
                cv2.putText(frame, label, (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)

                # Real-time scan check (every SCAN_INTERVAL seconds)
                if current_time - last_scan_time >= SCAN_INTERVAL:
                    # Crop vehicle area (for plate OCR)
                    vehicle_crop = frame[y1:y2, x1:x2]

                    if vehicle_crop.size > 0:
                        crop_filename = os.path.join(SAVE_CROP_DIR, f"scan_{int(current_time)}.jpg")
                        cv2.imwrite(crop_filename, vehicle_crop)

                        print(f"Scanning vehicle crop: {crop_filename}")

                        # OCR Process (Directly via Plate Recognizer API)
                        ocr_result = scan_plate_with_api(crop_filename)

                        if ocr_result and 'results' in ocr_result and len(ocr_result['results']) > 0:
                            data = ocr_result['results'][0]
                            plate = data.get('plate', '').upper()
                            confidence = data.get('score', 0)

                            # Simple logic to only process if not scanned in last 10 seconds
                            if plate and (plate not in scanned_plates or (current_time - scanned_plates[plate]) > 10):
                                print(f"Detected Plate: {plate} (Confidence: {confidence})")

                                # Send to Laravel for Entry/Exit logic & Dashboard update
                                if send_to_laravel(ocr_result, crop_filename):
                                    scanned_plates[plate] = current_time

                                    # Show result on screen for feedback
                                    cv2.putText(frame, f"SYNCED: {plate}", (x1, y2 + 25), cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 255, 255), 2)

                        last_scan_time = current_time

        # Display Preview
        cv2.imshow("Neston ANPR AI (YOLOv8 + Plate Recognizer)", frame)

        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()

if __name__ == "__main__":
    run_anpr_service()
