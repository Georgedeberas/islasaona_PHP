<?php
// src/Views/admin/pages/index.php
$layout = 'admin';
$title = 'Gestión de Contenidos';
require __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Gestión de Contenidos</h2>
            <p class="text-muted mb-0">Administra páginas estáticas y noticias.</p>
        </div>
        <div>
            <!-- Action Button Dynamic -->
            <a href="/admin/pages/create" class="btn btn-success fw-bold shadow-sm">+ Nueva Página</a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link active bg-primary text-white" aria-current="page" href="/admin/pages">📄 Páginas
                Estáticas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-white text-secondary border" href="/admin/articles">📰 Blog & Noticias</a>
        </li>
    </ul>

    <?php if (isset($_GET['saved'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            Cambios guardados correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow border-0 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Orden</th>
                        <th>Página</th>
                        <th>Slug (URL)</th>
                        <th>Última Actualización</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">No hay páginas creadas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pages as $page): ?>
                            <tr>
                                <td class="ps-4 text-muted font-monospace">
                                    #<?= $page['order_index'] ?? 0 ?>
                                </td>
                                <td class="fw-bold text-dark">
                                    <?= htmlspecialchars($page['title']) ?>
                                    <?php if ($page['slug'] === 'home'): ?>
                                        <span class="badge bg-info text-white ms-2">Inicio</span>
                                    <?php endif; ?>
                                </td>
                                <td><a href="/<?= $page['slug'] ?>" target="_blank"
                                        class="text-decoration-none font-monospace small">/<?= $page['slug'] ?> <i
                                            class="fas fa-external-link-alt text-xs"></i></a>
                                </td>
                                <td class="small text-muted"><?= $page['updated_at'] ?? $page['created_at'] ?></td>
                                <td class="text-end pe-4">
                                    <a href="/admin/pages/duplicate?id=<?= $page['id'] ?>"
                                        class="btn btn-sm btn-light text-success border me-1" title="Duplicar">
                                        <i class="fas fa-copy"></i>
                                    </a>
                                    <a href="/admin/pages/edit?id=<?= $page['id'] ?>"
                                        class="btn btn-sm btn-light text-primary border me-1" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <?php if (!in_array($page['slug'], ['home', 'contact', 'about', 'tours', 'gallery'])): ?>
                                        <a href="/admin/pages/delete?id=<?= $page['id'] ?>"
                                            class="btn btn-sm btn-light text-danger border" title="Eliminar"
                                            onclick="return confirm('¿Estás seguro de eliminar esta página permanentemente?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>