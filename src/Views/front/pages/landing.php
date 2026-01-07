<?php
// Template: Landing Page
// Description: Hero Image, Features, CTA.
$meta = json_decode($page['meta_data'] ?? '[]', true);
?>
<?php require __DIR__ . '/../../layout/header.php'; ?>

<main>
    <!-- Hero Section -->
    <div class="relative h-[60vh] flex items-center justify-center overflow-hidden">
        <?php
        $heroImg = !empty($meta['hero_image']) ? $meta['hero_image'] : '/assets/img/placeholders/gray-landscape.webp';
        ?>
        <div class="absolute inset-0 z-0">
            <img src="<?= $heroImg ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <div class="relative z-10 text-center text-white px-4 max-w-4xl">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 font-heading drop-shadow-md">
                <?= htmlspecialchars($meta['hero_title'] ?? $page['title']) ?>
            </h1>
            <?php if (!empty($meta['hero_subtitle'])): ?>
                <p class="text-xl md:text-2xl opacity-90 mb-8 font-light">
                    <?= htmlspecialchars($meta['hero_subtitle']) ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($meta['cta_text']) && !empty($meta['cta_link'])): ?>
                <a href="<?= htmlspecialchars($meta['cta_link']) ?>"
                    class="inline-block bg-primary hover:bg-primary-dark text-white font-bold py-3 px-8 rounded-full transition transform hover:scale-105 shadow-lg">
                    <?= htmlspecialchars($meta['cta_text']) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Features Section -->
    <?php if (!empty($meta['features'])): ?>
        <div class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($meta['features'] as $f): ?>
                        <div
                            class="bg-white p-8 rounded-2xl shadow-sm text-center transform hover:-translate-y-1 transition duration-300">
                            <div
                                class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                                <?php
                                $icons = ['star' => '⭐', 'check' => '✅', 'heart' => '❤️', 'map' => '🗺️'];
                                echo $icons[$f['icon']] ?? '✨';
                                ?>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">
                                <?= htmlspecialchars($f['title']) ?>
                            </h3>
                            <p class="text-gray-600 leading-relaxed">
                                <?= htmlspecialchars($f['desc']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Extra Content -->
    <?php if (!empty($page['content'])): ?>
        <div class="py-16">
            <div class="container mx-auto px-4 max-w-4xl prose prose-blue">
                <?= $page['content'] ?>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../../layout/footer.php'; ?>