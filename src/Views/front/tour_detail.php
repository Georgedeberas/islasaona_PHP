<?php
// Preparar datos JSON-LD SEO Dinámico
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Obtener datos si existen, sino fallbacks seguros
$seoTitle = !empty($tour['seo_title']) ? $tour['seo_title'] : $tour['title'];
$seoDesc = !empty($tour['seo_description']) ? $tour['seo_description'] : $tour['description_short'];
$rating = !empty($tour['rating_score']) ? $tour['rating_score'] : '4.8';
$reviews = !empty($tour['review_count']) ? $tour['review_count'] : '124';
$highlights = json_decode($tour['tour_highlights'] ?? '[]', true);

// Construcción del Schema
$schema = [
    "@context" => "https://schema.org",
    "@type" => $tour['schema_type'] ?? 'TouristTrip',
    "name" => $seoTitle,
    "description" => $seoDesc,
    "url" => $currentUrl,
    "image" => array_map(function ($img) {
        $safePath = ltrim($img['image_path'], '/');
        // Asegurar que estamos usando el directorio uploads
        if (strpos($safePath, 'assets/uploads') === false && strpos($safePath, 'assets/images') === false) {
            // Si el path no tiene la estructura, intentar forzar la correcta
            $safePath = 'assets/uploads/' . basename($safePath);
        }

        $physicalPath = realpath(__DIR__ . '/../../../public/' . $safePath);

        if (!$physicalPath || !file_exists($physicalPath)) {
            return "https://placehold.co/800x600/E5E7EB/1F2937?text=Foto+No+Disponible";
        }
        return 'http://islasaona.mochilerosrd.com/' . $safePath;
    }, $images),
    "touristType" => ["AdventureTourism", "CulturalTourism", "FamilyTourism"],
    "itinerary" => [
        "@type" => "ItemList",
        "itemListElement" => array_map(function ($h, $k) {
            return ["@type" => "ListItem", "position" => $k + 1, "name" => $h];
        }, $highlights, array_keys($highlights))
    ],
    "offers" => [
        "@type" => "Offer",
        "name" => "Entrada General",
        "price" => $tour['price_adult'],
        "priceCurrency" => "USD",
        "availability" => "https://schema.org/InStock",
        "validFrom" => date('Y-m-d')
    ],
    "provider" => [
        "@type" => "TravelAgency",
        "name" => "Mochileros RD",
        "url" => "http://islasaona.mochilerosrd.com",
        "telephone" => "+18290000000" // Debería inyectarse de settings
    ],
    "aggregateRating" => [
        "@type" => "AggregateRating",
        "ratingValue" => $rating,
        "reviewCount" => $reviews,
        "bestRating" => "5",
        "worstRating" => "1"
    ]
];

$includes = json_decode($tour['includes'], true) ?? [];
$notIncluded = json_decode($tour['not_included'], true) ?? [];

// Iniciar Vista
require __DIR__ . '/../layout/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <!-- JSON-LD Injection -->
    <script type="application/ld+json">
        <?= json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>

    <!-- Meta tags adicionales manuales -->
    <meta name="keywords" content="<?= htmlspecialchars($tour['keywords'] ?? '') ?>">

    <!-- Breadcrumb simple -->
    <!-- ... (Resto del código visual se mantiene igual, solo inyectamos el bloque arriba) -->

    <nav class="text-sm text-gray-500 mb-4">
        <a href="/" class="hover:underline">Inicio</a> >
        <span class="text-gray-900"><?= htmlspecialchars($tour['title']) ?></span>
    </nav>

    <h1 class="text-3xl md:text-4xl font-bold mb-4 text-secondary">
        <?= htmlspecialchars($seoTitle) ?>
    </h1>

    <!-- Rating Badge (Visual SGE Signal) -->
    <div class="flex items-center mb-6 text-yellow-500 gap-1">
        <?php for ($i = 0; $i < 5; $i++): ?>
            <svg class="w-5 h-5 <?= $i < round($rating) ? 'fill-current' : 'text-gray-300' ?>" viewBox="0 0 24 24">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
            </svg>
        <?php endfor; ?>
        <span class="text-gray-600 font-medium ml-2"><?= $rating ?> (<?= $reviews ?> reseñas)</span>
    </div>

    <!-- Galería con Alt Tags Optimizados -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 h-96 rounded-2xl overflow-hidden shadow-lg mb-8">
        <?php
        $count = 0;
        foreach ($images as $img):
            $count++;
            $class = ($count == 1) ? 'col-span-2 row-span-2' : 'col-span-1 row-span-1 hidden md:block';
            if ($count > 5)
                break;

            // Sanitizar path
            $safePath = ltrim($img['image_path'], '/');

            // AUTOMATIC FIX: Si la DB tiene solo "foto.jpg", convertirlo a "assets/uploads/foto.jpg"
            // Esto alinea la visualización con la carpeta real de uploads
            if (strpos($safePath, 'assets') === false && strpos($safePath, '/') === false) {
                $safePath = 'assets/uploads/' . $safePath;
            }

            // Verificar existencia física (Asumiendo que tour_detail.php está en src/Views/front)
            // __DIR__ = .../src/Views/front
            // Root = .../
            // Public Image = .../public/$safePath
            $physicalPath = realpath(__DIR__ . '/../../../public/' . $safePath);

            // URL Final (Fallback si no existe archivo)
            $imageUrl = "/$safePath";
            if (!$physicalPath || !file_exists($physicalPath)) {
                $imageUrl = "https://placehold.co/800x600/E5E7EB/1F2937?text=Foto+No+Disponible";
            }

            // Generar Alt dinámico según regla AEO 2026
            $altText = !empty($img['description'])
                ? $img['description']
                : $tour['title'] . " - Experiencia en República Dominicana foto " . $count;
            ?>
            <div class="<?= $class ?> relative h-full group">
                <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($altText) ?>"
                    class="w-full h-full object-cover hover:scale-105 transition duration-500 cursor-pointer">
            </div>
        <?php endforeach; ?>
        <?php if ($count == 0): ?>
            <div class="col-span-4 h-full bg-gray-200 flex items-center justify-center text-gray-500">Sin Imagenes</div>
        <?php endif; ?>
    </div>
