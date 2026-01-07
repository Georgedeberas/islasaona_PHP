<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento - Mochileros RD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body
    class="bg-gray-900 text-white h-screen flex flex-col items-center justify-center text-center p-6 bg-[url('/assets/images/hero_saona_01.jpg')] bg-cover bg-center bg-no-repeat relative">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    <div class="relative z-10 max-w-lg">
        <div class="inline-block p-4 rounded-full bg-orange-500/20 mb-6 animate-pulse">
            <svg class="w-16 h-16 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                </path>
            </svg>
        </div>

        <h1 class="text-5xl font-black mb-4 tracking-tight">Estamos Mejorando</h1>
        <p class="text-xl text-gray-300 mb-8 leading-relaxed">
            Mochileros RD está recibiendo un poco de cariño técnico para ofrecerte mejores aventuras.
            <br>Volvemos en unos minutos.
        </p>

        <a href="https://wa.me/18290000000"
            class="bg-[#25D366] hover:bg-[#128C7E] text-white font-bold py-3 px-8 rounded-full transition transform hover:scale-105 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163..."></path>
            </svg>
            Contáctanos por WhatsApp
        </a>
    </div>

    <div class="absolute bottom-6 text-sm text-gray-500 z-10">
        &copy;
        <?= date('Y') ?> Mochileros RD.
    </div>

</body>

</html>