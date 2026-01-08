<?php
// layout/footer.php - Global Footer
// Ensure settings are loaded
if (!isset($conf)) {
    $settingModel_f = new \App\Models\Setting();
    $settingsRaw_f = $settingModel_f->getAllFull();
    $conf = [];
    foreach ($settingsRaw_f as $s)
        $conf[$s['setting_key']] = $s['setting_value'];
}
?>
</main>
<!-- Footer -->
<footer class="bg-secondary text-white py-16 border-t border-white/5 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary via-blue-500 to-primary opacity-50">
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            <!-- 1. Brand & Bio -->
            <div class="lg:col-span-1">
                <h3 class="text-2xl font-bold mb-6 text-white font-heading tracking-wide">
                    <?= htmlspecialchars($conf['company_name'] ?? 'Mochileros RD') ?>
                    <span class="text-primary">.</span>
                </h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-6 border-l-2 border-primary pl-4">
                    <?= nl2br(htmlspecialchars($conf['footer_text'] ?? 'Explora los mejores destinos de República Dominicana con expertos locales.')) ?>
                </p>

                <div class="flex items-center gap-3">
                    <?php if (!empty($conf['social_instagram'])): ?>
                        <a href="<?= $conf['social_instagram'] ?>" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-gradient-to-tr hover:from-yellow-400 hover:via-red-500 hover:to-purple-600 hover:border-transparent transition-all duration-300 group">
                            <span class="text-lg group-hover:scale-110 transition-transform">📸</span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($conf['social_facebook'])): ?>
                        <a href="<?= $conf['social_facebook'] ?>" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-blue-600 hover:border-transparent transition-all duration-300 group">
                            <span class="text-lg group-hover:scale-110 transition-transform">f</span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($conf['social_tiktok'])): ?>
                        <a href="<?= $conf['social_tiktok'] ?>" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-black hover:border-transparent transition-all duration-300 group">
                            <span class="text-lg group-hover:scale-110 transition-transform">🎵</span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($conf['social_tripadvisor'])): ?>
                        <a href="<?= $conf['social_tripadvisor'] ?>" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-[#00af87] hover:border-transparent transition-all duration-300 group">
                            <span class="text-lg group-hover:scale-110 transition-transform">🦉</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. Quick Links -->
            <div>
                <h4 class="text-white font-bold mb-6 text-sm uppercase tracking-wider opacity-80">Navegación</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="/"
                            class="hover:text-primary hover:translate-x-1 transition-transform inline-block">Inicio</a>
                    </li>
                    <li><a href="/#tours"
                            class="hover:text-primary hover:translate-x-1 transition-transform inline-block">Tours &
                            Excursiones</a></li>
                    <li><a href="/about"
                            class="hover:text-primary hover:translate-x-1 transition-transform inline-block">Nuestra
                            Historia</a></li>
                    <li><a href="/gallery"
                            class="hover:text-primary hover:translate-x-1 transition-transform inline-block">Galería</a>
                    </li>
                    <li><a href="/contact"
                            class="hover:text-primary hover:translate-x-1 transition-transform inline-block">Contacto</a>
                    </li>
                </ul>
            </div>

            <!-- 3. Contact Info -->
            <div class="md:col-span-2">
                <h4 class="text-white font-bold mb-6 text-sm uppercase tracking-wider opacity-80">Información de
                    Contacto</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 text-sm text-gray-400">

                    <?php if (!empty($conf['contact_phone'])): ?>
                        <div class="flex items-start gap-4 group">
                            <div
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                📞
                            </div>
                            <div>
                                <span class="block text-white font-bold mb-0.5">Reservas</span>
                                <a href="tel:<?= $conf['contact_phone'] ?>"
                                    class="hover:text-primary transition-colors"><?= $conf['contact_phone'] ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($conf['contact_email'])): ?>
                        <div class="flex items-start gap-4 group">
                            <div
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                ✉️
                            </div>
                            <div>
                                <span class="block text-white font-bold mb-0.5">Email</span>
                                <a href="mailto:<?= $conf['contact_email'] ?>"
                                    class="hover:text-primary transition-colors truncate max-w-[150px] inline-block"><?= $conf['contact_email'] ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($conf['contact_address'])): ?>
                        <div class="flex items-start gap-4 group sm:col-span-2">
                            <div
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                📍
                            </div>
                            <div>
                                <span class="block text-white font-bold mb-0.5">Oficina</span>
                                <span><?= $conf['contact_address'] ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div
            class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-4">
            <div class="text-center md:text-left">
                &copy; <?= date('Y') ?> <strong
                    class="text-gray-400"><?= htmlspecialchars($conf['legal_copyright'] ?? 'Mochileros RD') ?></strong>.
                Todos los derechos reservados.
            </div>

            <div class="flex items-center gap-6">
                <?php if (!empty($conf['legal_privacy_link'])): ?>
                    <a href="<?= $conf['legal_privacy_link'] ?>" class="hover:text-white transition-colors">Política de
                        Privacidad</a>
                <?php endif; ?>
                <?php if (!empty($conf['legal_terms_link'])): ?>
                    <a href="<?= $conf['legal_terms_link'] ?>" class="hover:text-white transition-colors">Términos y
                        Condiciones</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>



