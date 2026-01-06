<?php
// Preparar datos para JSON-LD
$price = $tour['price_adult'];
$currency = 'USD'; // Asumimos USD, admin podría configurar
$imagesArray = array_map(function ($img) {
    return 'http://islasaona.mochilerosrd.com/' . $img['image_path']; }, $images);
$schema = [
    "@context" => "https://schema.org",
    "@type" => "TouristTrip",
    "name" => $tour['title'],
    "description" => $tour['description_short'],
    "touristType" => ["AdventureTourism", "CulturalTourism"],
    "offers" => [
        "@type" => "Offer",
        "price" => $price,
        "priceCurrency" => $currency,
        "availability" => "https://schema.org/InStock"
    ],
    "image" => $imagesArray
];

// Inclusiones y Exclusiones
$includes = json_decode($tour['includes'], true) ?? [];
$notIncluded = json_decode($tour['not_included'], true) ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($tour['meta_title'] ?: $tour['title']) ?> - Mochileros RD
    </title>
    <meta name="description" content="<?= htmlspecialchars($tour['meta_description'] ?: $tour['description_short']) ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="application/ld+json">
        <?= json_encode($schema) ?>
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9',
                        secondary: '#3b82f6',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Header (Reutilizar si se extrae a partial, por ahora duplicado para consistencia rápida) -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-primary">Mochileros RD</a>
            <a href="https://wa.me/18290000000"
                class="bg-primary text-white px-4 py-2 rounded-full hover:bg-sky-600 transition shadow-md">
                Reserva WhatsApp
            </a>
        </div>
    </header>

    <!-- Galería Hero (Estilo Airbnb: 1 grande, 4 pequeñas) -->
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">
            <?= htmlspecialchars($tour['title']) ?>
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 h-96 rounded-2xl overflow-hidden">
            <?php
            $count = 0;
            foreach ($images as $img):
                $count++;
                $class = ($count == 1) ? 'col-span-2 row-span-2' : 'col-span-1 row-span-1 hidden md:block';
                if ($count > 5)
                    break;
                ?>
                <div class="<?= $class ?> relative h-full">
                    <img src="/<?= $img['image_path'] ?>"
                        class="w-full h-full object-cover hover:opacity-95 transition cursor-pointer">
                </div>
            <?php endforeach; ?>
            <!-- Fallback si pocas imagenes -->
            <?php if ($count == 0): ?>
                <div class="col-span-4 h-full bg-gray-200 flex items-center justify-center text-gray-500">Sin Imagenes</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Columna Izquierda: Detalles -->
        <div class="lg:col-span-2 space-y-8">
            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold mb-4">Lo que vivirás</h2>
                <div class="prose max-w-none text-gray-600">
                    <?= $tour['description_long'] ?>
                </div>
            </div>

            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold mb-4">¿Qué incluye?</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($includes as $inc): ?>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>
                                <?= htmlspecialchars($inc) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold mb-4">No incluye</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($notIncluded as $notin): ?>
                        <div class="flex items-start text-gray-500">
                            <svg class="w-6 h-6 text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>
                                <?= htmlspecialchars($notin) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Sticky Booking Card -->
        <div class="lg:col-span-1">
            <div class="sticky top-28 border rounded-2xl p-6 shadow-xl bg-white">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <span class="text-3xl font-bold text-gray-900">$
                            <?= number_format($tour['price_adult'], 0) ?>
                        </span>
                        <span class="text-gray-500">/ adulto</span>
                    </div>
                    <?php if ($tour['price_child'] > 0): ?>
                        <div class="text-right">
                            <span class="block text-xl font-bold text-gray-800">$
                                <?= number_format($tour['price_child'], 0) ?>
                            </span>
                            <span class="text-xs text-gray-500">/ niño</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-4">
                    <div class="flex border rounded-lg p-3">
                        <div class="flex-1 border-r pr-2">
                            <span class="block text-xs font-bold uppercase text-gray-500">Duración</span>
                            <span>
                                <?= $tour['duration'] ?>
                            </span>
                        </div>
                        <div class="flex-1 pl-2">
                            <span class="block text-xs font-bold uppercase text-gray-500">Tipo</span>
                            <span>Aventura</span>
                        </div>
                    </div>

                    <a href="https://wa.me/18290000000?text=Hola,%20me%20interesa%20reservar%20el%20tour%20<?= urlencode($tour['title']) ?>"
                        class="w-full block text-center bg-primary text-white py-3 rounded-lg font-bold hover:bg-sky-600 transition shadow-lg transform active:scale-95">
                        Reservar Ahora
                    </a>

                    <p class="text-center text-xs text-gray-400 mt-2">No se te cobrará nada todavía.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Footer (Solo visible en movil) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t p-4 z-50 flex justify-between items-center">
        <div>
            <span class="block text-lg font-bold text-gray-900">$
                <?= number_format($tour['price_adult'], 0) ?>
            </span>
            <span class="text-xs text-gray-500">por persona</span>
        </div>
        <a href="https://wa.me/18290000000?text=Hola,%20me%20interesa%20reservar%20el%20tour%20<?= urlencode($tour['title']) ?>"
            class="bg-primary text-white px-6 py-3 rounded-lg font-bold shadow-md">
            Reservar
        </a>
    </div>

</body>

</html>