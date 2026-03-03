<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { mono: ['JetBrains Mono', 'Fira Code', 'monospace'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; height: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: #1e293b; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
    </style>
    @livewireStyles
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Nav -->
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
            <span class="font-semibold text-white tracking-wide">Supplier Monitor</span>
            <span class="text-xs text-gray-500 bg-gray-800 px-2 py-0.5 rounded">v1.0</span>
        </div>
        <div class="text-xs text-gray-500">
            Node: <span class="text-gray-300">{{ config('payload.node_server_url') }}</span>
        </div>
    </nav>

    <!-- Content -->
    <main class="p-6">
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
