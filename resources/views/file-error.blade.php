<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pratinjau File' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f3f6f8; /* background abu-abu mirip drive */
            margin: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .container { 
            background: white; 
            padding: 40px 30px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            text-align: center; 
            max-width: 400px; 
            width: 90%; 
            border: 1px solid #e2e8f0; 
        }
        .icon { 
            width: 50px; 
            height: 50px; 
            margin-bottom: 20px; 
            opacity: 0.6; 
        }
        .message { 
            font-size: 15px; 
            font-weight: 500; 
            margin-bottom: 25px; 
            line-height: 1.5; 
            color: #475569; 
        }
        .btn-download { 
            display: inline-flex; 
            align-items: center;
            gap: 8px;
            padding: 10px 24px; 
            background-color: #1a73e8; /* warna biru drive */
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
            font-weight: 600; 
            font-size: 14px; 
            transition: background 0.2s; 
        }
        .btn-download:hover { 
            background-color: #1557b0; 
        }
        .btn-download img {
            width: 16px;
            height: 16px;
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message" style="font-size: 16px; color: #334155;">{{ $message ?? 'File ini tidak dapat dipratinjau secara langsung di browser.' }}</div>
        <a href="{{ url('/files/' . $file->id . '/download') }}" class="btn-download">
            <img src="{{ asset('images/download.png') }}" alt="Download">
            Download File
        </a>
    </div>
</body>
</html>
