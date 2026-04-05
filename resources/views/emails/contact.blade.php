<!DOCTYPE html>
<html>
<head>
    <title>Pesan Baru dari Landing Page</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #10b981; border-bottom: 2px solid #10b981; padding-bottom: 10px;">Pesan Baru dari Kontak Landing Page</h2>
        
        <p><strong>Nama:</strong> {{ $data['first_name'] }} {{ $data['last_name'] }}</p>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        
        <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-top: 20px;">
            <p><strong>Pesan:</strong></p>
            <p>{{ $data['message'] }}</p>
        </div>
        
        <p style="font-size: 12px; color: #666; margin-top: 30px; border-top: 1px solid #eee; pt: 10px;">
            Email ini dikirim otomatis dari sistem NESTON.
        </p>
    </div>
</body>
</html>
