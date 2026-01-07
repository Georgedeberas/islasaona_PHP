<?php
// Layout Admin
require_once __DIR__ . '/../../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 text-danger"><i class="fas fa-trash-alt"></i> Papelera de Reciclaje</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/tours" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<?php if (isset($_GET['restored'])): ?>
    <div class="alert alert-success">♻️ Tour restaurado correctamente.</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-dark">🗑️ Tour eliminado permanentemente. Adios.</div>
<?php endif; ?>

<div class="card shadow border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Tour</th>
                        <th>Eliminado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deletedTours)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="fas fa-smile-beam fa-3x mb-3"></i><br>
                                La papelera está vacía. ¡Bien hecho!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deletedTours as $tour): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-decoration-line-through text-muted">
                                        <?= htmlspecialchars($tour['title']) ?>
                                    </div>
                                    <small class="text-muted">Slug:
                                        <?= $tour['slug'] ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= date('d M Y H:i', strtotime($tour['deleted_at'])) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="/admin/tours/restore/<?= $tour['id'] ?>" method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-success" title="Restaurar">
                                            <i class="fas fa-trash-restore"></i> Restaurar
                                        </button>
                                    </form>

                                    <button class="btn btn-sm btn-danger ms-1" onclick="confirmDestroy(<?= $tour['id'] ?>)"
                                        title="Eliminar para siempre">
                                        <i class="fas fa-ban"></i> Eliminar
                                    </button>
                                    <form id="destroy-form-<?= $tour['id'] ?>" action="/admin/tours/destroy/<?= $tour['id'] ?>"
                                        method="POST" class="d-none"></form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function confirmDestroy(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás deshacer esto. Se borrará para siempre de la base de datos.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, borrar para siempre',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('destroy-form-' + id).submit();
            }
        })
    }
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>