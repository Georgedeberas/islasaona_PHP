</main>
<!-- Footer -->
<footer class="bg-secondary text-white py-12 border-t border-blue-900">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-8">
            <!-- Branding -->
            <div>
                <h3 class="text-2xl font-bold mb-4 text-primary">
                    <?= htmlspecialchars($settings['company_name'] ?? 'Mochileros RD') ?></h3>
                <p class="text-gray-300 text-sm leading-relaxed mb-4">
                    <?= nl2br(htmlspecialchars($settings['footer_text'] ?? '')) ?>
                </p>
                <div class="flex space-x-4">
                    <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?= $settings['instagram_url'] ?>" target="_blank"
                            class="bg-white/10 p-2 rounded-full hover:bg-primary transition">📸</a>
                    <?php endif; ?>
                    <?php if (!empty($settings['facebook_url'])): ?>
                        <a href="<?= $settings['facebook_url'] ?>" target="_blank"
                            class="bg-white/10 p-2 rounded-full hover:bg-primary transition">👍</a>
                    <?php endif; ?>
                    <?php if (!empty($settings['tiktok_url'])): ?>
                        <a href="<?= $settings['tiktok_url'] ?>" target="_blank"
                            class="bg-white/10 p-2 rounded-full hover:bg-primary transition">🎵</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Links -->
            <div>
                <h3 class="text-lg font-bold mb-4 text-white border-b border-primary/30 inline-block pb-1">Explora</h3>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><a href="/" class="hover:text-primary transition flex items-center"><span
                                class="text-primary mr-2">›</span>Inicio</a></li>
                    <li><a href="/#tours" class="hover:text-primary transition flex items-center"><span
                                class="text-primary mr-2">›</span>Nuestras Excursiones</a></li>
                    <li><a href="/about" class="hover:text-primary transition flex items-center"><span
                                class="text-primary mr-2">›</span>Quiénes Somos</a></li>
                    <li><a href="/contact" class="hover:text-primary transition flex items-center"><span
                                class="text-primary mr-2">›</span>Contacto</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="text-lg font-bold mb-4 text-white border-b border-primary/30 inline-block pb-1">Contáctanos
                </h3>
                <ul class="space-y-4 text-sm text-gray-300">
                    <?php if (!empty($settings['phone_main'])): ?>
                        <li class="flex items-start">
                            <span class="text-2xl mr-3">📞</span>
                            <div>
                                <span class="block font-bold text-white">Llámanos</span>
                                <a href="tel:<?= $settings['phone_main'] ?>"
                                    class="hover:text-primary"><?= $settings['phone_main'] ?></a>
                            </div>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($settings['whatsapp_number'])): ?>
                        <li class="flex items-start">
                            <span class="text-2xl mr-3">💬</span>
                            <div>
                                <span class="block font-bold text-white">WhatsApp</span>
                                <a href="https://wa.me/<?= $settings['whatsapp_number'] ?>" class="hover:text-primary">Chat
                                    Directo</a>
                            </div>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($settings['email_contact'])): ?>
                        <li class="flex items-start">
                            <span class="text-2xl mr-3">✉️</span>
                            <div>
                                <span class="block font-bold text-white">Email</span>
                                <a href="mailto:<?= $settings['email_contact'] ?>"
                                    class="hover:text-primary"><?= $settings['email_contact'] ?></a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10 pt-8 text-center text-sm text-gray-400">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['company_name'] ?? 'Mochileros RD') ?>. Todos los
                derechos reservados.</p>
        </div>
    </div>
</footer>
</body>

</html>