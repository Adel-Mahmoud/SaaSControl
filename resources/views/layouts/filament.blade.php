<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="filament-body min-h-screen antialiased bg-gray-100">
    {{ $slot }}

    <div 
        id="page-loader"
        wire:loading.flex
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-white"></div>
    </div>

    <script>
        window.addEventListener("load", function () {
            document.getElementById("page-loader").style.display = "none";
        });
    </script>

    @filamentScripts
</body>
</html>
