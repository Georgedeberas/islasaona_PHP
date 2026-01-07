<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Página - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"
        referrerpolicy="no-referrer"></script>
    <script>
        tinymce.init({
            selector: '#editorContent',
            height: 600,
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        });
    </script>
</head>

<body>

    <div class="d-flex">
        <?php require __DIR__ . '/../layout/sidebar.php'; ?>

        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Editando: <span class="text-primary"><?= htmlspecialchars($page['title']) ?></span> <span
                        class="badge bg-info text-dark fs-6 ms-2">v2.1</span></h2>
                <a href="/admin/pages" class="btn btn-outline-secondary">Cancelar</a>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="template" id="selectedTemplate"
                    value="<?= $page['template'] ?? 'classic' ?>">

                <div class="row g-4 mb-4">
                    <!-- Configuración Base -->
                    <div class="col-md-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white fw-bold">Configuración General</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Título de la Página</label>
                                    <input type="text" name="title" class="form-control" required
                                        value="<?= htmlspecialchars($page['title']) ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold">Slug / URL</label>
                                        <input type="text" name="slug" class="form-control"
                                            value="<?= htmlspecialchars($page['slug']) ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Orden</label>
                                        <input type="number" name="order_index" class="form-control"
                                            value="<?= $page['order_index'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selector de Plantilla -->
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 bg-light">
                            <div class="card-header fw-bold">Plantilla Actual</div>
                            <div class="card-body p-2 d-flex flex-column gap-2">
                                <?php $tpl = $page['template'] ?? 'classic'; ?>
                                <div class="template-option p-3 border rounded bg-white cursor-pointer <?= $tpl == 'classic' ? 'active-template' : '' ?>"
                                    onclick="selectTemplate('classic', this)">
                                    <h6 class="mb-1">📝 Clásica</h6>
                                    <small class="text-muted">Texto enriquecido estándar.</small>
                                </div>
                                <div class="template-option p-3 border rounded bg-white cursor-pointer <?= $tpl == 'landing' ? 'active-template' : '' ?>"
                                    onclick="selectTemplate('landing', this)">
                                    <h6 class="mb-1">🚀 Landing Page</h6>
                                    <small class="text-muted">Hero, Features y CTA.</small>
                                </div>
                                <div class="template-option p-3 border rounded bg-white cursor-pointer <?= $tpl == 'gallery' ? 'active-template' : '' ?>"
                                    onclick="selectTemplate('gallery', this)">
                                    <h6 class="mb-1">🖼️ Galería Visual</h6>
                                    <small class="text-muted">Grid de fotos y Lightbox.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Áreas Dinámicas -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">Contenido de la Página</div>
                    <div class="card-body p-4">

                        <?php $meta = $page['meta_data'] ?? []; ?>

                        <!-- SECCIÓN: CLÁSICA -->
                        <div id="section-classic" class="template-section <?= $tpl == 'classic' ? '' : 'd-none' ?>">
                            <label class="form-label fw-bold">Editor de Texto</label>
                            <textarea id="editorContent"
                                name="content"><?= htmlspecialchars($page['content']) ?></textarea>
                        </div>

                        <!-- SECCIÓN: LANDING PAGE -->
                        <div id="section-landing" class="template-section <?= $tpl == 'landing' ? '' : 'd-none' ?>">
                            <h5 class="text-primary border-bottom pb-2 mb-3">Cabecera (Hero Section)</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Título Principal</label>
                                    <input type="text" name="hero_title" class="form-control"
                                        value="<?= htmlspecialchars($meta['hero_title'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subtítulo</label>
                                    <input type="text" name="hero_subtitle" class="form-control"
                                        value="<?= htmlspecialchars($meta['hero_subtitle'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Imagen de Fondo Actual</label>
                                <?php if (!empty($meta['hero_image'])): ?>
                                    <div class="mb-2"><img src="<?= $meta['hero_image'] ?>"
                                            style="height: 100px; border-radius: 8px;"></div>
                                <?php endif; ?>
                                <input type="file" name="hero_image" class="form-control" accept="image/*">
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Texto CTA</label>
                                    <input type="text" name="cta_text" class="form-control"
                                        value="<?= htmlspecialchars($meta['cta_text'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Enlace CTA</label>
                                    <input type="text" name="cta_link" class="form-control"
                                        value="<?= htmlspecialchars($meta['cta_link'] ?? '') ?>">
                                </div>
                            </div>

                            <h5 class="text-primary border-bottom pb-2 mb-3">Bloques de Características</h5>
                            <?php
                            $features = $meta['features'] ?? [];
                            for ($i = 0; $i < 3; $i++):
                                $f = $features[$i] ?? ['title' => '', 'desc' => '', 'icon' => 'check'];
                                ?>
                                <div class="row g-2 mb-2 feature-row">
                                    <div class="col-md-4">
                                        <input type="text" name="feature_title[]" class="form-control form-control-sm"
                                            placeholder="Título" value="<?= htmlspecialchars($f['title']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="feature_desc[]" class="form-control form-control-sm"
                                            placeholder="Descripción" value="<?= htmlspecialchars($f['desc']) ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="feature_icon[]" class="form-select form-select-sm">
                                            <option value="star" <?= $f['icon'] == 'star' ? 'selected' : '' ?>>⭐</option>
                                            <option value="check" <?= $f['icon'] == 'check' ? 'selected' : '' ?>>✅</option>
                                            <option value="heart" <?= $f['icon'] == 'heart' ? 'selected' : '' ?>>❤️</option>
                                            <option value="map" <?= $f['icon'] == 'map' ? 'selected' : '' ?>>🗺️</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endfor; ?>

                            <div class="mt-4 border-top pt-3">
                                <label class="form-label fw-bold">Contenido Extra (Opcional)</label>
                                <div class="alert alert-info py-2"><small>El contenido del Editor de Texto (Pestaña
                                        Clásica) aparecerá debajo de los bloques.</small></div>
                            </div>
                        </div>

                        <!-- SECCIÓN: GALERÍA -->
                        <div id="section-gallery" class="template-section <?= $tpl == 'gallery' ? '' : 'd-none' ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción del Álbum</label>
                                <textarea name="gallery_description" class="form-control"
                                    rows="3"><?= htmlspecialchars($meta['gallery_description'] ?? '') ?></textarea>
                            </div>

                            <?php if (!empty($meta['images'])): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Imágenes Actuales</label>
                                    <div class="d-flex gap-2 flex-wrap bg-light p-3 rounded">
                                        <?php foreach ($meta['images'] as $img): ?>
                                            <div class="position-relative">
                                                <img src="<?= $img ?>"
                                                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;"
                                                    class="border">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Añadir Más Fotos</label>
                                <input type="file" name="gallery_photos[]" class="form-control" multiple
                                    accept="image/*">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light p-3 text-end sticky-bottom">
                        <button type="submit" class="btn btn-primary btn-lg px-5">Guardar Cambios</button>
                    </div>
                </div>
            </form>

            <style>
                .cursor-pointer {
                    cursor: pointer;
                    transition: all 0.2s;
                }

                .template-option:hover {
                    border-color: #0d6efd !important;
                    background-color: #f8f9fa !important;
                }

                .active-template {
                    border: 2px solid #0d6efd !important;
                    background-color: #e7f1ff !important;
                }
            </style>

            <script>
                function selectTemplate(templateName, element) {
                    document.getElementById('selectedTemplate').value = templateName;
                    document.querySelectorAll('.template-option').forEach(el => el.classList.remove('active-template'));
                    element.classList.add('active-template');
                    document.querySelectorAll('.template-section').forEach(el => el.classList.add('d-none'));
                    document.getElementById('section-' + templateName).classList.remove('d-none');
                }
            </script>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>