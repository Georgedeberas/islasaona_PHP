<?php
// Cargar configuraciones globales
use App\Models\Setting;
use App\Models\Page; // NEW: Para menú dinámico

$settingModel = new Setting();
$settings = $settingModel->getAll();

// Cargar páginas para el menú
$pageModel = new Page();
$menuPages = $pageModel->getAll();

// Analytics Tracking (Local & Privado)
use App\Services\Analytics;
Analytics::track();

// Fallbacks
$companyName = $settings['company_name'] ?? 'Mochileros RD';
$phone = $settings['phone_main'] ?? '';
$whatsapp = $settings['whatsapp_number'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $companyName ?> - Agencia de Viajes</title>
    <meta name="description" content="Explora con <?= $companyName ?>">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ff8c00',
                        secondary: '#002f6c',
                        accent: '#ffb74d'
                    }
                }
            }
        }
    </script>
    <style>
        /* Clean Google Translate */
        .goog-te-banner-frame {
            display: none !important;
        }

        .goog-te-gadget-simple {
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
            font-size: 14px !important;
        }

        .goog-te-gadget-simple .goog-te-menu-value {
            color: #4b5563 !important;
        }

        .goog-te-gadget-simple .goog-te-menu-value span {
            border-left: none !important;
            color: #4b5563 !important;
        }

        .goog-te-gadget-icon {
            display: none !important;
        }

        body {
            top: 0 !important;
        }

        #google_translate_element {
            display: inline-block;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

    <!-- Top Bar Contact -->
    <div class="bg-secondary text-white text-xs py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <!-- Lang Selector -->
                <div id="google_translate_element"></div>

                <?php if ($phone): ?>
                    <span class="hidden sm:inline">📞 <?= htmlspecialchars($phone) ?></span>
                <?php endif; ?>
                <span class="hidden md:inline">📍
                    <?= htmlspecialchars($settings['address'] ?? 'Republica Dominicana') ?></span>
            </div>
            <div class="flex space-x-3">
                <?php if (!empty($settings['instagram_url'])): ?><a href="<?= $settings['instagram_url'] ?>"
                        target="_blank" class="hover:text-primary">IG</a><?php endif; ?>
                <?php if (!empty($settings['facebook_url'])): ?><a href="<?= $settings['facebook_url'] ?>"
                        target="_blank" class="hover:text-primary">FB</a><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <!-- Logo area -->
            <a href="/" class="flex items-center gap-2 group">
                <?php if (file_exists(__DIR__ . '/../../../public/assets/images/logo.png')): ?>
                    <img src="/assets/images/logo.png" alt="Mochileros RD" class="h-12 w-auto object-contain">
                <?php else: ?>
                    <div
                        class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white font-bold text-xl shadow">
                        M</div>
                    <span
                        class="text-xl font-bold text-secondary tracking-tight group-hover:text-primary transition"><?= htmlspecialchars($companyName) ?></span>
                <?php endif; ?>
            </a>

            <!-- Nav Desktop (DYNAMIC MENU) -->
            <nav class="hidden md:flex space-x-8 items-center font-medium text-gray-700">
                <a href="/" class="hover:text-primary transition">Inicio</a>
                <a href="/#tours" class="hover:text-primary transition">Tours</a>

                <!-- Páginas Dinámicas -->
                <?php foreach ($menuPages as $mp): ?>
                    <a href="/<?= $mp['slug'] ?>"
                        class="hover:text-primary transition"><?= htmlspecialchars($mp['title']) ?></a>
                <?php endforeach; ?>
            </nav>

            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <?php if ($whatsapp): ?>
                    <a href="https://wa.me/<?= $whatsapp ?>"
                        class="inline-flex items-center bg-primary text-white px-5 py-2 rounded-full hover:bg-orange-600 transition shadow-md font-bold text-sm transform hover:scale-105">
                        WhatsApp
                    </a>
                <?php endif; ?>
                <button class="md:hidden text-gray-600 focus:outline-none"
                    onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu (DYNAMIC MENU) -->
        <script type="text/javascript">
            function googleTranslateElementInit() {
                console.log('Google Translate Init Started...');
                new google.translate.TranslateElement({
                    pageLanguage: 'es',
                    includedLanguages: 'en,fr,de,it,ru,pt',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                    autoDisplay: false
                }, 'google_translate_element');
            }
        </script>
        <script type="text/javascript"
            src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

        <div id="mobile-menu" class="hidden md:hidden border-t">
            <a href="/" class="block px-4 py-2 hover:bg-gray-100">Inicio</a>
            <a href="/#tours" class="block px-4 py-2 hover:bg-gray-100">Tours</a>
            <?php foreach ($menuPages as $mp): ?>
                <a href="/<?= $mp['slug'] ?>"
                    class="block px-4 py-2 hover:bg-gray-100"><?= htmlspecialchars($mp['title']) ?></a>
            <?php endforeach; ?>
            <a href="/admin/login" class="block px-4 py-2 text-sm text-gray-500">Admin Login</a>
        </div>
    </header>

    <main class="flex-grow">