<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .cv-document { width: 100%; }
    </style>
</head>
<body>
    @include($cv->template->blade_view ?? 'cv-templates.classic-blue', ['cv' => $cv, 'preview' => false])
</body>
</html>
