<?php
// Template: Landing Page (Home)
// Description: Fully Dynamic Home Page managed via Admin > Settings

// 1. Cargar Configuración Global
$settingModel = new \App\Models\Setting();
$settingsRaw = $settingModel->getAllFull();
$conf = [];
foreach ($settingsRaw as $s)
    $conf[$s['setting_key']] = $s['setting_value'];

// 2. Cargar Tours Destacados
$tourModel = new \App\Models\Tour();
$featuredIds = json_decode($conf['home_featured_tours'] ?? '[]', true);
$featuredTours = [];

if (!empty($featuredIds) && is_array($featuredIds)) {
    // Obtener todos y filtrar (Optimización: En V2 hacer WHERE IN)
    $allTours = $tourModel->getAll(true); // true = include images/metadata if supported, or just basic
    foreach ($allTours as $t) {
        if (in_array($t['id'], $featuredIds)) {
            // Buscamos su imagen de portada manualmente si no viene
            // Por simplicidad usaremos un placeholder si no hay lógica de cover en getAll
            $t['cover_image'] = '/assets/img/placeholder.jpg'; // Fallback
            // Si el modelo soporta cover logic, mejor.
            // Hack rápido: verificar si existe en disco alguna de sus imagenes o usar la de la DB
            $featuredTours[] = $t;
        }
    }
}
?>
<?php require __DIR__ . '/../../layout/header.php'; ?>

