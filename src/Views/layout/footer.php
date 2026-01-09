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