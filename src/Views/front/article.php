<?php
// src/Views/front/article.php
require __DIR__ . '/../layout/header.php';

$img = !empty($article['image_path']) ? '/' . $article['image_path'] : null;
$date = date('d F, Y', strtotime($article['created_at']));
// Translate date manually or use IntlDateFormatter (Simple approach for now)
$months = ['January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo', 'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio', 'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre', 'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'];
$date = strtr($date, $months);
?>

<div class="bg-white min-h-screen pt-20 pb-20"> <!-- Added padding top for fixed header spacing -->

    <!-- Article Header -->
    <header class="container mx-auto px-4 max-w-4xl text-center mb-12">
        <div class="inline-block px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-bold mb-6">
            Blog & Noticias
        </div>
        <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6 font-heading leading-tight">
            <?= htmlspecialchars($article['title']) ?>
        </h1>
        <div class="flex items-center justify-center gap-4 text-gray-500 text-sm">
            <span>📅
                <?= $date ?>
            </span>
            <span>👁️
                <?= $article['views'] ?? 0 ?> Lecturas
            </span>
        </div>
    </header>

    <!-- Featured Image -->
    <?php if ($img): ?>
        <div class="container mx-auto px-4 max-w-5xl mb-12">
            <div class="rounded-2xl overflow-hidden shadow-2xl">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($article['title']) ?>"
                    class="w-full max-h-[600px] object-cover">
            </div>
        </div>
    <?php endif; ?>

    <!-- Content -->
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="prose prose-lg prose-teal mx-auto font-sans leading-relaxed text-gray-700">
            <?= $article['content'] // Rich Text from Editor ?>
        </div>

        <!-- Share -->
        <div class="border-t border-gray-100 mt-16 pt-8 text-center">
            <h4 class="font-bold text-gray-900 mb-4">¿Te gustó? Compártelo</h4>
            <div class="flex justify-center gap-4">
                <a href="https://wa.me/?text=<?= urlencode($article['title'] . ' ' . "https://" . $_SERVER['HTTP_HOST'] . "/blog/" . $article['slug']) ?>" target="_blank" class="bg-green-500 text-white w-10 h-10
                    rounded-full flex items-center justify-center hover:opacity-90">
                    W
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("https://" . $_SERVER['HTTP_HOST'] . "/blog/" . $article['slug']) ?>" target="_blank" class="bg-blue-600 text-white w-10 h-10
                    rounded-full flex items-center justify-center hover:opacity-90">
                    F
                </a>
            </div>

            <div class="mt-12">
                <a href="/blog" class="inline-flex items-center text-primary hover:underline font-bold">
                    ← Volver a Noticias
                </a>
            </div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>