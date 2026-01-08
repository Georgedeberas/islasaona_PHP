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
<div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 z-[9999] md:hidden pb-safe shadow-[0_-5px_15px_rgba(0,0,0,0.03)]">
    <div class="flex justify-between items-center px-8 h-[70px]">
        
        <!-- Inicio (Home) -->
        <a href="/" class="flex flex-col items-center justify-center text-gray-400 function-link hover:text-black transition-colors w-12" aria-label="Inicio">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z"/>
            </svg>
        </a>

        <!-- Tours -->
        <a href="/#tours" class="flex flex-col items-center justify-center text-gray-400 function-link hover:text-black transition-colors w-12" aria-label="Tours">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm.25 10.574l.951 3.55a.5.5 0 0 0 .964-.257l-.951-3.55a.5.5 0 0 0-.964.257zm-3.5-2.285l-1.927 3.21a.5.5 0 1 0 .857.514l1.927-3.21a.5.5 0 1 0-.857-.514zm9.431-3.715a.5.5 0 0 0-.693-.16l-3.21 1.927a.5.5 0 1 0 .514.857l3.21-1.927a.5.5 0 0 0 .179-.697z"/>
                <path d="M8 6.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>
            </svg>
        </a>

        <!-- Contacto -->
        <a href="/contact" class="flex flex-col items-center justify-center text-gray-400 function-link hover:text-black transition-colors w-12" aria-label="Contacto">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.681.681 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
            </svg>
        </a>

        <!-- WhatsApp (Brand Color, Right Aligned) -->
        <a href="<?= $waLink ?>" target="_blank" class="flex items-center justify-center w-12 h-12 bg-[#25D366] text-white rounded-full shadow-md hover:scale-105 transition-transform" aria-label="WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
            </svg>
        </a>
        
    </div>
</div>

</body>

</html>