<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem trước – {{ $template->name }}</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-200 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold text-gray-800">Mẫu: {{ $template->name }}</h1>
            <a href="{{ route('templates.index') }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">← Quay lại chọn mẫu</a>
        </div>
        <div class="bg-white shadow-xl rounded-xl overflow-hidden" style="font-family: '{{ $cv->font_family }}', sans-serif;">
            @include($template->blade_view ?? 'cv-templates.classic-blue', ['cv' => $cv, 'preview' => true])
        </div>
        <div class="mt-6 text-center">
            <form action="{{ route('cv.store') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="template_id" value="{{ $template->id }}">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                    Dùng mẫu này
                </button>
            </form>
        </div>
    </div>
</body>
</html>
