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

    <div class="d-flex">
        <!-- Sidebar -->
        <?php require __DIR__ . '/layout/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5">
            <h2 class="mb-4">Bienvenido al Panel de Control</h2>

            <div class="row g-4">
                <!-- Stat 1 -->
                <div class="col-md-4">
                    <div class="card stat-card bg-primary text-white shadow">
                        <div class="card-body p-4">
                            <h5 class="card-title opacity-75">Tours Totales</h5>
                            <h1 class="display-4 fw-bold mb-0"><?= $stats['total_tours'] ?></h1>
                        </div>
                    </div>
                </div>
                <!-- Stat 2 -->
                <div class="col-md-4">
                    <div class="card stat-card bg-success text-white shadow">
                        <div class="card-body p-4">
                            <h5 class="card-title opacity-75">Tours Activos</h5>
                            <h1 class="display-4 fw-bold mb-0"><?= $stats['active_tours'] ?></h1>
                        </div>
                    </div>
                </div>
                <!-- Stat 3 -->
                <div class="col-md-4">
                    <div class="card stat-card bg-secondary text-white shadow">
                        <div class="card-body p-4">
                            <h5 class="card-title opacity-75">Borradores / Inactivos</h5>
                            <h1 class="display-4 fw-bold mb-0"><?= $stats['inactive_tours'] ?></h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección Analytics -->
            <h3 class="mt-5 mb-4 text-secondary border-bottom pb-2">📊 Estadísticas de Tráfico (Últimos 30 días)</h3>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 bg-white">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Visitas Totales</h6>
                            <h2 class="display-6 fw-bold text-primary mb-0">
                                <?= number_format($trafficStats['total_visits']) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 bg-white">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Visitantes Únicos</h6>
                            <h2 class="display-6 fw-bold text-success mb-0">
                                <?= number_format($trafficStats['unique_visitors']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Top Páginas -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold py-3">
                            📄 Páginas Más Visitadas
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php if (empty($trafficStats['top_pages'])): ?>
                                <li class="list-group-item text-muted text-center py-4">No hay datos aún.</li>
                            <?php else: ?>
                                <?php foreach ($trafficStats['top_pages'] as $page): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-truncate"
                                            style="max-width: 70%;"><?= htmlspecialchars($page['page_url']) ?></span>
                                        <span class="badge bg-light text-dark rounded-pill border"><?= $page['visits'] ?>
                                            hits</span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Top Países -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold py-3">
                            🌍 Visitas por País
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php if (empty($trafficStats['top_countries'])): ?>
                                <li class="list-group-item text-muted text-center py-4">No hay datos aún.</li>
                            <?php else: ?>
                                <?php foreach ($trafficStats['top_countries'] as $country): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <?php if ($country['country_code'] !== 'XX'): ?>
                                                <img src="https://flagcdn.com/16x12/<?= strtolower($country['country_code']) ?>.png"
                                                    alt="<?= $country['country_code'] ?>" class="me-2">
                                            <?php else: ?>
                                                🏳️
                                            <?php endif; ?>
                                            <?= htmlspecialchars($country['country_code']) ?>
                                        </span>
                                        <span class="badge bg-light text-dark rounded-pill border"><?= $country['visits'] ?>
                                            hits</span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h4 class="mb-3">Accesos Rápidos</h4>
                <div class="d-flex gap-3">
                    <a href="/admin/tours/create" class="btn btn-lg btn-outline-primary">creating Nuevo Tour ➕</a>
                    <a href="/admin/pages" class="btn btn-lg btn-outline-dark">Editar Páginas 📝</a>
                    <a href="/" target="_blank" class="btn btn-lg btn-outline-success">Ver Sitio Web 🌎</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>