<!-- Mobile Bottom App Bar -->
<div
    class="fixed bottom-0 left-0 w-full bg-[#FAFAFA] border-t border-gray-300 z-[9999] md:hidden pb-safe shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
    <div class="flex justify-between items-center px-6 h-[50px]">

        <!-- Inicio (Home) -->
        <a href="/"
            class="flex flex-col items-center justify-center text-gray-600 function-link hover:text-black transition-colors w-8"
            aria-label="Inicio">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z" />
            </svg>
        </a>

        <!-- Tours (Palm Tree) -->
        <a href="/#tours"
            class="flex flex-col items-center justify-center text-gray-600 function-link hover:text-black transition-colors w-8"
            aria-label="Tours">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 512 512">
                <path
                    d="M272 96c-78.6 0-145.1 51.5-167.7 122.5c33.6-17 71.5-26.5 111.7-26.5h88c8.8 0 16 7.2 16 16s-7.2 16-16 16H288 216s0 0 0 0c-16.6 0-32.7 1.9-48.3 5.4c-25.9 24.1-41.2 58.2-41.2 96.2c0 12.8 1.9 25.1 5.3 36.8l20.3 70.8 100-249.1c9.3-23.4 11.2-41.9 11.2-56.1l0-6c-3.1 3-6.4 5.9-9.9 8.6C265.4 116.6 272 96 272 96zM112 192c0-8.8 7.2-16 16-16H216c6.2 0 12.2 1.1 18 3.2C194.2 146.5 125.7 121.7 87.8 186.4c7.6 2.3 15.5 4.3 23.3 5.6H128c-8.8 0-16-7.2-16-16zm176 16c0-8.8 7.2-16 16-16h44.1c16.1-9.3 27.6-25 35-43.9C369.4 137.2 338.3 96 272 96c0 0 6.6-20.6-21.4-36.6c3.5-2.7 6.8-5.6 9.9-8.6c8.5-6.6 22.3-13.6 39.5-8.8c12.3 3.4 20.2 15.6 17.5 27.9s-14.7 21.6-27.1 19.1c-3.9-1.1-7.5-1.5-10.7-1.3l.1 2.2c16.3 16.2 46.2 36.2 122.2 26.6c-7.7 41.5-29 78.7-60.8 107.4c5.1 1.7 10 3.3 14.8 4.7l46.1 13.9c12.5 3.8 19.6 17 15.8 29.5s-17 19.6-29.5 15.8l-52.9-16.1c5.9 14.7 9.1 30.7 9.1 47.7c0 29.9-10.4 57.5-27.8 80l-20-75.5c-48.4 113.8-63.1 135.5-63.1 135.5c-1.4 2.2-3.8 3.5-6.4 3.5s-5-1.3-6.4-3.5c0 0-14.7-21.7-63.1-135.5l-20 75.5c-7.9-10-14-21.3-19.1-33.3c-2.4 8.7-2.6 15.3-2.6 15.3c0 2.6 1.3 5 3.5 6.4s5 1.4 7.6 .2l64.3-30.5 64.3 30.5c2.6 1.2 5.5 1.1 7.6-.2s3.5-3.8 3.5-6.4c0 0 .1-6.6-2.6-15.3c-4.9 11.4-11 22.1-18.4 31.7l16 6.3c15.8 6.2 36.3 4.1 48-7.7c17.6-17.7 30.1-34.9 44-53.9c16.1-22 36.9-50.6 61.3-80.1c15.6-18.9 40.5-29.7 65.6-23.7l6.4 1.5c12.6 3 25.4-4.8 28.4-17.4s-4.8-25.4-17.4-28.4l-7.3-1.8c-20-4.8-39.7 4.1-52.3 19.3L442 278.1c-14.2 17.1-27.1 33.3-39.3 49.3c-1.6-1.5-3.2-3-5-4.5l-9.1-7.8C359.8 289 324.2 267.3 288 267.3V208z" />
            </svg>
        </a>

        <!-- Contacto (User Icon) -->
        <a href="/contact"
            class="flex flex-col items-center justify-center text-gray-600 function-link hover:text-black transition-colors w-8"
            aria-label="Contacto">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                <path fill-rule="evenodd"
                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
            </svg>
        </a>

        <!-- WhatsApp (Brand Color, Right Aligned) -->
        <a href="<?= $waLink ?>" target="_blank"
            class="flex items-center justify-center w-8 h-8 bg-[#25D366] text-white rounded-full shadow-md hover:scale-105 transition-transform"
            aria-label="WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
            </svg>
        </a>

    </div>
</div>

</body>

</html>