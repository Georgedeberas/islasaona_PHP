<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tour) ? 'Editar' : 'Nuevo' ?> Tour - SEO Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-top: 3px solid #ff8c00;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2><?= isset($tour) ? 'Editando: ' . htmlspecialchars($tour['title']) : 'Crear Nuevo Tour' ?></h2>
            <a href="/admin/tours" class="btn btn-secondary">← Volver al listado</a>
        </div>

        <form method="POST" enctype="multipart/form-data">

            <ul class="nav nav-tabs mb-4" id="tourTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                        type="button" role="tab">📋 Información General</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button"
                        role="tab">🤖 SEO & Inteligencia Artificial</button>
                </li>
            </ul>

            <div class="tab-content" id="tourTabContent">

                <!-- TAB 1: GENERAL -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Título del producto</label>
                                        <input type="text" name="title" class="form-control form-control-lg"
                                            value="<?= $tour['title'] ?? '' ?>" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Precio Adulto (USD)</label>
                                            <input type="number" step="0.01" name="price_adult" class="form-control"
                                                value="<?= $tour['price_adult'] ?? '' ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Precio Niño (USD)</label>
                                            <input type="number" step="0.01" name="price_child" class="form-control"
                                                value="<?= $tour['price_child'] ?? '' ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción Corta</label>
                                        <textarea name="description_short" class="form-control"
                                            rows="2"><?= $tour['description_short'] ?? '' ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción Completa (HTML)</label>
                                        <textarea name="description_long" class="form-control"
                                            rows="8"><?= $tour['description_long'] ?? '' ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-success text-white">✅ Incluye (1 por línea)</div>
                                        <div class="card-body p-0">
                                            <textarea name="includes" class="form-control border-0"
                                                rows="6"><?= isset($tour['includes']) ? implode("\n", json_decode($tour['includes'], true) ?? []) : '' ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-danger text-white">❌ No Incluye (1 por línea)</div>
                                        <div class="card-body p-0">
                                            <textarea name="not_included" class="form-control border-0"
                                                rows="6"><?= isset($tour['not_included']) ? implode("\n", json_decode($tour['not_included'], true) ?? []) : '' ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header">Configuración</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Duración</label>
                                        <input type="text" name="duration" class="form-control"
                                            value="<?= $tour['duration'] ?? '' ?>" placeholder="Ej: 8 horas">
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                            <?= (!isset($tour) || $tour['is_active']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="isActive">Publicado / Activo</label>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-3">
                                <div class="card-header">Imágenes</div>
                                <div class="card-body text-center">
                                    <input type="file" name="images[]" multiple class="form-control mb-2"
                                        accept="image/*">
                                    <small class="text-muted d-block">Sube varias fotos a la vez.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: SEO & AI -->
                <div class="tab-pane fade" id="seo" role="tabpanel">
                    <div class="alert alert-info border-start border-5 border-info">
                        <strong>💡 AEO (Answer Engine Optimization):</strong> Estos datos ayudarán a que Google SGE,
                        ChatGPT y Gemini entiendan y recomienden tu tour.
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">SEO Title (Google)</label>
                                        <input type="text" name="seo_title" class="form-control"
                                            value="<?= $tour['seo_title'] ?? ($tour['title'] ?? '') ?>"
                                            placeholder="Ej: Tour Isla Saona VIP Todo Incluido | Mochileros RD">
                                        <div class="form-text">Título optimizado para clics. Máx 60 caracteres
                                            recomendado.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Meta Description</label>
                                        <textarea name="seo_description" class="form-control" rows="3"
                                            maxlength="160"><?= $tour['seo_description'] ?? ($tour['description_short'] ?? '') ?></textarea>
                                        <div class="form-text">Resumen persuasivo que aparece en Google. Máx 160
                                            caracteres.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Keywords / Etiquetas IA</label>
                                        <textarea name="keywords" class="form-control"
                                            rows="2"><?= $tour['keywords'] ?? '' ?></textarea>
                                        <div class="form-text">Tags separados por comas. Ej: <em>Saona, Catamarán,
                                                Fiesta, Langosta, Bayahibe</em>.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">Highlights (Lo más destacado para la IA)
                                </div>
                                <div class="card-body p-0">
                                    <textarea name="tour_highlights" class="form-control border-0" rows="5"
                                        placeholder="• Almuerzo buffet privado en playa exclusiva&#10;• Barra libre nacional incluida&#10;• Parada en la Piscina Natural con estrellas de mar"><?= isset($tour['tour_highlights']) ? implode("\n", json_decode($tour['tour_highlights'], true) ?? []) : '' ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header">Schema.org Data</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Evento</label>
                                        <select name="schema_type" class="form-select">
                                            <option value="TouristTrip" <?= ($tour['schema_type'] ?? '') == 'TouristTrip' ? 'selected' : '' ?>>TouristTrip (General)</option>
                                            <option value="Event" <?= ($tour['schema_type'] ?? '') == 'Event' ? 'selected' : '' ?>>Evento (Fecha Fija)</option>
                                        </select>
                                    </div>
                                    <hr>
                                    <label class="form-label fw-bold mb-2">Social Proof (Estrellas)</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="small">Rating (0-5)</label>
                                            <input type="number" step="0.1" max="5" name="rating_score"
                                                class="form-control" value="<?= $tour['rating_score'] ?? 4.8 ?>">
                                        </div>
                                        <div class="col-6">
                                            <label class="small"># Reseñas</label>
                                            <input type="number" name="review_count" class="form-control"
                                                value="<?= $tour['review_count'] ?? 150 ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="fixed-bottom bg-white border-top p-3 shadow-lg" style="z-index: 1000;">
                <div class="container d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-lg px-5 fw-bold">💾 Guardar Cambios</button>
                </div>
            </div>

        </form>
        <div style="height: 80px;"></div> <!-- Spacer for fixed bottom -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>