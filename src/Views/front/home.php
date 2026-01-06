<?php // src/Views/front/home.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mochileros RD - Isla Saona</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9', // Sky 500
                        secondary: '#3b82f6', // Blue 500
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-primary">Mochileros RD</h1>
            <nav class="hidden md:flex space-x-6">
                <a href="#" class="hover:text-primary transition">Inicio</a>
                <a href="#tours" class="hover:text-primary transition">Tours</a>
                <a href="#" class="hover:text-primary transition">Nosotros</a>
                <a href="#" class="hover:text-primary transition">Contacto</a>
            </nav>
            <a href="https://wa.me/18290000000"
                class="bg-primary text-white px-4 py-2 rounded-full hover:bg-sky-600 transition shadow-md">
                Reserva WhatsApp
            </a>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative h-[60vh] flex items-center justify-center bg-cover bg-center"
        style="background-image: url('https://images.unsplash.com/photo-1544414336-7c9b8377e012?q=80&w=1920&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative z-10 text-center text-white px-4">
            <h2 class="text-5xl md:text-6xl font-extrabold mb-4 drop-shadow-lg">Descubre Isla Saona</h2>
            <p class="text-xl md:text-2xl mb-8 font-light">La experiencia caribeña definitiva te espera.</p>
            <a href="#tours"
                class="bg-white text-primary px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition shadow-lg transform hover:scale-105 inline-block">
                Ver Tours Disponibles
            </a>
        </div>
    </section>

    <!-- Tours Section -->
    <section id="tours" class="container mx-auto px-4 py-16">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h3 class="text-3xl font-bold text-gray-900">Nuestros Tours</h3>
                <p class="text-gray-500 mt-2">Elige la aventura perfecta para ti</p>
            </div>

            <!-- View Toggle -->
            <div class="flex space-x-2 bg-gray-200 p-1 rounded-lg">
                <button onclick="toggleView('grid')" id="btn-grid"
                    class="p-2 rounded bg-white shadow text-primary transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                </button>
                <button onclick="toggleView('list')" id="btn-list"
                    class="p-2 rounded hover:bg-white hover:shadow text-gray-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="tours-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-all">
            <?php if (empty($tours)): ?>
                <div class="col-span-full text-center py-10 text-gray-500">
                    <p class="text-xl">No hay tours disponibles en este momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($tours as $tour): ?>
                    <div
                        class="tour-card bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-2xl transition duration-300 border border-gray-100 flex flex-col h-full">
                        <div class="relative overflow-hidden h-64">
                            <img src="/<?= $tour['cover_image'] ?? 'assets/placeholder.jpg' ?>"
                                alt="<?= htmlspecialchars($tour['title']) ?>"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                            <div
                                class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full font-bold text-primary shadow-sm">
                                $
                                <?= number_format($tour['price_adult'], 0) ?> USD
                            </div>
                        </div>
                        <div class="p-6 flex-grow flex flex-col">
                            <h4 class="text-xl font-bold mb-2 text-gray-900">
                                <?= htmlspecialchars($tour['title']) ?>
                            </h4>
                            <div class="flex items-center text-sm text-gray-500 mb-4 space-x-4">
                                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?= $tour['duration'] ?>
                                </span>
                            </div>
                            <p class="text-gray-600 mb-6 flex-grow line-clamp-3">
                                <?= htmlspecialchars($tour['description_short']) ?>
                            </p>
                            <a href="/tour/<?= $tour['slug'] ?>"
                                class="block text-center bg-gray-900 text-white py-3 rounded-xl font-medium hover:bg-gray-800 transition">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-12 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy;
                <?= date('Y') ?> Mochileros RD. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <script>
        const container = document.getElementById('tours-container');
        const btnGrid = document.getElementById('btn-grid');
        const btnList = document.getElementById('btn-list');

        function toggleView(view) {
            if (view === 'list') {
                container.classList.remove('grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3');
                container.classList.add('flex', 'flex-col', 'space-y-6');

                // Adjust cards for list view via JS or specific classes
                const cards = document.querySelectorAll('.tour-card');
                cards.forEach(card => {
                    card.classList.add('md:flex-row', 'md:h-64');
                    card.querySelector('div.relative').classList.remove('h-64');
                    card.querySelector('div.relative').classList.add('md:w-1/3', 'h-64', 'md:h-auto');
                });

                btnList.classList.add('bg-white', 'shadow', 'text-primary');
                btnList.classList.remove('hover:bg-white', 'hover:shadow', 'text-gray-500');
                btnGrid.classList.remove('bg-white', 'shadow', 'text-primary');
                btnGrid.classList.add('hover:bg-white', 'hover:shadow', 'text-gray-500');
            } else {
                container.classList.add('grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3');
                container.classList.remove('flex', 'flex-col', 'space-y-6');

                const cards = document.querySelectorAll('.tour-card');
                cards.forEach(card => {
                    card.classList.remove('md:flex-row', 'md:h-64');
                    card.querySelector('div.relative').classList.add('h-64');
                    card.querySelector('div.relative').classList.remove('md:w-1/3', 'md:h-auto');
                });

                btnGrid.classList.add('bg-white', 'shadow', 'text-primary');
                btnGrid.classList.remove('hover:bg-white', 'hover:shadow', 'text-gray-500');
                btnList.classList.remove('bg-white', 'shadow', 'text-primary');
                btnList.classList.add('hover:bg-white', 'hover:shadow', 'text-gray-500');
            }
        }
    </script>
</body>

</html>