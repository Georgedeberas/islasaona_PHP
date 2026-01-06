<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mochileros RD - Isla Saona</title>
    <meta name="description" content="Las mejores excursiones a Isla Saona y turismo interno en República Dominicana.">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9', // Sky 500
                        secondary: '#3b82f6', // Blue 500
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo area -->
            <a href="/" class="flex items-center gap-2 group">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-primary to-blue-600 rounded-lg flex items-center justify-center text-white font-bold shadow-md group-hover:shadow-lg transition">
                    M
                </div>
                <span class="text-xl font-bold text-gray-800 group-hover:text-primary transition">Mochileros RD</span>
            </a>

            <!-- Nav -->
            <nav class="hidden md:flex space-x-6 items-center">
                <a href="/" class="hover:text-primary transition font-medium">Inicio</a>
                <a href="#tours" class="hover:text-primary transition font-medium">Tours</a>
                <a href="#" class="hover:text-primary transition font-medium">Contacto</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <a href="/admin/login"
                    class="text-xs text-gray-400 hover:text-gray-600 uppercase tracking-wider font-semibold">Admin</a>
                <a href="https://wa.me/18290000000"
                    class="hidden sm:inline-block bg-primary text-white px-5 py-2 rounded-full hover:bg-sky-600 transition shadow-md font-semibold text-sm">
                    Reserva Ya
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="flex-grow">