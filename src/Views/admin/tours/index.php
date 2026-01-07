<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tours - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <?php require __DIR__ . '/../layout/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gestión de Tours</h2>
                <a href="/admin/tours/create" class="btn btn-success fw-bold">+ Nuevo Tour</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Imagen</th>
                                <th>Título</th>
                                <th>Precio (USD)</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tours)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No hay tours creados aún.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tours as $tour): ?>
                                    <tr class="align-middle">
                                        <td class="ps-4">
                                            <?php if (isset($tour['cover_image'])): ?>
                                                <img src="/<?= $tour['cover_image'] ?>"
                                                    style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                <?= htmlspecialchars($tour['title']) ?>
                                            </div>
                                            <small class="text-muted">/tour/
                                                <?= $tour['slug'] ?>
                                            </small>
                                        </td>
                                        <td>$
                                            <?= number_format($tour['price_adult'], 2) ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill <?= $tour['is_active'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                                                <?= $tour['is_active'] ? 'Activo' : 'Borrador' ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="/admin/tours/duplicate?id=<?= $tour['id'] ?>"
                                                class="btn btn-sm btn-warning me-1" title="Duplicar Oferta"
                                                onclick="return confirm('¿Crear una copia de este tour?');">📋</a>
                                            <a href="/tour/<?= $tour['slug'] ?>" target="_blank"
                                                class="btn btn-sm btn-outline-info me-1" title="Ver en vivo">👁️</a>
                                            <a href="/admin/tours/edit?id=<?= $tour['id'] ?>"
                                                class="btn btn-sm btn-primary">Editar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>