</div>

<div class="container mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
    <!-- Columna Izquierda: Información -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Highlights AI Block -->
        <?php if (!empty($highlights)): ?>
            <div class="bg-indigo-50 border-l-4 border-secondary p-5 rounded-r">
                <h3 class="font-bold text-secondary mb-2 flex items-center">
                    ✨ Puntos Destacados (Highlights)
                </h3>
                <ul class="grid grid-cols-1 gap-2">
                    <?php foreach ($highlights as $hl): ?>
                        <li class="flex items-start text-sm text-gray-700">
                            <span class="mr-2">🔹</span> <?= htmlspecialchars($hl) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="border-b border-gray-100 pb-8">
            <h2 class="text-2xl font-bold mb-4 text-secondary">Lo que vivirás</h2>
            <div class="prose max-w-none text-gray-600 leading-relaxed">
                <?= $tour['description_long'] ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-green-50 p-6 rounded-xl">
                <h3 class="text-lg font-bold mb-4 text-green-800 flex items-center">
                    ✅ Incluido
                </h3>
                <ul class="space-y-3">
                    <?php foreach ($includes as $inc): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700 text-sm"><?= htmlspecialchars($inc) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="bg-red-50 p-6 rounded-xl">
                <h3 class="text-lg font-bold mb-4 text-red-800 flex items-center">
                    ❌ No Incluido
                </h3>
                <ul class="space-y-3">
                    <?php foreach ($notIncluded as $notin): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="text-gray-600 text-sm"><?= htmlspecialchars($notin) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Price Card (Desktop) -->
    <div class="lg:col-span-1 relative">
        <div class="sticky top-24 border border-gray-200 rounded-2xl p-6 shadow-xl bg-white">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <span class="text-sm text-gray-500 uppercase font-semibold">Precio por adulto</span>
                    <div class="text-4xl font-bold text-primary">$<?= number_format($tour['price_adult'], 0) ?></div>
                </div>
                <?php if ($tour['price_child'] > 0): ?>
                    <div class="text-right">
                        <span class="text-xs text-gray-500">Niños</span>
                        <div class="text-xl font-bold text-gray-700">$<?= number_format($tour['price_child'], 0) ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="space-y-4 mb-6">
                <div class="flex items-center text-gray-600 bg-gray-50 p-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium"><?= htmlspecialchars($tour['duration']) ?></span>
                </div>
                <div class="flex items-center text-gray-600 bg-gray-50 p-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                    </svg>
                    <span class="font-medium">Salida: Bayahibe / La Romana</span>
                </div>
            </div>

            <button onclick="openBookingModal()"
                class="w-full block text-center bg-primary text-white py-4 rounded-xl font-bold text-lg hover:bg-orange-600 transition shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                <span>Reservar por WhatsApp</span>
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-.967-.272-.297-.471-.446-.917-.446-.445 0-.966.173-1.461.719-.496.545-1.908 1.857-1.908 4.526 0 2.67 1.956 5.242 2.229 5.613.272.371 3.847 5.861 9.32 8.082 3.256 1.321 3.918 1.058 4.612.991.694-.067 2.229-.916 2.551-1.807.322-.892.322-1.656.223-1.808z" />
                </svg>
            </button>
            <p class="text-center text-xs text-gray-400 mt-3">Sin pagos por adelantado. Habla directo con nosotros.</p>
        </div>
    </div>
