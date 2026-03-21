<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xuất PNG – {{ $cv->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f1f5f9; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; min-height: 100vh; padding: 24px; font-family: Inter, sans-serif; }
        #status { text-align: center; margin-bottom: 20px; }
        #status h2 { font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
        #status p { font-size: 14px; color: #64748b; }
        #cv-container { width: 794px; background: white; box-shadow: 0 4px 32px rgba(0,0,0,0.1); }
        .spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid #e2e8f0; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite; vertical-align: middle; margin-right: 8px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        #back-btn { margin-top: 20px; display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #6366f1; color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; }
        #back-btn:hover { background: #4f46e5; }
        #download-btn { margin-top: 12px; display: none; align-items: center; gap: 8px; padding: 10px 20px; background: #10b981; color: white; border-radius: 8px; border: none; font-size: 14px; font-weight: 500; cursor: pointer; }
        #download-btn:hover { background: #059669; }
        #preview-img { max-width: 794px; box-shadow: 0 4px 32px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div id="status">
        <h2><span class="spinner"></span> Đang tạo ảnh PNG...</h2>
        <p>Vui lòng chờ trong giây lát</p>
    </div>

    {{-- Hidden CV for capture --}}
    <div id="cv-container">
        @php
            // Load template directly from database to avoid any stale data
            $templateModel = \App\Models\Template::find($cv->template_id);
            $bladeView = $templateModel ? $templateModel->blade_view : null;
            $actualView = $bladeView && \View::exists($bladeView) ? $bladeView : 'cv-templates.classic-blue';
        @endphp
        @include($actualView, ['cv' => $cv, 'preview' => false])
    </div>

    <div id="actions" style="display:none; flex-direction: column; align-items: center; gap: 8px;">
        <img id="preview-img" alt="CV Preview">
        <button id="download-btn" onclick="triggerDownload()">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Tải xuống PNG
        </button>
        <a id="back-btn" href="{{ isset($share) ? route('cv.public', $share->share_token) : route('cv.edit', $cv) }}">
            ← {{ isset($share) ? 'Quay lại xem CV' : 'Quay lại chỉnh sửa' }}
        </a>
    </div>

    <script>
        let pngDataUrl = null;

        window.addEventListener('load', async () => {
            // Wait for fonts to load
            await document.fonts.ready;
            await new Promise(r => setTimeout(r, 600));

            const container = document.getElementById('cv-container');

            try {
                const canvas = await html2canvas(container, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: false,
                    backgroundColor: '#ffffff',
                    logging: false,
                    width: container.offsetWidth,
                    height: container.scrollHeight,
                    windowWidth: container.offsetWidth,
                    windowHeight: container.scrollHeight,
                });

                pngDataUrl = canvas.toDataURL('image/png');

                // Show preview
                const img = document.getElementById('preview-img');
                img.src = pngDataUrl;

                // Hide status and container, show actions
                document.getElementById('status').style.display = 'none';
                container.style.display = 'none';
                const actions = document.getElementById('actions');
                actions.style.display = 'flex';
                document.getElementById('download-btn').style.display = 'inline-flex';

                // Auto-trigger download
                triggerDownload();
            } catch (err) {
                document.getElementById('status').innerHTML = `
                    <h2 style="color:#ef4444;">Xuất thất bại</h2>
                    <p style="color:#64748b;">${err.message}</p>
                    <a href="{{ isset($share) ? route('cv.public', $share->share_token) : route('cv.edit', $cv) }}" style="display:inline-block;margin-top:12px;padding:10px 20px;background:#6366f1;color:white;border-radius:8px;text-decoration:none;">← Quay lại</a>
                `;
            }
        });

        function triggerDownload() {
            if (!pngDataUrl) return;
            const a = document.createElement('a');
            a.download = '{{ Str::slug($cv->title) }}.png';
            a.href = pngDataUrl;
            a.click();
        }
    </script>
</body>
</html>
