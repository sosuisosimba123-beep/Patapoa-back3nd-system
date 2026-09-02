<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Patapoa Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Patapoa Admin</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <p class="text-gray-600 mb-8">Access restricted to authorized administrators.</p>

        <a href="https://pocketbase.patapoa.online/_/" target="_blank"
           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
            Verify via PocketBase
        </a>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-400 uppercase tracking-widest">Bot Protection Active</p>
        </div>
    </div>
</body>
</html>
