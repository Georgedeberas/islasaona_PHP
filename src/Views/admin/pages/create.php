<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Página - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"
        referrerpolicy="no-referrer"></script>
    <script>
        tinymce.init({
            selector: '#editorContent',
            height: 500,
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
                <h2>Crear Nueva Página <span class="badge bg-warning text-dark fs-6">v2.1 (OpenSource)</span></h2>
                <a href="/admin/pages" class="btn btn-outline-secondary">Cancelar</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="pageForm">
                <input type="hidden" name="template" id="selectedTemplate" value="classic">

                <div class="row g-4 mb-4">
                    <!-- Configuración Base -->
                    <div class="col-md-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white fw-bold">Configuración General</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Título de la Página</label>
                                    <input type="text" name="title" class="form-control" required
                                        placeholder="Ej: Nuestra Historia">
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold">Slug / URL</label>
                                        <input type="text" name="slug" class="form-control"
                                            placeholder="Ej: nuestra-historia">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Orden</label>
                                        <input type="number" name="order_index" value="0" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selector de Plantilla -->
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 bg-light">
                            <div class="card-header fw-bold">Elegir Plantilla</div>
                            <?php
                            $templates = [
                                'classic' => ['icon' => '📝', 'title' => 'Clásica', 'desc' => 'Texto enriquecido estándar.', 'hassample' => true],
                                'landing' => ['icon' => '🚀', 'title' => 'Landing Page', 'desc' => 'Hero, Features y CTA.', 'hassample' => true],
                                'gallery' => ['icon' => '🖼️', 'title' => 'Galería Visual', 'desc' => 'Grid de fotos y Lightbox.', 'hassample' => true]
                            ];
                            foreach ($templates as $key => $t):
                                ?>
                                <div class="template-option p-3 border rounded bg-white cursor-pointer d-flex justify-content-between align-items-center <?= $key == 'classic' ? 'active-template' : '' ?>"
                                    onclick="selectTemplate('<?= $key ?>', this)">
                                    <div>
                                        <h6 class="mb-1"><?= $t['icon'] ?>     <?= $t['title'] ?></h6>
                                        <small class="text-muted"><?= $t['desc'] ?></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                        onclick="event.stopPropagation(); showPreview('<?= $key ?>')">
                                        👁️
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3 text-center">
                            <button type="button" class="btn btn-warning btn-sm w-100" onclick="loadSampleData()">
                                🪄 Cargar Datos de Ejemplo
                            </button>
                        </div>
                    </div>
                </div>
        </div>

        <!-- Áreas Dinámicas -->
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Contenido de la Página</div>
            <div class="card-body p-4">

                <!-- SECCIÓN: CLÁSICA -->
                <div id="section-classic" class="template-section">
                    <label class="form-label fw-bold">Editor de Texto</label>
                    <textarea id="editorContent" name="content"></textarea>
                </div>

                <!-- SECCIÓN: LANDING PAGE -->
                <div id="section-landing" class="template-section d-none">
                    <h5 class="text-primary border-bottom pb-2 mb-3">Cabecera (Hero Section)</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Título Principal</label>
                            <input type="text" name="hero_title" id="hero_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="hero_subtitle" id="hero_subtitle" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen de Fondo <small class="text-muted">(Opcional, se usará
                                placeholder si vacío)</small></label>
                        <input type="file" name="hero_image" class="form-control" accept="image/*">
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Texto CTA</label>
                            <input type="text" name="cta_text" id="cta_text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Enlace CTA</label>
                            <input type="text" name="cta_link" id="cta_link" class="form-control">
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-3">Bloques de Características</h5>
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="row g-2 mb-2 feature-row">
                            <div class="col-md-4">
                                <input type="text" name="feature_title[]" id="feature_title_<?= $i ?>"
                                    class="form-control form-control-sm" placeholder="Título">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="feature_desc[]" id="feature_desc_<?= $i ?>"
                                    class="form-control form-control-sm" placeholder="Descripción">
                            </div>
                            <div class="col-md-2">
                                <select name="feature_icon[]" class="form-select form-select-sm">
                                    <option value="star">⭐</option>
                                    <option value="check">✅</option>
                                    <option value="heart">❤️</option>
                                    <option value="map">🗺️</option>
                                </select>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- SECCIÓN: GALERÍA -->
                <div id="section-gallery" class="template-section d-none">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción del Álbum</label>
                        <textarea name="gallery_description" class="form-control" rows="3"
                            placeholder="Describe qué estamos viendo en esta galería..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subir Fotos</label>
                        <input type="file" name="gallery_photos[]" class="form-control" multiple accept="image/*">
                        <div class="form-text">Puedes seleccionar múltiples archivos a la vez.</div>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-light p-3 text-end sticky-bottom">
                <button type="submit" class="btn btn-success btn-lg px-5">Crear Página</button>
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
                // Update hidden input
                document.getElementById('selectedTemplate').value = templateName;

                // Update visual state of cards
                document.querySelectorAll('.template-option').forEach(el => el.classList.remove('active-template'));
                element.classList.add('active-template');

                // Update Badge
                const labels = { 'classic': 'Clásica', 'landing': 'Landing Page', 'gallery': 'Galería Visual' };
                document.getElementById('currentTemplateBadge').textContent = labels[templateName];

                // Hide all sections
                document.querySelectorAll('.template-section').forEach(el => el.classList.add('d-none'));

                // Show selected section
                document.getElementById('section-' + templateName).classList.remove('d-none');
            }
        </script>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>