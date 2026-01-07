<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tours - Mochileros RD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        .drag-handle {
            cursor: grab;
            color: #adb5bd;
        }

        .drag-handle:active {
            cursor: grabbing;
            color: #6c757d;
        }

        tr.sortable-ghost {
            background-color: #f8f9fa;
            opacity: 0.5;
        }
    </style>
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <?php require __DIR__ . '/../layout/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Gestión de Tours</h2>
                    <p class="text-muted small">Arrastra para reordenar &middot; ⚡ Edición Rápida</p>
                </div>

                <div class="d-flex gap-2">
                    <a href="/admin/tours/trash" class="btn btn-outline-danger position-relative">
                        <i class="fas fa-trash-alt"></i> Papelera
                        <?php if (isset($_GET['trashed'])): ?>
                            <span
                                class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        <?php endif; ?>
                    </a>
                    <a href="/admin/tours/create" class="btn btn-success fw-bold shadow-sm">
                        <i class="fas fa-plus"></i> Nuevo Tour
                    </a>
                </div>
            </div>

            <div class="card shadow border-0 overflow-hidden">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th style="width: 40px;"></th> <!-- Drag Handle -->
                                <th class="ps-3">Tour</th>
                                <th>Precio ADL</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tourTableBody">
                            <?php if (empty($tours)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No hay tours activos.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tours as $tour): ?>
                                    <tr data-id="<?= $tour['id'] ?>" data-price="<?= $tour['price_adult'] ?>"
                                        data-active="<?= $tour['is_active'] ?>">

                                        <!-- Drag Handle -->
                                        <td class="text-center">
                                            <i class="fas fa-grip-vertical drag-handle fa-lg"></i>
                                        </td>

                                        <!-- Info -->
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <?php if (isset($tour['cover_image'])): ?>
                                                    <img src="/<?= $tour['cover_image'] ?>" class="rounded me-3 shadow-sm"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center text-muted small"
                                                        style="width: 50px; height: 50px;">N/A</div>
                                                <?php endif; ?>

                                                <div>
                                                    <div class="fw-bold text-dark tour-title">
                                                        <?= htmlspecialchars($tour['title']) ?></div>
                                                    <div class="small text-muted">
                                                        <?php if (!empty($tour['private_notes'])): ?>
                                                            <span class="text-warning cursor-pointer"
                                                                onclick="editNotes(<?= $tour['id'] ?>, '<?= htmlspecialchars($tour['private_notes'], ENT_QUOTES) ?>')"
                                                                title="Nota Privada: <?= htmlspecialchars($tour['private_notes']) ?>">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-light-gray cursor-pointer hover-show"
                                                                onclick="editNotes(<?= $tour['id'] ?>, '')"
                                                                title="Agregar nota privada">
                                                                <i class="far fa-sticky-note text-black-50"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="fw-bold text-secondary">$<?= number_format($tour['price_adult']) ?></td>

                                        <td>
                                            <?php if ($tour['is_active']): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Publicado</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Borrador</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-end pe-4">
                                            <!-- Quick Edit Trigger -->
                                            <button class="btn btn-sm btn-light text-primary btn-quick-edit me-1"
                                                title="Edición Rápida">
                                                <i class="fas fa-bolt"></i>
                                            </button>

                                            <!-- Edit Full -->
                                            <a href="/admin/tours/edit?id=<?= $tour['id'] ?>"
                                                class="btn btn-sm btn-light text-dark me-1" title="Editar Completo">
                                                <i class="fas fa-pen"></i>
                                            </a>

                                            <!-- Duplicate -->
                                            <a href="/admin/tours/duplicate?id=<?= $tour['id'] ?>"
                                                class="btn btn-sm btn-light text-info me-1" title="Duplicar"
                                                onclick="return confirm('¿Duplicar?')">
                                                <i class="fas fa-copy"></i>
                                            </a>

                                            <!-- Soft Delete -->
                                            <form action="/admin/tours/trash/move/<?= $tour['id'] ?>" method="POST"
                                                class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-light text-danger"
                                                    title="Mover a Papelera">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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

    <!-- Quick Edit Modal -->
    <div class="modal fade" id="quickEditModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">⚡ Edición Rápida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="qe_id">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" id="qe_title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio Adulto (USD)</label>
                        <input type="number" class="form-control" id="qe_price">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="qe_active">
                        <label class="form-check-label" for="qe_active">Publicado en la web</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveQuickEdit()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/assets/js/admin_power_tools.js"></script>
</body>

</html>