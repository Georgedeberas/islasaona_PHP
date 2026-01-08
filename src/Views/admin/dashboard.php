<?php
// Admin Dashboard - CMS V2
$layout = 'admin'; // trigger admin layout
$title = 'Centro de Mando';

// Mock Stats (In real implementation, these come from Controller)
// Ensure variables are set if not passed
$stats = $stats ?? ['total_tours' => 0, 'active_tours' => 0, 'inactive_tours' => 0];
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0 text-gray-800">👋 ¡Hola, Admin!</h1>
        <p class="text-muted">Aquí tienes un resumen de tu negocio en tiempo real.</p>
    </div>
</div>

<!-- Stats Cards Row -->
<div class="row g-4 mb-5">

    <!-- Tours Activos -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 5px solid #1cc88a !important;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold text-success small mb-1">Tours Activos</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= $stats['active_tours'] ?></div>
                    </div>
                    <div class="text-gray-300">
                        <i class="fs-1 opacity-25">🏝️</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Noticias Publicadas -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 5px solid #4e73df !important;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold text-primary small mb-1">Artículos Blog</div>
                        <div class="h3 mb-0 fw-bold text-dark">--</div> <!-- Placeholder for now -->
                    </div>
                    <div class="text-gray-300">
                        <i class="fs-1 opacity-25">📰</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitas Mes (Estimado) -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 5px solid #36b9cc !important;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold text-info small mb-1">Visitas (Mes)</div>
                        <div class="h3 mb-0 fw-bold text-dark">1.2k</div>
                        <small class="text-success"><i class="text-xs">▲ 12%</i></small>
                    </div>
                    <div class="text-gray-300">
                        <i class="fs-1 opacity-25">👁️</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Clics -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 5px solid #f6c23e !important;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold text-warning small mb-1">Contactos WA</div>
                        <div class="h3 mb-0 fw-bold text-dark">45</div>
                    </div>
                    <div class="text-gray-300">
                        <i class="fs-1 opacity-25">💬</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<h2 class="h5 mb-3 text-gray-800">🚀 Accesos Rápidos</h2>
<div class="row g-3">
    <div class="col-6 col-md-3">
        <a href="/admin/tours/create"
            class="btn btn-white shadow-sm w-100 py-4 border d-flex flex-column align-items-center gap-2 hover-up">
            <span class="fs-2">➕</span>
            <span class="fw-bold text-dark">Nuevo Tour</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="/admin/articles/edit"
            class="btn btn-white shadow-sm w-100 py-4 border d-flex flex-column align-items-center gap-2 hover-up">
            <span class="fs-2">✍️</span>
            <span class="fw-bold text-dark">Escribir Artículo</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="/admin/settings"
            class="btn btn-white shadow-sm w-100 py-4 border d-flex flex-column align-items-center gap-2 hover-up">
            <span class="fs-2">⚙️</span>
            <span class="fw-bold text-dark">Configuración</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="/" target="_blank"
            class="btn btn-white shadow-sm w-100 py-4 border d-flex flex-column align-items-center gap-2 hover-up">
            <span class="fs-2">🌐</span>
            <span class="fw-bold text-dark">Ver Web</span>
        </a>
    </div>
</div>

<style>
    .hover-up {
        transition: transform 0.2s;
    }

    .hover-up:hover {
        transform: translateY(-5px);
    }
</style>