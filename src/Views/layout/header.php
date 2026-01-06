<?php
// Cargar configuraciones globales
use App\Models\Setting;
$settingModel = new Setting();
$settings = $settingModel->getAll();

// Fallbacks por si la DB está vacía
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
                        // Mochileros RD Brand Colors
                        primary: '#ff8c00', // Naranja
                        secondary: '#002f6c', // Azul Profundo
                        accent: '#ffb74d'
                    }
                }
            }
        }
    </script>
    <style>
        /* Ajustes custom */
        .btn-whatsapp { background-color: #25D366; color: white; }
        .btn-whatsapp:hover { background-color: #128C7E; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">
    
    <!-- Top Bar Contact -->
    <div class="bg-secondary text-white text-xs py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <?php if($phone): ?>
                    <span>📞 <?= htmlspecialchars($phone) ?></span>
                <?php endif; ?>
                <span class="hidden md:inline">📍 <?= htmlspecialchars($settings['address'] ?? 'Republica Dominicana') ?></span>
            </div>
            <div class="flex space-x-3">
                <!-- Redes Sociales Pequeñas -->
                <?php if(!empty($settings['instagram_url'])): ?><a href="<?= $settings['instagram_url'] ?>" target="_blank" class="hover:text-primary">IG</a><?php endif; ?>
                <?php if(!empty($settings['facebook_url'])): ?><a href="<?= $settings['facebook_url'] ?>" target="_blank" class="hover:text-primary">FB</a><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <!-- Logo area -->
            <a href="/" class="flex items-center gap-2 group">
                <!-- Fallback imagen logo si existe, sino Texto -->
                <?php if(file_exists(__DIR__ . '/../../../public/assets/images/logo.png')): ?>
                    <img src="/assets/images/logo.png" alt="Mochileros RD" class="h-12 w-auto object-contain">
                <?php else: ?>
                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white font-bold text-xl shadow">M</div>
                    <span class="text-xl font-bold text-secondary tracking-tight group-hover:text-primary transition"><?= htmlspecialchars($companyName) ?></span>
                <?php endif; ?>
            </a>

            <!-- Nav Desktop -->
            <nav class="hidden md:flex space-x-8 items-center font-medium text-gray-700">
                <a href="/" class="hover:text-primary transition">Inicio</a>
                <a href="/#tours" class="hover:text-primary transition">Tours</a>
                <a href="/about" class="hover:text-primary transition">Nosotros</a>
                <a href="/contact" class="hover:text-primary transition">Contacto</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <?php if($whatsapp): ?>
                <a href="https://wa.me/<?= $whatsapp ?>" class="inline-flex items-center bg-primary text-white px-5 py-2 rounded-full hover:bg-orange-600 transition shadow-md font-bold text-sm transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    WhatsApp
                </a>
                <?php endif; ?>
                <!-- Mobile Menu Button (Simple implementation) -->
                 <button class="md:hidden text-gray-600 focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t">
            <a href="/" class="block px-4 py-2 hover:bg-gray-100">Inicio</a>
            <a href="/#tours" class="block px-4 py-2 hover:bg-gray-100">Tours</a>
            <a href="/about" class="block px-4 py-2 hover:bg-gray-100">Nosotros</a>
            <a href="/contact" class="block px-4 py-2 hover:bg-gray-100">Contacto</a>
            <a href="/admin/login" class="block px-4 py-2 text-sm text-gray-500">Admin Login</a>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="flex-grow">