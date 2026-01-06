<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Páginas (CMS) - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <?php require __DIR__ . '/../layout/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5">
            <h2 class="mb-4">Gestión de Contenidos</h2>
            <p class="text-muted">Selecciona una página para editar su texto e imágenes.</p>

            <?php if (isset($_GET['saved'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Cambios guardados correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Página</th>
                                <th>Slug (URL)</th>
                                <th>Última Actualización</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pages as $page): ?>
                                <tr class="align-middle">
                                    <td class="ps-4 fw-bold">
                                        <?= htmlspecialchars($page['title']) ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark font-monospace">/
                                            <?= $page['slug'] ?>
                                        </span></td>
                                    <td>
                                        <?= $page['updated_at'] ?? $page['created_at'] ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="/<?= $page['slug'] ?>" target="_blank"
                                            class="btn btn-sm btn-outline-info me-1">Ver</a>
                                        <a href="/admin/pages/edit?id=<?= $page['id'] ?>"
                                            class="btn btn-sm btn-primary">Editar Contenido</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>