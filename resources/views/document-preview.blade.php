<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Dokumen - {{ $file->name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f3f6f8;
            margin: 0; 
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .header {
            background-color: white;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            z-index: 10;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            color: #1e293b;
        }

        .header-title img {
            width: 24px;
            height: 24px;
        }

        .btn-download {
            padding: 8px 16px;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-download:hover {
            background-color: #1557b0;
        }

        .document-container {
            flex: 1;
            overflow-y: auto;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .document-paper {
            background: white;
            width: 100%;
            max-width: 800px;
            min-height: 1000px;
            padding: 60px 80px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 16px;
            line-height: 1.6;
            color: #000;
        }

        /* Basic styling for Mammoth HTML output to look like a document */
        .document-paper h1 { font-size: 24px; margin-top: 20px; margin-bottom: 10px; }
        .document-paper h2 { font-size: 20px; margin-top: 18px; margin-bottom: 10px; }
        .document-paper p { margin-bottom: 12px; }
        .document-paper table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .document-paper table, .document-paper th, .document-paper td { border: 1px solid black; }
        .document-paper th, .document-paper td { padding: 8px; }
        .document-paper img { max-width: 100%; height: auto; }

        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #64748b;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1a73e8;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">
            <span style="font-size: 20px;">📄</span>
            <span>{{ $file->name }}</span>
        </div>
        <a href="{{ url('/files/' . $file->id . '/download') }}" class="btn-download">
            <img src="{{ asset('images/download.png') }}" alt="Download" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">
            Download Asli
        </a>
    </div>

    <div class="document-container">
        <div class="document-paper" id="document-content">
            <div class="loading" id="loading-indicator">
                <div class="spinner"></div>
                <div>Memuat pratinjau dokumen...</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var fileUrl = "{{ url('/files/' . $file->id . '/download') }}";
            
            fetch(fileUrl)
                .then(response => response.arrayBuffer())
                .then(arrayBuffer => {
                    mammoth.convertToHtml({arrayBuffer: arrayBuffer})
                        .then(displayResult)
                        .catch(handleError);
                })
                .catch(handleError);

            function displayResult(result) {
                document.getElementById('loading-indicator').style.display = 'none';
                document.getElementById('document-content').innerHTML = result.value || '<div style="color: #64748b; text-align: center; margin-top: 40px;">Dokumen ini kosong atau tidak dapat di-render dengan sempurna.</div>';
            }

            function handleError(err) {
                document.getElementById('loading-indicator').style.display = 'none';
                document.getElementById('document-content').innerHTML = `
                    <div style="color: #ef4444; text-align: center; margin-top: 40px;">
                        <h3>Gagal memuat pratinjau dokumen</h3>
                        <p>Format DOCX ini mungkin menggunakan fitur tingkat lanjut yang tidak dapat dirender di browser.</p>
                        <p>Silakan klik tombol <b>Download Asli</b> di pojok kanan atas untuk membukanya menggunakan Microsoft Word.</p>
                    </div>
                `;
                console.log(err);
            }
        });
    </script>
</body>
</html>
