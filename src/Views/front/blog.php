<?php
// src/Views/front/blog.php
require __DIR__ . '/../layout/header.php';
?>

<!-- Blog Header -->
<div class="relative bg-gray-900 py-24">
    <div class="absolute inset-0 overflow-hidden">
        <img src="/assets/img/placeholders/gray-landscape.png" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/50 to-gray-900"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 font-heading">
            Blog & <span class="text-primary">Noticias</span>
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto">
            Descubre consejos, guías y secretos de Isla Saona y República Dominicana.
        </p>
    </div>
</div>

<!-- Blog Grid -->
<div class="bg-white py-16">
    <div class="container mx-auto px-4">

        <?php if (empty($articles)): ?>
            <div class="text-center py-20">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-2xl font-bold text-gray-800">No hay noticias aún</h3>
                <p class="text-gray-500">Estamos preparando contenido increíble para ti. Vuelve pronto.</p>
                <a href="/" class="inline-block mt-8 btn-primary">Volver al Inicio</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($articles as $a):
                    $img = !empty($a['image_path']) ? '/' . $a['image_path'] : '/assets/img/placeholders/gray-square.png';
                    $date = date('d M, Y', strtotime($a['created_at']));
                    ?>
                    <article
                        class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow group flex flex-col h-full">
                        <a href="/blog/<?= $a['slug'] ?>" class="block overflow-hidden h-48 relative">
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($a['title']) ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </a>

                        <div class="p-6 flex flex-col flex-grow">
                            <div class="text-xs text-primary font-bold uppercase tracking-wider mb-2">
                                <?= $date ?>
                            </div>
                            <h2
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary transition-colors line-clamp-2">
                                <a href="/blog/<?= $a['slug'] ?>">
                                    <?= htmlspecialchars($a['title']) ?>
                                </a>
                            </h2>

                            <p class="text-gray-600 mb-4 line-clamp-3 text-sm flex-grow">
                                <?= htmlspecialchars($a['excerpt'] ?? substr(strip_tags($a['content']), 0, 150) . '...') ?>
                            </p>

                            <a href="/blog/<?= $a['slug'] ?>"
                                class="text-primary font-bold text-sm hover:underline inline-flex items-center mt-auto">
                                Leer Artículo <span class="ml-1">→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>