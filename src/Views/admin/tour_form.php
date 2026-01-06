<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($tour) ? 'Editar' : 'Nuevo' ?> Tour - Admin
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2>
                <?= isset($tour) ? 'Editar Tour: ' . htmlspecialchars($tour['title']) : 'Crear Nuevo Tour' ?>
            </h2>
            <a href="/admin/dashboard" class="btn btn-secondary">Volver</a>
        </div>

        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <!-- Info Básica -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">Información Principal</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Título del Tour</label>
                            <input type="text" name="title" class="form-control" value="<?= $tour['title'] ?? '' ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción Corta (Listados)</label>
                            <textarea name="description_short" class="form-control"
                                rows="2"><?= $tour['description_short'] ?? '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción Detallada (HTML permitido)</label>
                            <textarea name="description_long" class="form-control"
                                rows="6"><?= $tour['description_long'] ?? '' ?></textarea>
                            <small class="text-muted">Puedes usar etiquetas HTML simples.</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Detalles e Inclusiones</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Qué incluye (Uno por línea)</label>
                            <textarea name="includes" class="form-control"
                                rows="4"><?= isset($tour['includes']) ? implode("\n", json_decode($tour['includes'], true) ?? []) : '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qué NO incluye (Uno por línea)</label>
                            <textarea name="not_included" class="form-control"
                                rows="4"><?= isset($tour['not_included']) ? implode("\n", json_decode($tour['not_included'], true) ?? []) : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Configuración</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Precio Adulto</label>
                            <input type="number" step="0.01" name="price_adult" class="form-control"
                                value="<?= $tour['price_adult'] ?? '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio Niño</label>
                            <input type="number" step="0.01" name="price_child" class="form-control"
                                value="<?= $tour['price_child'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duración</label>
                            <input type="text" name="duration" class="form-control"
                                value="<?= $tour['duration'] ?? '' ?>" placeholder="Ej: 8 horas">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="isActive"
                                <?= (!isset($tour) || $tour['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Tour Activo</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estilo de Visualización</label>
                            <select name="display_style" class="form-select">
                                <option value="grid" <?= (isset($tour) && $tour['display_style'] == 'grid') ? 'selected' : '' ?>>Normal (Grid)</option>
                                <option value="featured" <?= (isset($tour) && $tour['display_style'] == 'featured') ? 'selected' : '' ?>>Destacado (Featured)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Imágenes</div>
                    <div class="card-body">
                        <input type="file" name="images[]" multiple class="form-control mb-2" accept="image/*">
                        <small class="text-muted">Sube nuevas imágenes. La primera será portada si no hay.</small>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">SEO</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control"
                                value="<?= $tour['meta_title'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control"
                                rows="3"><?= $tour['meta_description'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Guardar Tour</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>