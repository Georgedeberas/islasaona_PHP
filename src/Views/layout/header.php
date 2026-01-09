<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

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
        /* FORCE Hide Google Translate Banner */
        .goog-te-banner-frame.skiptranslate,
        .goog-te-banner-frame {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        #google_translate_element {
            display: none !important;
        }
    </style>

    <script>
        function toggleLangMenu() {
            const menu = document.getElementById('langDropdown');
            menu.classList.toggle('hidden');
        }
        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const btn = document.getElementById('langBtn');
            const menu = document.getElementById('langDropdown');
            if (btn && menu && !btn.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    <script src="/assets/js/live_search.js" defer></script>
</head>

<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

    <!-- ADMIN BAR (Phase 4) -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div
            class="bg-gray-900 text-white text-xs font-mono py-2 px-4 flex justify-between items-center sticky top-0 z-[99999] border-b border-gray-700 shadow-xl">
            <div class="flex items-center gap-3">
                <span class="text-green-400 animate-pulse">●</span>
                <span class="font-bold tracking-wide uppercase">Mochileros Admin</span>
            </div>
            <div class="flex gap-4 items-center">
                <a href="/admin/dashboard" class="flex items-center gap-1 hover:text-white text-gray-400 transition mr-2">
                    Dashboard</a>
                <a href="/admin/tours" class="flex items-center gap-1 hover:text-white text-gray-400 transition mr-4">
                    Tours</a>

                <?php if (isset($tour['id'])): ?>
                    <a href="/admin/tours/edit?id=<?= $tour['id'] ?>"
                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-500 transition font-bold flex items-center gap-2 border border-blue-400">
                        ✏️ Editar Tour
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Top Bar Contact -->
    <div class="bg-secondary text-white text-xs py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <!-- Clean Top Bar -->

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
                <a href="/blog" class="hover:text-primary transition">Blog</a>

                <!-- Páginas Dinámicas -->
                <?php foreach ($menuPages as $mp): ?>
                    <a href="/<?= $mp['slug'] ?>"
                        class="hover:text-primary transition"><?= htmlspecialchars($mp['title']) ?></a>
                <?php endforeach; ?>
            </nav>

            <!-- Search Bar (Desktop) -->
            <div class="relative hidden lg:block w-64 mx-4">
                <input type="text" name="q" placeholder="Buscar paraíso..."
                    class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary focus:outline-none transition text-sm text-gray-700 shadow-sm hover:shadow">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <!-- Resultados se inyectan aquí por JS -->
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <!-- Custom Language Selector (Click Toggle) -->
                <div class="relative z-50">
                    <button id="langBtn" onclick="toggleLangMenu()"
                        class="flex items-center space-x-1 focus:outline-none hover:opacity-80 transition p-1 rounded-full hover:bg-gray-100">
                        <img id="currentFlag" src="https://flagcdn.com/w40/es.png" alt="Idioma"
                            class="w-8 h-8 rounded-full object-cover border border-gray-200 shadow-sm">
                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <!-- Dropdown -->
                    <div id="langDropdown"
                        class="hidden absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden transform origin-top-right transition-all">
                        <div class="py-1">
                            <a href="javascript:void(0)" onclick="setLanguage('es')"
                                class="flex items-center px-4 py-3 hover:bg-orange-50 transition group">
                                <img src="https://flagcdn.com/w40/es.png"
                                    class="w-6 h-6 mr-3 rounded-full border border-gray-100 shadow-sm group-hover:scale-110 transition">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-primary">Español</span>
                            </a>
                            <a href="javascript:void(0)" onclick="setLanguage('en')"
                                class="flex items-center px-4 py-3 hover:bg-blue-50 transition group">
                                <img src="https://flagcdn.com/w40/us.png"
                                    class="w-6 h-6 mr-3 rounded-full border border-gray-100 shadow-sm group-hover:scale-110 transition">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-secondary">English</span>
                            </a>
                        </div>
                    </div>
                </div>

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

        <!-- Hidden Google Translate Element -->
        <div id="google_translate_element" style="display:none;"></div>

        <!-- Google Translate Logic -->
        <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'es',
                    includedLanguages: 'en,es',
                    autoDisplay: false
                }, 'google_translate_element');
            }

            // Custom Language Logic
            function setLanguage(lang) {
                // Set cookie for Google Translate
                // Format: googtrans=/source/dest
                const cookieValue = `/es/${lang}`;
                document.cookie = `googtrans=${cookieValue}; path=/; domain=${window.location.hostname}`;
                document.cookie = `googtrans=${cookieValue}; path=/`; // Fallback

                // Actualizar visualmente la bandera (opcional, pues se recarga)
                updateFlag(lang);

                // Reload to apply
                window.location.reload();
            }

            function updateFlag(lang) {
                const img = document.getElementById('currentFlag');
                if (lang === 'en') {
                    img.src = 'https://flagcdn.com/w40/us.png';
                } else {
                    img.src = 'https://flagcdn.com/w40/es.png';
                }
            }

            // Detect current lang on load to set flag
            document.addEventListener("DOMContentLoaded", function () {
                const cookies = document.cookie.split(';');
                let currentLang = 'es';

                cookies.forEach(c => {
                    if (c.trim().startsWith('googtrans=')) {
                        const val = c.trim().split('=')[1];
                        // val is like /es/en
                        if (val.endsWith('/en')) currentLang = 'en';
                    }
                });

                updateFlag(currentLang);
            });
        </script>
        <script type="text/javascript"
            src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

        <div id="mobile-menu" class="hidden md:hidden border-t">
            <a href="/" class="block px-4 py-2 hover:bg-gray-100">Inicio</a>
            <a href="/#tours" class="block px-4 py-2 hover:bg-gray-100">Tours</a>
            <a href="/blog" class="block px-4 py-2 hover:bg-gray-100">Blog</a>
            <?php foreach ($menuPages as $mp): ?>
                <a href="/<?= $mp['slug'] ?>"
                    class="block px-4 py-2 hover:bg-gray-100"><?= htmlspecialchars($mp['title']) ?></a>
            <?php endforeach; ?>
            <a href="/admin/login" class="block px-4 py-2 text-sm text-gray-500">Admin Login</a>
        </div>
    </header>

    <main class="flex-grow">