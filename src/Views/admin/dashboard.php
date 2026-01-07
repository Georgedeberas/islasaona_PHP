<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mochileros RD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <div class="d-flex" style="background-color: #f4f6f9; min-height: 100vh;">
        <!-- Sidebar (Desktop) -->
        <div class="d-none d-md-block">
            <?php require __DIR__ . '/layout/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Mobile Header -->
            <div class="d-md-none bg-dark text-white p-3 d-flex justify-content-between align-items-center mb-0">
                <h5 class="m-0 fw-bold">Mochileros Admin</h5>
                <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mobileMenu">
                    ☰
                </button>
            </div>
            <div class="collapse d-md-none bg-white p-3 border-bottom" id="mobileMenu">
                <?php require __DIR__ . '/layout/mobile_menu.php'; ?>
            </div>

            <div class="container-fluid p-3 p-md-5">
                <!-- Welcome Section -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark m-0">Hola, Admin 👋</h2>
                        <p class="text-secondary m-0">Aquí tienes el resumen de hoy.</p>
                    </div>
                </div>

                <!-- 1. BIG ACTION BUTTONS (App Style) -->
                <div class="row g-3 mb-5">
                    <div class="col-6 col-md-3">
                        <a href="/admin/tours/create"
                            class="card h-100 border-0 shadow-sm hover-scale text-decoration-none">
                            <div
                                class="card-body d-flex flex-column align-items-center justify-content-center py-5 text-center">
                                <div class="bg-primary-subtle text-primary rounded-circle p-3 mb-3"
                                    style="width: 60px; height: 60px; display:grid; place-items:center; font-size: 24px;">
                                    ➕
                                </div>
                                <h6 class="fw-bold text-dark m-0">Nuevo Tour</h6>
                                <small class="text-secondary">Crear Oferta</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="/admin/tours" class="card h-100 border-0 shadow-sm hover-scale text-decoration-none">
                            <div
                                class="card-body d-flex flex-column align-items-center justify-content-center py-5 text-center">
                                <div class="bg-success-subtle text-success rounded-circle p-3 mb-3"
                                    style="width: 60px; height: 60px; display:grid; place-items:center; font-size: 24px;">
                                    📦
                                </div>
                                <h6 class="fw-bold text-dark m-0">Catálogo</h6>
                                <small class="text-secondary">Ver Tours</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="/admin/pages" class="card h-100 border-0 shadow-sm hover-scale text-decoration-none">
                            <div
                                class="card-body d-flex flex-column align-items-center justify-content-center py-5 text-center">
                                <div class="bg-info-subtle text-info rounded-circle p-3 mb-3"
                                    style="width: 60px; height: 60px; display:grid; place-items:center; font-size: 24px;">
                                    📝
                                </div>
                                <h6 class="fw-bold text-dark m-0">Páginas</h6>
                                <small class="text-secondary">Editar Contenido</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="/" target="_blank"
                            class="card h-100 border-0 shadow-sm hover-scale text-decoration-none bg-dark text-white">
                            <div
                                class="card-body d-flex flex-column align-items-center justify-content-center py-5 text-center">
                                <div class="bg-white/10 rounded-circle p-3 mb-3"
                                    style="width: 60px; height: 60px; display:grid; place-items:center; font-size: 24px;">
                                    🌎
                                </div>
                                <h6 class="fw-bold text-white m-0">Ver Web</h6>
                                <small class="text-white-50">Vista Cliente</small>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- 2. STATS WIDGETS -->
                <h5 class="fw-bold text-dark mb-3">Estadísticas Rápidas</h5>
                <div class="row g-3 mb-4">
                    <!-- Stats Card 1 -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-uppercase text-secondary small fw-bold mb-1">Visitas
                                        (<?= is_numeric($filter) ? $filter . 'd' : $filter ?>)</h6>
                                    <h2 class="mb-0 fw-bold text-dark">
                                        <?= number_format($trafficStats['total_visits']) ?></h2>
                                </div>
                                <div class="bg-light rounded-circle p-2 text-primary" style="font-size: 2rem;">📊</div>
                            </div>
                        </div>
                    </div>
                    <!-- Stats Card 2 -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-uppercase text-secondary small fw-bold mb-1">Usuarios Únicos</h6>
                                    <h2 class="mb-0 fw-bold text-success">
                                        <?= number_format($trafficStats['unique_visitors']) ?></h2>
                                </div>
                                <div class="bg-light rounded-circle p-2 text-success" style="font-size: 2rem;">👥</div>
                            </div>
                        </div>
                    </div>
                    <!-- Stats Card 3 -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-uppercase text-secondary small fw-bold mb-1">Tours Activos</h6>
                                    <h2 class="mb-0 fw-bold text-warning"><?= $stats['active_tours'] ?></h2>
                                </div>
                                <div class="bg-light rounded-circle p-2 text-warning" style="font-size: 2rem;">🌴</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. DETAILED ANALYTICS (Collapsed by default on mobile?) No, keep simple -->
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div
                                class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold m-0">📄 Top Páginas</h6>
                                <!-- Simple Filter Dropdown -->
                                <form action="/admin/dashboard" method="GET">
                                    <select name="month"
                                        class="form-select form-select-sm border-0 bg-light fw-bold text-primary"
                                        onchange="this.form.submit()">
                                        <option value="1d" <?= ($filter === '1d') ? 'selected' : '' ?>>24h</option>
                                        <option value="7d" <?= ($filter === '7d') ? 'selected' : '' ?>>7 días</option>
                                        <option value="30" <?= ($filter == 30) ? 'selected' : '' ?>>30 días</option>
                                    </select>
                                </form>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php if (empty($trafficStats['top_pages'])): ?>
                                        <div class="p-4 text-center text-muted">Sin datos recientes</div>
                                    <?php else: ?>
                                        <?php foreach (array_slice($trafficStats['top_pages'], 0, 5) as $page): ?>
                                            <div
                                                class="list-group-item border-0 d-flex justify-content-between align-items-center px-4 py-3">
                                                <div class="text-truncate me-3">
                                                    <a href="<?= htmlspecialchars($page['page_url']) ?>" target="_blank"
                                                        class="text-dark text-decoration-none fw-medium">
                                                        <?= htmlspecialchars($page['page_url']) ?>
                                                    </a>
                                                </div>
                                                <span
                                                    class="badge bg-primary-subtle text-primary rounded-pill"><?= $page['visits'] ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="fw-bold m-0">🌍 Top Países</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php if (empty($trafficStats['top_countries'])): ?>
                                        <div class="p-4 text-center text-muted">Sin datos</div>
                                    <?php else: ?>
                                        <?php foreach (array_slice($trafficStats['top_countries'], 0, 5) as $country): ?>
                                            <div
                                                class="list-group-item border-0 d-flex justify-content-between align-items-center px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <?php if ($country['country_code'] !== 'XX' && !empty($country['country_code'])): ?>
                                                        <img src="https://flagcdn.com/20x15/<?= strtolower($country['country_code']) ?>.png"
                                                            class="me-2 rounded shadow-sm">
                                                    <?php else: ?>
                                                        <span class="me-2">🏳️</span>
                                                    <?php endif; ?>
                                                    <small
                                                        class="fw-bold text-secondary"><?= htmlspecialchars($country['country_code']) ?></small>
                                                </div>
                                                <span class="fw-bold"><?= $country['visits'] ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Mobile Menu Partial Creation on the fly if needed, or just standard sidebar links -->

    <style>
        .hover-scale {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-scale:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .bg-primary-subtle {
            background-color: #cfe2ff;
        }

        .text-primary {
            color: #0a58ca;
        }

        .bg-success-subtle {
            background-color: #d1e7dd;
        }

        .text-success {
            color: #146c43;
        }

        .bg-info-subtle {
            background-color: #cff4fc;
        }

        .text-info {
            color: #087990;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>