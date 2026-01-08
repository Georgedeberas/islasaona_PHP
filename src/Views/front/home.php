<?php
// src/Views/front/home.php
// Descripción: Página de Inicio Dinámica (Gestionada desde Admin)

// 1. Cargar Configuración Global
$settingModel = new \App\Models\Setting();
$settingsRaw = $settingModel->getAllFull();
$conf = [];
foreach ($settingsRaw as $s)
    $conf[$s['setting_key']] = $s['setting_value'];

// 2. Filtrar Tours Destacados
// HomeController ya pasa $tours (todos los activos)
$featuredIds = json_decode($conf['home_featured_tours'] ?? '[]', true);
$featuredTours = [];

if (!empty($tours) && is_array($tours)) {
    if (!empty($featuredIds)) {
        // Mapear por ID para acceso rápido
        $toursById = [];
        foreach ($tours as $t)
            $toursById[$t['id']] = $t;

        // Mantener el orden de la selección
        foreach ($featuredIds as $fid) {
            if (isset($toursById[$fid])) {
                $featuredTours[] = $toursById[$fid];
            }
        }
    } else {
        // Fallback: Si no hay nada seleccionado, mostrar los primeros 6
        $featuredTours = array_slice($tours, 0, 6);
    }
}
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<main>
    <!-- HERO SECTION -->
    <div class="relative h-[80vh] flex items-center justify-center overflow-hidden bg-gray-900">
        <?php
        $heroImg = !empty($conf['home_hero_bg']) ? '/' . $conf['home_hero_bg'] : '/assets/img/placeholders/gray-landscape.png';
        ?>
        <div class="absolute inset-0 z-0 select-none">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-black/30 z-10"></div>
            <img src="<?= $heroImg ?>" class="w-full h-full object-cover animate-fade-in opacity-90">
        </div>

        <div class="relative z-20 text-center text-white px-4 max-w-5xl mx-auto">
            <div class="mb-6 animate-fade-in-up" style="animation-delay: 0.1s;">
                <span
                    class="inline-block py-1.5 px-5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-sm font-bold tracking-widest uppercase text-shadow-sm">
                    <?= htmlspecialchars($conf['home_hero_subtitle'] ?? 'Vive la Aventura') ?>
                </span>
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold mb-8 font-heading leading-tight drop-shadow-2xl animate-fade-in-up"
                style="animation-delay: 0.2s;">
                <?= htmlspecialchars($conf['home_hero_title'] ?? 'Explora Isla Saona') ?>
            </h1>

            <?php if (!empty($conf['home_hero_cta_text'])): ?>
                <div class="animate-fade-in-up" style="animation-delay: 0.3s;">
                    <a href="<?= htmlspecialchars($conf['home_hero_cta_link'] ?? '#tours') ?>"
                        class="group inline-flex items-center gap-3 bg-primary hover:bg-primary-dark text-white text-lg font-bold py-4 px-10 rounded-full transition-all transform hover:scale-105 shadow-[0_10px_40px_rgba(37,211,102,0.4)]">
                        <span><?= htmlspecialchars($conf['home_hero_cta_text']) ?></span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SECCIÓN: BIENVENIDA -->
    <?php if (($conf['home_show_welcome'] ?? '0') == '1'): ?>
        <section class="py-24 bg-white">
            <div class="container mx-auto px-4 max-w-4xl text-center">
                <h2 class="text-3xl md:text-5xl font-bold mb-8 text-gray-900 font-heading">
                    <?= htmlspecialchars($conf['home_welcome_title'] ?? 'Bienvenidos a Mochileros RD') ?>
                </h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-primary to-blue-500 mx-auto mb-10 rounded-full"></div>
                <p class="text-xl text-gray-600 leading-relaxed font-light">
                    <?= nl2br(htmlspecialchars($conf['home_welcome_text'] ?? '')) ?>
                </p>
            </div>
        </section>
    <?php endif; ?>

    <!-- SECCIÓN: TOURS DESTACADOS -->
    <?php if (!empty($featuredTours)): ?>
        <section class="py-24 bg-gray-50 relative" id="tours">
            <!-- Decorative Dots -->
            <div class="absolute top-10 left-10 opacity-10"><img src="/assets/img/dots.svg" width="100"></div>

            <div class="container mx-auto px-4 relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                    <div class="text-center md:text-left">
                        <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block">Nuestras
                            Recomendaciones</span>
                        <h2 class="text-4xl font-bold text-gray-900">Experiencias Populares</h2>
                    </div>
                    <a href="/#tours"
                        class="hidden md:inline-flex items-center text-gray-600 hover:text-primary font-bold transition">Ver
                        todos los tours <span class="ml-2">→</span></a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <?php foreach ($featuredTours as $tour): ?>
                        <?php
                        // Lógica de Imagen Robusta
                        $coverUrl = "https://placehold.co/600x400/e2e8f0/475569?text=" . urlencode($tour['title']);
                        $rawImg = $tour['main_image'] ?? $tour['cover_image'] ?? '';
                        // Si usamos 'getAll(true)' de HomeController, debería traer 'main_image' si existe columna o lógica
                        // Si no, intentamos buscarla
                
                        if (!empty($rawImg)) {
                            if (filter_var($rawImg, FILTER_VALIDATE_URL)) {
                                $coverUrl = $rawImg;
                            } else {
                                $clean = ltrim($rawImg, '/');
                                if (file_exists(__DIR__ . '/../../../public/' . $clean)) {
                                    $coverUrl = '/' . $clean;
                                } elseif (file_exists(__DIR__ . '/../../../public/assets/uploads/' . $clean)) {
                                    $coverUrl = '/assets/uploads/' . $clean;
                                }
                            }
                        }
                        ?>

                        <div
                            class="group bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col h-full border border-gray-100">
                            <!-- Imagen -->
                            <div class="relative h-72 overflow-hidden">
                                <img src="<?= $coverUrl ?>" alt="<?= htmlspecialchars($tour['title']) ?>"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-60 transition-opacity duration-300">
                                </div>

                                <div
                                    class="absolute top-5 right-5 bg-white/95 backdrop-blur-md px-4 py-1.5 rounded-full text-sm font-extrabold text-gray-900 shadow-sm z-10">
                                    <?= $tour['duration'] ?? '1 Día' ?>
                                </div>
                            </div>

                            <div class="p-8 flex flex-col flex-grow relative">
                                <!-- Category Tag (Mockup logic or category field) -->
                                <div class="text-xs font-bold text-blue-500 uppercase tracking-wide mb-2">Excursión VIP</div>

                                <h3
                                    class="text-2xl font-bold mb-3 text-gray-900 leading-snug group-hover:text-primary transition-colors">
                                    <?= htmlspecialchars($tour['title']) ?>
                                </h3>

                                <p class="text-gray-500 mb-6 text-sm line-clamp-2 leading-relaxed">
                                    <?= htmlspecialchars($tour['description_short'] ?? '') ?>
                                </p>

                                <div class="mt-auto pt-6 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-0.5">Precio por persona</p>
                                        <div class="flex items-baseline gap-1">
                                            <span
                                                class="text-2xl font-extrabold text-primary">$<?= number_format($tour['price_adult'], 0) ?></span>
                                            <span class="text-xs font-bold text-gray-500">USD</span>
                                        </div>
                                    </div>
                                    <a href="/tour/<?= $tour['slug'] ?>"
                                        class="w-12 h-12 rounded-full bg-gray-100 text-gray-900 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all transform group-hover:rotate-45">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- SECCIÓN: POR QUÉ ELEGIRNOS -->
    <?php if (($conf['home_show_why'] ?? '0') == '1'): ?>
        <section class="py-24 bg-white relative overflow-hidden">
            <div class="absolute right-0 top-0 w-1/3 h-full bg-blue-50/50 skew-x-12 translate-x-32 z-0"></div>

            <div class="container mx-auto px-4 relative z-10">
                <div class="flex flex-col lg:flex-row items-center gap-16">
                    <!-- Cards Grid -->
                    <div class="w-full lg:w-1/2 order-2 lg:order-1">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-6 mt-12">
                                <div
                                    class="bg-white p-6 rounded-2xl shadow-xl border border-gray-50 transform hover:-translate-y-2 transition-transform duration-300">
                                    <div
                                        class="w-14 h-14 bg-sky-100 rounded-xl flex items-center justify-center text-2xl mb-4">
                                        🛡️</div>
                                    <h4 class="font-bold text-lg mb-2">Seguridad Total</h4>
                                    <p class="text-sm text-gray-500">Protocolos internacionales.</p>
                                </div>
                                <div
                                    class="bg-white p-6 rounded-2xl shadow-xl border border-gray-50 transform hover:-translate-y-2 transition-transform duration-300">
                                    <div
                                        class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center text-2xl mb-4">
                                        ⭐</div>
                                    <h4 class="font-bold text-lg mb-2">Google 5 Estrellas</h4>
                                    <p class="text-sm text-gray-500">Cientos de reseñas reales.</p>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div
                                    class="bg-white p-6 rounded-2xl shadow-xl border border-gray-50 transform hover:-translate-y-2 transition-transform duration-300">
                                    <div
                                        class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center text-2xl mb-4">
                                        🌿</div>
                                    <h4 class="font-bold text-lg mb-2">Eco-Friendly</h4>
                                    <p class="text-sm text-gray-500">Turismo sostenible.</p>
                                </div>
                                <div
                                    class="bg-white p-6 rounded-2xl shadow-xl border border-gray-50 transform hover:-translate-y-2 transition-transform duration-300">
                                    <div
                                        class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center text-2xl mb-4">
                                        💎</div>
                                    <h4 class="font-bold text-lg mb-2">Servicio Premium</h4>
                                    <p class="text-sm text-gray-500">Atención personalizada.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="w-full lg:w-1/2 order-1 lg:order-2">
                        <span class="text-blue-500 font-bold tracking-wider uppercase text-sm mb-2 block">Por qué
                            nosotros</span>
                        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-gray-900 font-heading leading-tight">
                            <?= htmlspecialchars($conf['home_why_title'] ?? '¿Por qué viajar con Mochileros RD?') ?>
                        </h2>
                        <p class="text-lg text-gray-600 leading-relaxed mb-8">
                            <?= nl2br(htmlspecialchars($conf['home_why_text'] ?? '')) ?>
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="/contact"
                                class="px-8 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-primary transition shadow-lg text-center">Contáctanos</a>
                            <a href="/about"
                                class="px-8 py-3 bg-white text-gray-900 border-2 border-gray-900 font-bold rounded-xl hover:bg-gray-100 transition text-center">Conócenos</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>