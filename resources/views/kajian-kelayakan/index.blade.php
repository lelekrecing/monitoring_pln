<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kajian Kelayakan Proyek | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        *{ margin:0; padding:0; box-sizing:border-box; font-family:'Inter', 'Segoe UI', Arial, sans-serif; }
        body{ background:#f8fafc; color:#0f172a; }
    </style>
</head>
<body>

@includeIf('partials.simerah-topbar', ['topSubTitle' => 'UP3 JAMBI • KAJIAN KELAYAKAN PROYEK', 'withSidebar' => true])

<div class="simerah-shell">
    @includeIf('partials.simerah-sidebar', ['activeNav' => 'kajian'])
    <div class="simerah-main">
        @include('kajian-kelayakan.partials.workspace', ['isAdminPortal' => false])
    </div>
</div>

</body>
</html>
