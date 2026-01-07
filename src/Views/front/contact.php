<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Content -->
<div class="container mx-auto px-4 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-white rounded-3xl shadow-xl overflow-hidden">

        <!-- Info Column -->
        <div class="bg-secondary text-white p-12 flex flex-col justify-center">
            <h1 class="text-4xl font-bold mb-6 text-primary">Contáctanos</h1>
            <p class="text-xl mb-12 text-blue-100">Estamos listos para planificar tu próxima aventura a Isla Saona y más
                allá.</p>

            <div class="space-y-8">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-2xl mr-4">📞
                    </div>
                    <div>
                        <p class="text-sm text-blue-200 uppercase font-bold tracking-wider">Teléfono</p>
                        <p class="text-xl font-semibold">
                            <?= $settings['phone_main'] ?? '' ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-2xl mr-4">💬
                    </div>
                    <div>
                        <p class="text-sm text-blue-200 uppercase font-bold tracking-wider">WhatsApp</p>
                        <p class="text-xl font-semibold">
                            <?= $settings['whatsapp_number'] ?? '' ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-2xl mr-4">✉️
                    </div>
                    <div>
                        <p class="text-sm text-blue-200 uppercase font-bold tracking-wider">Email</p>
                        <p class="text-xl font-semibold">
                            <?= $settings['email_contact'] ?? '' ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-2xl mr-4">📍
                    </div>
                    <div>
                        <p class="text-sm text-blue-200 uppercase font-bold tracking-wider">Oficina</p>
                        <p class="text-xl font-semibold">
                            <?= $settings['address'] ?? '' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links Column -->
        <div class="p-12 flex flex-col justify-center items-center text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">¿Listo para reservar?</h3>
            <p class="text-gray-600 mb-8">La forma más rápida es escribirnos directo a WhatsApp.</p>

            <?php
            $cleanWa = preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '');
            ?>
            <a href="https://wa.me/<?= $cleanWa ?>?text=Hola,%20quisiera%20m%C3%A1s%20informaci%C3%B3n"
                class="bg-green-500 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-green-600 transition shadow-lg w-full max-w-sm flex items-center justify-center">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                </svg>
                Chatear
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>