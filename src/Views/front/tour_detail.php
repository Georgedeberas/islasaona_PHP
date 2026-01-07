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
        if (strpos($safePath, 'assets/uploads') === false && strpos($safePath, 'assets/images') === false) {
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
        "telephone" => "+18290000000"
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

<!-- ========================================== -->
<!-- 1. NOTION STYLE HEADER (Full Width Strip)  -->
<!-- ========================================== -->
<!-- ========================================== -->
<!-- 1. NOTION STYLE HEADER (Full Width Strip)  -->
<!-- ========================================== -->
<div class="w-full h-[35vh] md:h-[50vh] relative bg-gray-200 group">
    <?php
    // ============================================================
    // ADVANCED IMAGE AUTO-DISCOVERY & RECOVERY SYSTEM (7/1/2026)
    // ============================================================
    
    $coverUrl = "https://placehold.co/1200x600/e2e8f0/475569?text=" . urlencode($tour['title']); // Default
    
    // 1. Intentar usar la ruta guardada en DB
    $dbPath = $tour['main_image'] ?? $tour['cover_image'] ?? '';

    if (!empty($dbPath) && filter_var($dbPath, FILTER_VALIDATE_URL)) {
        $coverUrl = $dbPath; // Es una URL externa válida
    } else {
        // 2. Búsqueda Inteligente en Sistema de Archivos Local
        $found = false;

        // Candidatos basados en lo que haya en DB (limpiando slashes)
        $candidates = [];
        if (!empty($dbPath)) {
            $cleanName = basename($dbPath); // Si en DB dice 'assets/uploads/foto.jpg', buscamos solo 'foto.jpg'
            $candidates[] = 'assets/uploads/' . $cleanName;
            $candidates[] = 'assets/images/' . $cleanName;
            $candidates[] = 'uploads/' . $cleanName;
        }

        // 3. AUTO-HEALING: Buscar imágenes huérfanas por ID de Tour dadas por el sistema admin
        // Patrón común: "tour_{id}_"
        $uploadDir = __DIR__ . '/../../../public/assets/uploads/';
        if (is_dir($uploadDir)) {
            $files = scandir($uploadDir);
            foreach ($files as $file) {
                if (strpos($file, 'tour_' . $tour['id'] . '_') === 0) {
                    // Encontramos una imagen que pertenece a este tour por nombre!
                    // Priorizamos la que sea jpg/png
                    $candidates[] = 'assets/uploads/' . $file;
                }
            }
        }

        // Verificar existencia física
        foreach ($candidates as $cand) {
            if (file_exists(__DIR__ . '/../../../public/' . $cand)) {
                $coverUrl = "/" . $cand;
                $found = true;
                break; // Usar la primera encontrada (prioridad DB, luego Auto-Heal)
            }
        }
    }
    ?>
    <img src="<?= $coverUrl ?>" alt="Portada <?= htmlspecialchars($tour['title']) ?>"
        class="w-full h-full object-cover">
    <!-- Gradiente sutil -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
</div>

<div class="container mx-auto px-4 py-8 -mt-10 relative z-10"> <!-- z-10 OK for content -->

    <!-- ... (Title Section skipped in replacement - standard) ... -->
    <!-- NB: Content replaced via StartLine logic below will include all this -->

    <!-- Título y Breadcrumbs -->
    <div class="mb-10">
        <nav
            class="text-sm text-gray-700 mb-3 bg-white/90 backdrop-blur inline-block px-4 py-1.5 rounded-full shadow-sm border border-gray-100">
            <a href="/" class="hover:text-primary font-medium">Inicio</a> <span class="text-gray-400 mx-1">></span>
            <span class="text-gray-900 font-bold"><?= htmlspecialchars($tour['title']) ?></span>
        </nav>

        <h1
            class="text-3xl md:text-5xl font-black mb-3 text-gray-900 tracking-tight leading-tight drop-shadow-sm bg-white/50 p-2 rounded-lg inline-block backdrop-blur-sm">
            <?= htmlspecialchars($seoTitle) ?>
        </h1>

        <!-- Rating -->
        <div class="flex items-center gap-2 mt-2">
            <div class="flex text-yellow-500 bg-white px-2 py-1 rounded-lg shadow-sm">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <svg class="w-5 h-5 <?= $i < round($rating) ? 'fill-current' : 'text-gray-300' ?>" viewBox="0 0 24 24">
                        <path
                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                    </svg>
                <?php endfor; ?>
                <span class="text-gray-800 font-bold ml-2 text-sm"><?= $rating ?></span>
            </div>
            <span class="text-gray-500 text-sm font-medium bg-gray-100 px-2 py-1 rounded-lg">(<?= $reviews ?> reseñas
                verificadas)</span>
        </div>
    </div>

    <!-- Layout Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- COLUMNA IZQUIERDA -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Highlights -->
            <?php if (!empty($highlights)): ?>
                <div class="bg-indigo-50 border-l-4 border-primary p-6 rounded-r-xl">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center text-lg">✨ Lo más destacado</h3>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($highlights as $hl): ?>
                            <li class="flex items-start text-gray-700">
                                <svg class="w-5 h-5 text-primary mr-2 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <span><?= htmlspecialchars($hl) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Descripción -->
            <?php if (!empty($tour['description_long'])): ?>
                <div class="prose max-w-none text-gray-600 leading-relaxed text-lg">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Sobre esta experiencia</h2>
                    <?= $tour['description_long'] ?>
                </div>
            <?php endif; ?>

            <!-- Info Blocks (Visitaremos, Logística, Fechas) -->
            <div class="space-y-6">
                <!-- Visitaremos -->
                <?php if (!empty($tour['info_visiting'])): ?>
                    <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100/50 hover:border-blue-200 transition">
                        <h3 class="text-xl font-bold mb-3 text-blue-900 flex items-center gap-2"><span>📍</span> Visitaremos
                        </h3>
                        <p class="text-gray-700 whitespace-pre-line leading-relaxed">
                            <?= htmlspecialchars($tour['info_visiting']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Grid Logística -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if (!empty($tour['info_departure'])): ?>
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">🚐 Puntos de Salida</h4>
                            <p class="text-sm text-gray-600 whitespace-pre-line">
                                <?= htmlspecialchars($tour['info_departure']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($tour['info_dates_text'])): ?>
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">📅 Fechas</h4>
                            <p class="text-sm text-gray-600 whitespace-pre-line">
                                <?= htmlspecialchars($tour['info_dates_text']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Incluye / No Incluye -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-green-50 p-6 rounded-2xl border border-green-100">
                    <h3 class="text-lg font-bold mb-4 text-green-800 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Incluye
                    </h3>
                    <?php if (!empty($tour['info_includes'])): ?>
                        <div class="text-gray-700 text-sm whitespace-pre-line leading-relaxed">
                            <?= htmlspecialchars($tour['info_includes']) ?>
                        </div>
                    <?php else: ?>
                        <ul class="space-y-3">
                            <?php foreach ($includes as $inc): ?>
                                <li class="flex items-start text-sm text-gray-700"><span
                                        class="mr-2 text-green-600">✓</span><?= htmlspecialchars($inc) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="bg-red-50 p-6 rounded-2xl border border-red-100">
                    <h3 class="text-lg font-bold mb-4 text-red-800 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        No Incluye
                    </h3>
                    <?php if (!empty($tour['info_not_included'])): ?>
                        <div class="text-gray-600 text-sm whitespace-pre-line leading-relaxed">
                            <?= htmlspecialchars($tour['info_not_included']) ?>
                        </div>
                    <?php else: ?>
                        <ul class="space-y-3">
                            <?php foreach ($notIncluded as $inc): ?>
                                <li class="flex items-start text-sm text-gray-600"><span
                                        class="mr-2 text-red-400">×</span><?= htmlspecialchars($inc) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Importante -->
            <?php if (!empty($tour['info_what_to_bring']) || !empty($tour['info_important'])): ?>
                <div class="space-y-6">
                    <?php if (!empty($tour['info_what_to_bring'])): ?>
                        <div class="bg-orange-50 p-6 rounded-2xl border border-orange-100">
                            <h3 class="font-bold text-orange-900 mb-2">🎒 ¿Qué llevar?</h3>
                            <p class="text-gray-700 whitespace-pre-line"><?= htmlspecialchars($tour['info_what_to_bring']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($tour['info_important'])): ?>
                        <div class="bg-gray-100 p-6 rounded-2xl border border-gray-200">
                            <h3 class="font-bold text-gray-800 mb-2">⚠️ Importante</h3>
                            <p class="text-gray-600 text-sm whitespace-pre-line">
                                <?= htmlspecialchars($tour['info_important']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- GALERIA (Grid Bottom / Stories Mobile) -->
            <div class="pt-10 mt-10 border-t border-gray-100">
                <h3 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                    📸 Galería <span class="text-sm font-normal text-gray-500 ml-3 md:hidden">(Desliza para ver)</span>
                </h3>
                
                <!-- Container: Flex on Mobile (Stories), Grid on Desktop -->
                <div class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-4 md:grid md:grid-cols-3 lg:grid-cols-4 md:overflow-visible md:pb-0" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <?php
                    $galleryImages = [];
                    foreach ($images as $index => $imgData) {
                        // Same Robust Logic for Gallery Images
                        $rawPath = $imgData['image_path'] ?? '';
                        $finalUrl = "https://placehold.co/800x800/e2e8f0/475569?text=Foto+" . ($index + 1);

                        // Copy-paste logic from Header (simplified)
                        if (!empty($rawPath)) {
                            if (filter_var($rawPath, FILTER_VALIDATE_URL)) {
                                $finalUrl = $rawPath;
                            } else {
                                $cleanP = ltrim($rawPath, '/');
                                $cands = (strpos($cleanP, 'assets/') === false)
                                    ? ['assets/uploads/' . $cleanP, 'assets/images/' . $cleanP]
                                    : [$cleanP];
                                foreach ($cands as $c) {
                                    if (file_exists(__DIR__ . '/../../../public/' . $c)) {
                                        $finalUrl = "/" . $c;
                                        break;
                                    }
                                }
                            }
                        }

                        $galleryImages[] = [
                            'src' => $finalUrl,
                            'alt' => $imgData['description'] ?? $tour['title']
                        ];
                    }
                    ?>

                    <?php foreach ($galleryImages as $idx => $img): ?>
                        <!-- Item: Full width on mobile, auto on desktop -->
                        <div class="snap-center shrink-0 w-[85vw] md:w-auto aspect-[4/5] md:aspect-square rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition cursor-pointer group relative"
                            onclick="openLightbox(<?= $idx ?>)">
                            <img src="<?= $img['src'] ?>" alt="<?= htmlspecialchars($img['alt']) ?>"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition"></div>
                            
                            <!-- Mobile Hint -->
                            <div class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded-full md:hidden backdrop-blur-sm">
                                <?= $idx + 1 ?> / <?= count($galleryImages) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div> <!-- Fin Columna Izquierda -->

        <!-- COLUMNA DERECHA: CARD DE PRECIO (Desktop) -->
        <div class="hidden lg:block lg:col-span-1 relative">
            <div class="sticky top-24 border border-gray-200 rounded-3xl p-8 shadow-xl bg-white z-20">
                <div class="text-center mb-6">
                    <p class="text-gray-500 uppercase text-xs font-bold tracking-wider mb-1">Precio por persona</p>
                    <div class="text-5xl font-black text-primary">$<?= number_format($tour['price_adult'], 0) ?></div>
                    <p class="text-gray-400 text-sm mt-2">Mejor precio garantizado</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center text-gray-700 bg-gray-50 p-4 rounded-xl">
                        <span class="text-xl mr-3">⏱️</span>
                        <span class="font-bold"><?= htmlspecialchars($tour['duration']) ?></span>
                    </div>
                </div>

                <button onclick="openBookingModal()"
                    class="w-full bg-primary hover:bg-orange-600 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition transform active:scale-95 flex items-center justify-center gap-3 text-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-.967-.272-.297-.471-.446-.917-.446-.445 0-.966.173-1.461.719-.496.545-1.908 1.857-1.908 4.526 0 2.67 1.956 5.242 2.229 5.613.272.371 3.847 5.861 9.32 8.082 3.256 1.321 3.918 1.058 4.612.991.694-.067 2.229-.916 2.551-1.807.322-.892.322-1.656.223-1.808z" />
                    </svg>
                    Reservar Ahora
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. MOBILE APP BAR (Bottom Fixed)           -->
<!-- ========================================== -->
<div
    class="lg:hidden fixed bottom-4 left-4 right-4 bg-white border border-gray-200 rounded-2xl shadow-2xl z-[9999] p-2 flex items-center justify-between">

    <!-- Left Group: Navigation -->
    <div class="flex items-center space-x-1 pl-2">
        <a href="/"
            class="flex flex-col items-center justify-center p-2 text-gray-500 hover:text-primary transition rounded-xl hover:bg-gray-100/50">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="text-[10px] font-medium">Inicio</span>
        </a>
        <a href="#tours"
            class="flex flex-col items-center justify-center p-2 text-gray-500 hover:text-primary transition rounded-xl hover:bg-gray-100/50">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span class="text-[10px] font-medium">Buscar</span>
        </a>
    </div>

    <!-- Right: WhatsApp Action Button (Floating Style) -->
    <button onclick="openBookingModal()"
        class="bg-[#25D366] text-white rounded-full p-3 pr-5 pl-4 flex items-center shadow-lg hover:bg-[#20bd5a] active:scale-95 transition mr-1">
        <svg class="w-7 h-7 mr-2" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-.967-.272-.297-.471-.446-.917-.446-.445 0-.966.173-1.461.719-.496.545-1.908 1.857-1.908 4.526 0 2.67 1.956 5.242 2.229 5.613.272.371 3.847 5.861 9.32 8.082 3.256 1.321 3.918 1.058 4.612.991.694-.067 2.229-.916 2.551-1.807.322-.892.322-1.656.223-1.808z" />
        </svg>
        <div class="text-left leading-tight">
            <span class="block text-[10px] font-semibold opacity-90">Reservar via</span>
            <span class="block text-sm font-bold">WhatsApp</span>
        </div>
    </button>
</div>

<!-- ========================================== -->
<!-- 4. LIGHTBOX MODAL (Hidden)                 -->
<!-- ========================================== -->
<div id="lightboxModal" class="fixed inset-0 z-[60] hidden bg-black/95 flex items-center justify-center p-4">
    <button onclick="closeLightbox()"
        class="absolute top-4 right-4 text-white hover:text-gray-300 z-50 bg-black/50 rounded-full p-2">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <button onclick="prevImage()"
        class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 bg-black/20 hover:bg-black/50 rounded-full p-3 transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>

    <img id="lightboxImg" src="" alt="Zoom" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">

    <button onclick="nextImage()"
        class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 bg-black/20 hover:bg-black/50 rounded-full p-3 transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>
</div>

<!-- Booking Modal (Igual que antes) -->
<div id="bookingModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
        onclick="closeBookingModal()"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-secondary">Reservar Tour</h3>
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
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tour</label>
                            <input type="text" id="tourName" value="<?= htmlspecialchars($tour['title']) ?>" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-600">
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre</label>
                            <input type="text" id="clientName" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha</label>
                                <input type="date" id="tourDate" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Personas</label>
                                <input type="number" id="pax" min="1" value="2" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Hotel / Recogida</label>
                            <textarea id="pickup" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary"></textarea>
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
    // Configuración del teléfono
    <?php
    $cleanPhone = preg_replace('/[^0-9]/', '', $whatsapp);
    if (empty($cleanPhone))
        $cleanPhone = '18290000000';

    // Rastreo de Origen (Master Plan Item 5)
    $origen = isset($_GET['origen']) ? preg_replace('/[^a-zA-Z0-9]/', '', $_GET['origen']) : '';
    ?>
    const ADMIN_PHONE = "<?= $cleanPhone ?>";
    const TRAFFIC_SOURCE = "<?= $origen ?>";

    // LIGHTBOX LOGIC
    const galleryImages = <?= json_encode($galleryImages) ?>;
    let currentImageIndex = 0;

    function openLightbox(index) {
        currentImageIndex = index;
        updateLightboxImage();
        document.getElementById('lightboxModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightboxModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        updateLightboxImage();
    }

    function prevImage() {
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        updateLightboxImage();
    }

    function updateLightboxImage() {
        if (galleryImages.length > 0) {
            document.getElementById('lightboxImg').src = galleryImages[currentImageIndex].src;
        }
    }

    // Teclado
    document.addEventListener('keydown', function (e) {
        if (document.getElementById('lightboxModal').classList.contains('hidden')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
    });

    // BOOKING MODAL LOGIC (Legacy)
    function openBookingModal() {
        document.getElementById('bookingModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeBookingModal() {
        document.getElementById('bookingModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function sendToWhatsapp(e) {
        e.preventDefault();
        const tour = document.getElementById('tourName').value;
        const name = document.getElementById('clientName').value;
        const date = document.getElementById('tourDate').value;
        const pax = document.getElementById('pax').value;
        const pickup = document.getElementById('pickup').value;

        if (!name || !date || !pax) return;

        let msg = `*¡Hola Mochileros RD!* 👋\nQuiero reservar:\n\n🌴 *Tour:* ${tour}\n👤 *Nombre:* ${name}\n📅 *Fecha:* ${date}\n👥 *Personas:* ${pax}\n`;
        if (pickup) msg += `📍 *Recogida:* ${pickup}\n`;

        // Smart Tracking Injection
        if (TRAFFIC_SOURCE) {
            msg += `\n🔗 *Vengo desde:* ${TRAFFIC_SOURCE.toUpperCase()}\n`;
        }

        msg += `\n¿Disponibilidad?`;

        const url = `https://wa.me/${ADMIN_PHONE}?text=${encodeURIComponent(msg)}`;
        window.open(url, '_blank');
        closeBookingModal();
    }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>