<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Hero Section -->
<section class="relative h-[60vh] flex items-center justify-center bg-gray-900 overflow-hidden">
    <!-- Imagen de fondo optimizada -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1590523741831-ab7f29192bc5?q=80&w=1920&auto=format&fit=crop"
            alt="Isla Saona Playa" class="w-full h-full object-cover opacity-60">
    </div>

    <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
        <span
            class="inline-block py-1 px-3 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-sm font-semibold tracking-wide mb-4 animate-fade-in-up">
            DESCUBRE EL PARAÍSO
        </span>
        <h2 class="text-4xl md:text-6xl font-extrabold mb-6 drop-shadow-2xl leading-tight">
            Vivia la Experiencia <br><span
                class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-emerald-400">Isla Saona</span>
        </h2>
        <p class="text-lg md:text-xl mb-8 font-light text-gray-200 max-w-2xl mx-auto">
            La aventura que estabas esperando. Playas vírgenes, fiesta en catamarán y recuerdos inolvidables con
            Mochileros RD.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#tours"
                class="bg-primary text-white px-8 py-3.5 rounded-full font-bold hover:bg-sky-600 transition shadow-lg transform hover:scale-105">
                Ver Tours
            </a>
            <a href="https://wa.me/18290000000"
                class="bg-white/10 backdrop-blur-sm border border-white/30 text-white px-8 py-3.5 rounded-full font-bold hover:bg-white/20 transition shadow-lg">
                Hablar con Asesor
            </a>
        </div>
    </div>
</section>

<!-- Tours Section -->
<section id="tours" class="container mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h3 class="text-3xl font-bold text-gray-900 mb-3">Nuestras Próximas Salidas</h3>
        <div class="h-1 w-20 bg-primary mx-auto rounded-full"></div>
    </div>

    <?php if (empty($tours)): ?>
        <!-- EMPTY STATE -->
        <div class="max-w-md mx-auto text-center py-16 px-4 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="bg-sky-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
            </div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">¡Aventuras en camino!</h4>
            <p class="text-gray-500 mb-6">Estamos actualizando nuestro catálogo de excursiones para brindarte lo mejor.
                Vuelve pronto.</p>
            <a href="https://wa.me/18290000000"
                class="inline-flex items-center text-primary font-semibold hover:text-sky-700 transition">
                Preguntar por disponibilidad
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                    </path>
                </svg>
            </a>
        </div>
    <?php else: ?>
        <!-- GRID DE TOURS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($tours as $tour): ?>
                <div
                    class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-2xl transition duration-300 border border-gray-100 flex flex-col h-full">
                    <!-- Imagen -->
                    <div class="relative overflow-hidden h-64">
                        <img src="/<?= !empty($tour['cover_image']) ? $tour['cover_image'] : 'assets/placeholder.jpg' ?>"
                            alt="<?= htmlspecialchars($tour['title']) ?>"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                        <div
                            class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-bold text-primary shadow-sm">
                            $<?= number_format($tour['price_adult'], 0) ?> USD
                        </div>
                    </div>

                    <!-- Contenido -->
                    <div class="p-6 flex-grow flex flex-col">
                        <div class="mb-4">
                            <h4 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition">
                                <?= htmlspecialchars($tour['title']) ?></h4>
                            <div class="flex items-center gap-4 text-xs font-medium text-gray-500 uppercase tracking-wide">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?= $tour['duration'] ?>
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    Familiar / Aventura
                                </span>
                            </div>
                        </div>

                        <p class="text-gray-600 mb-6 flex-grow text-sm line-clamp-3 leading-relaxed">
                            <?= htmlspecialchars($tour['description_short']) ?>
                        </p>

                        <a href="/tour/<?= $tour['slug'] ?>"
                            class="w-full block text-center bg-gray-900 text-white py-3 rounded-xl font-semibold hover:bg-primary transition shadow-md">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Features Section (Relleno visual extra) -->
<section class="bg-sky-50 py-12">
    <div class="container mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div class="p-4">
            <div class="text-4xl mb-2">🌴</div>
            <h5 class="font-bold text-gray-800">Destinos Exóticos</h5>
        </div>
        <div class="p-4">
            <div class="text-4xl mb-2">🚤</div>
            <h5 class="font-bold text-gray-800">Transporte Seguro</h5>
        </div>
        <div class="p-4">
            <div class="text-4xl mb-2">🍹</div>
            <h5 class="font-bold text-gray-800">Todo Incluido</h5>
        </div>
        <div class="p-4">
            <div class="text-4xl mb-2">📸</div>
            <h5 class="font-bold text-gray-800">Fotos Increíbles</h5>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>