<main>
    <!-- HERO SECTION -->
    <div class="relative h-[70vh] flex items-center justify-center overflow-hidden">
        <?php
        $heroImg = !empty($conf['home_hero_bg']) ? '/' . $conf['home_hero_bg'] : '/assets/img/placeholders/gray-landscape.png';
        ?>
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/30 z-10"></div>
            <img src="<?= $heroImg ?>" class="w-full h-full object-cover transform scale-105 animate-slow-zoom">
        </div>

        <div class="relative z-20 text-center text-white px-4 max-w-5xl">
            <span
                class="inline-block py-1 px-3 border border-white/30 rounded-full text-sm backdrop-blur-sm mb-4 bg-white/10">
                <?= htmlspecialchars($conf['home_hero_subtitle'] ?? 'Experiencias Inolvidables') ?>
            </span>
            <h1 class="text-5xl md:text-7xl font-bold mb-8 font-heading leading-tight drop-shadow-lg">
                <?= htmlspecialchars($conf['home_hero_title'] ?? 'Bienvenido al Paraíso') ?>
            </h1>

            <?php if (!empty($conf['home_hero_cta_text'])): ?>
                <a href="<?= htmlspecialchars($conf['home_hero_cta_link'] ?? '#tours') ?>"
                    class="group inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-bold py-4 px-10 rounded-full transition-all transform hover:scale-105 shadow-[0_10px_30px_rgba(37,211,102,0.4)]">
                    <span><?= htmlspecialchars($conf['home_hero_cta_text']) ?></span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                        </path>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- SECCIÓN: BIENVENIDA -->
    <?php if (($conf['home_show_welcome'] ?? '0') == '1'): ?>
        <div class="py-20 bg-white">
            <div class="container mx-auto px-4 max-w-4xl text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-900">
                    <?= htmlspecialchars($conf['home_welcome_title'] ?? '') ?>
                </h2>
                <div class="w-24 h-1 bg-primary mx-auto mb-8 rounded-full"></div>
                <p class="text-lg md:text-xl text-gray-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($conf['home_welcome_text'] ?? '')) ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- SECCIÓN: TOURS DESTACADOS -->
    <?php if (!empty($featuredTours)): ?>
        <div class="py-20 bg-gray-50" id="tours">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Experiencias Destacadas</h2>
                    <p class="text-gray-500 text-lg">Los favoritos de nuestros viajeros</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                    <?php foreach ($featuredTours as $tour): ?>
                        <div
                            class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
                            <!-- Imagen (Simulación de Cover real) -->
                            <?php
                            // Intentar buscar imagen real si existe
                            $bg = '/assets/img/placeholders/gray-landscape.png';
                            // Logica rapida para encontrar imagen
                            $db = \App\Config\Database::getConnection();
                            $stmt = $db->prepare("SELECT image_path FROM tour_images WHERE tour_id = ? AND is_cover = 1 LIMIT 1");
                            $stmt->execute([$tour['id']]);
                            $img = $stmt->fetch();
                            if ($img)
                                $bg = '/' . ltrim($img['image_path'], '/');
                            ?>
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="<?= $bg ?>"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div
                                    class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-gray-800 shadow-sm">
                                    <?= $tour['duration'] ?? '1 Día' ?>
                                </div>
                            </div>

                            <div class="p-8 flex flex-col flex-grow">
                                <h3 class="text-2xl font-bold mb-3 text-gray-900 group-hover:text-primary transition-colors">
                                    <?= htmlspecialchars($tour['title']) ?>
                                </h3>
                                <div class="mb-6 flex items-baseline gap-1">
                                    <span class="text-sm text-gray-500">desde</span>
                                    <span class="text-2xl font-bold text-primary">$<?= $tour['price_adult'] ?></span>
                                    <span class="text-sm text-gray-500">USD</span>
                                </div>
                                <div class="mt-auto">
                                    <a href="/tours/<?= $tour['slug'] ?>"
                                        class="block w-full text-center border-2 border-gray-900 text-gray-900 font-bold py-3 rounded-xl hover:bg-gray-900 hover:text-white transition-colors duration-300">
                                        Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- SECCIÓN: POR QUÉ ELEGIRNOS -->
    <?php if (($conf['home_show_why'] ?? '0') == '1'): ?>
        <div class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center gap-12 max-w-6xl mx-auto">
                    <div class="w-full md:w-1/2">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-6 rounded-2xl rounded-tr-[4rem]">
                                <div class="text-4xl mb-4">🛡️</div>
                                <h4 class="font-bold text-gray-900">Seguridad</h4>
                            </div>
                            <div class="bg-green-50 p-6 rounded-2xl rounded-tl-[4rem] mt-8">
                                <div class="text-4xl mb-4">🌿</div>
                                <h4 class="font-bold text-gray-900">Ecología</h4>
                            </div>
                            <div class="bg-yellow-50 p-6 rounded-2xl rounded-bl-[4rem]">
                                <div class="text-4xl mb-4">🏆</div>
                                <h4 class="font-bold text-gray-900">Calidad</h4>
                            </div>
                            <div class="bg-purple-50 p-6 rounded-2xl rounded-br-[4rem] mt-8">
                                <div class="text-4xl mb-4">💖</div>
                                <h4 class="font-bold text-gray-900">Pasión</h4>
                            </div>
                        </div>
                    </div>
                    <div class="w-full md:w-1/2">
                        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-900">
                            <?= htmlspecialchars($conf['home_why_title'] ?? '¿Por qué nosotros?') ?>
                        </h2>
                        <p class="text-lg text-gray-600 leading-relaxed mb-8">
                            <?= nl2br(htmlspecialchars($conf['home_why_text'] ?? '')) ?>
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-gray-700">
                                <span class="text-green-500 bg-green-100 p-1 rounded-full text-xs">✔</span>
                                Sin intermediarios (Mejor precio garantizado)
                            </li>
                            <li class="flex items-center gap-3 text-gray-700">
                                <span class="text-green-500 bg-green-100 p-1 rounded-full text-xs">✔</span>
                                Atención personalizada 24/7
                            </li>
                            <li class="flex items-center gap-3 text-gray-700">
                                <span class="text-green-500 bg-green-100 p-1 rounded-full text-xs">✔</span>
                                Guías locales certificados
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../../layout/footer.php'; ?>