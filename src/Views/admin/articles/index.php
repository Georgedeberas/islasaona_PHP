<?php
// src/Views/admin/articles/index.php
$layout = 'admin';
$title = 'Blog & Noticias';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 fw-bold text-gray-800">Gestión de Contenidos</h2>
            <p class="text-muted mb-0">Administra páginas estáticas y noticias.</p>
        </div>
        <a href="/admin/articles/edit" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-plus-lg"></i> Redactar Artículo
        </a>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link bg-white text-secondary border" href="/admin/pages">📄 Páginas Estáticas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active bg-primary text-white" aria-current="page" href="/admin/articles">📰 Blog &
                Noticias</a>
        </li>
    </ul>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Artículo eliminado correctamente.</div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Título</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($articles)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No hay artículos publicados aún.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($articles as $a): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($a['title']) ?>
                                        </div>
                                        <div class="small text-muted">/blog/
                                            <?= $a['slug'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($a['is_published']): ?>
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Publicado</span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Borrador</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('d M Y', strtotime($a['created_at'])) ?>
                                    </td>
                                    <td>
                                        <a href="/admin/articles/edit?id=<?= $a['id'] ?>"
                                            class="btn btn-sm btn-outline-primary me-1">Editar</a>
                                        <a href="/admin/articles/delete?id=<?= $a['id'] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Seguro que deseas borrar este artículo?');">Borrar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div> <!-- End Container -->

<?php require __DIR__ . '/../layout/footer.php'; ?>