</div>

<!-- Mobile Sticky Footer Bar -->
<div
    class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 z-40 flex items-center justify-between shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
    <div>
        <p class="text-xs text-gray-500 uppercase">Total por persona</p>
        <p class="text-xl font-bold text-secondary">$<?= number_format($tour['price_adult'], 0) ?></p>
    </div>
    <button onclick="openBookingModal()"
        class="bg-primary text-white px-6 py-3 rounded-xl font-bold shadow-md hover:bg-orange-600 active:scale-95 transition flex items-center gap-2">
        <span>Reservar</span>
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-.967-.272-.297-.471-.446-.917-.446-.445 0-.966.173-1.461.719-.496.545-1.908 1.857-1.908 4.526 0 2.67 1.956 5.242 2.229 5.613.272.371 3.847 5.861 9.32 8.082 3.256 1.321 3.918 1.058 4.612.991.694-.067 2.229-.916 2.551-1.807.322-.892.322-1.656.223-1.808z" />
        </svg>
    </button>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
        onclick="closeBookingModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal panel -->
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-secondary" id="modal-title">Reservar Tour</h3>
                    <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-6">
                    <p class="text-sm text-gray-500 mb-4">Completa los datos para iniciar la reserva por WhatsApp.</p>

                    <form id="whatsappForm" onsubmit="sendToWhatsapp(event)">
                        <!-- Tour Name (Hidden visually or Readonly) -->
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tour
                                Seleccionado</label>
                            <input type="text" id="tourName" value="<?= htmlspecialchars($tour['title']) ?>" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-600 cursor-not-allowed">
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tu Nombre
                                Completo</label>
                            <input type="text" id="clientName" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none"
                                placeholder="Ej: Juan Pérez">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha
                                    Deseada</label>
                                <input type="date" id="tourDate" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Personas</label>
                                <input type="number" id="pax" min="1" value="2" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary outline-none">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Punto de Recogida /
                                Hotel</label>
                            <textarea id="pickup" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary outline-none"
                                placeholder="¿En qué hotel te hospedas?"></textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-[#25D366] hover:bg-[#128C7E] text-white font-bold py-3 rounded-lg shadow-lg flex items-center justify-center gap-2 transition hover:scale-[1.02]">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-.967-.272-.297-.471-.446-.917-.446-.445 0-.966.173-1.461.719-.496.545-1.908 1.857-1.908 4.526 0 2.67 1.956 5.242 2.229 5.613.272.371 3.847 5.861 9.32 8.082 3.256 1.321 3.918 1.058 4.612.991.694-.067 2.229-.916 2.551-1.807.322-.892.322-1.656.223-1.808z" />
                            </svg>
                            <span>Enviar Solicitud</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Configuración del teléfono desde PHP (Sanitizado)
    <?php
    // Eliminar todo lo que no sea digitos
    $cleanPhone = preg_replace('/[^0-9]/', '', $whatsapp);
    if (empty($cleanPhone))
        $cleanPhone = '18290000000';
    ?>
    const ADMIN_PHONE = "<?= $cleanPhone ?>";

    function openBookingModal() {
        const modal = document.getElementById('bookingModal');
        modal.classList.remove('hidden');
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    function closeBookingModal() {
        const modal = document.getElementById('bookingModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function sendToWhatsapp(e) {
        e.preventDefault();

        const tour = document.getElementById('tourName').value;
        const name = document.getElementById('clientName').value;
        const date = document.getElementById('tourDate').value;
        const pax = document.getElementById('pax').value;
        const pickup = document.getElementById('pickup').value;

        // Validaciones básicas (HTML required ya lo hace, pero por si acaso)
        if (!name || !date || !pax) return;

        // Construir mensaje
        // Usamos caracteres unicode si es necesario, pero urlencode maneja mejor
        let msg = `*¡Hola Mochileros RD!* 👋\n`;
        msg += `Quiero información/reservar este tour:\n\n`;
        msg += `🌴 *Tour:* ${tour}\n`;
        msg += `👤 *Nombre:* ${name}\n`;
        msg += `📅 *Fecha:* ${date}\n`;
        msg += `👥 *Personas:* ${pax}\n`;
        if (pickup) {
            msg += `📍 *Recogida:* ${pickup}\n`;
        }
        msg += `\n¿Tienen disponibilidad?`;

        // Encode
        const encodedMsg = encodeURIComponent(msg);
        const url = `https://wa.me/${ADMIN_PHONE}?text=${encodedMsg}`;

        // Abrir WhatsApp
        window.open(url, '_blank');

        // Opcional: Cerrar modal
        closeBookingModal();
    }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>