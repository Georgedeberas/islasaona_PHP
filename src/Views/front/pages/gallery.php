<?php
// Template: Visual Gallery
// Description: Masonry Grid + Lightbox
$meta = json_decode($page['meta_data'] ?? '[]', true);
?>
<?php require __DIR__ . '/../../layout/header.php'; ?>

<main class="py-12 bg-white min-h-screen">
    <div class="container mx-auto px-4">

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-secondary mb-4">
                <?= htmlspecialchars($page['title']) ?>
            </h1>
            <?php if (!empty($meta['gallery_description'])): ?>
                <p class="text-xl text-gray-500 max-w-2xl mx-auto">
                    <?= nl2br(htmlspecialchars($meta['gallery_description'])) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Gallery Grid -->
        <?php if (!empty($meta['images'])): ?>
            <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
                <?php foreach ($meta['images'] as $img): ?>
                    <div class="break-inside-avoid relative group cursor-pointer overflow-hidden rounded-xl shadow-md"
                        onclick="openLightbox('<?= $img ?>')">
                        <img src="<?= $img ?>" class="w-full h-auto transform group-hover:scale-110 transition duration-500">
                        <div
                            class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="text-white bg-black/50 p-2 rounded-full">🔍</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-gray-50 rounded-xl">
                <p class="text-gray-400">No hay imágenes en esta galería todavía.</p>
            </div>
        <?php endif; ?>

        <!-- Content below gallery if any -->
        <?php if (!empty($page['content'])): ?>
            <div class="mt-16 pt-16 border-t border-gray-100 prose max-w-3xl mx-auto text-center">
                <?= $page['content'] ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<!-- Simple Lightbox Script -->
<div id="lightbox" class="fixed inset-0 z-[10000] bg-black/95 hidden flex items-center justify-center p-4"
    onclick="closeLightbox()">
    <img id="lightbox-img" src="" class="max-w-full max-h-[90vh] rounded shadow-2xl">
    <button class="absolute top-5 right-5 text-white text-4xl">&times;</button>
</div>

<script>
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').classList.remove('hidden');
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
    }
</script>

<?php require __DIR__ . '/../../layout/footer.php'; ?>