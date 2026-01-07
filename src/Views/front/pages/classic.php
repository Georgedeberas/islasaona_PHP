<?php
// Template: Classic
// Description: Standard full-width prose content.
?>
<?php require __DIR__ . '/../../layout/header.php'; ?>

<main class="py-12 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold text-secondary mb-4 font-heading">
                <?= htmlspecialchars($page['title']) ?>
            </h1>
            <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>

        <!-- Content -->
        <div class="bg-white p-8 md:p-12 rounded-2xl shadow-sm prose prose-lg max-w-4xl mx-auto text-gray-700">
            <?= $page['content'] ?>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../../layout/footer.php'; ?>