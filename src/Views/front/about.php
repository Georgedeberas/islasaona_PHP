<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="bg-gray-100 py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-secondary mb-2">
            <?= htmlspecialchars($page['title']) ?>
        </h1>
        <div class="h-1 w-20 bg-primary mx-auto rounded-full"></div>
    </div>
</div>

<!-- Content -->
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 prose prose-lg max-w-none text-gray-600">
        <!-- Contenido dinámico desde DB -->
        <?= $page['content'] ?>

        <!-- Hardcoded visual grid for "About" specifically if wanted, otherwise just generic content -->
        <hr class="my-8 border-gray-100">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center not-prose">
            <div class="p-4 bg-orange-50 rounded-xl">
                <span class="text-4xl mb-2 block">🇩🇴</span>
                <h3 class="font-bold text-secondary">100% Local</h3>
                <p class="text-sm">Expertos dominicanos conociendo cada rincón.</p>
            </div>
            <div class="p-4 bg-orange-50 rounded-xl">
                <span class="text-4xl mb-2 block">🤝</span>
                <h3 class="font-bold text-secondary">Atención Personal</h3>
                <p class="text-sm">Te tratamos como familia, no como cliente.</p>
            </div>
            <div class="p-4 bg-orange-50 rounded-xl">
                <span class="text-4xl mb-2 block">🛡️</span>
                <h3 class="font-bold text-secondary">Seguridad</h3>
                <p class="text-sm">Transporte y guías certificados.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>