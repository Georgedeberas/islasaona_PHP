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
                            <div class="card-body p-2 d-flex flex-column gap-2">
                                <div class="template-option p-3 border rounded bg-white cursor-pointer active-template"
                                    onclick="selectTemplate('classic', this)">
                                    <h6 class="mb-1">📝 Clásica</h6>
                                    <small class="text-muted">Texto enriquecido, imágenes simples. Ideal para políticas
                                        o info.</small>
                                </div>
                                <div class="template-option p-3 border rounded bg-white cursor-pointer"
                                    onclick="selectTemplate('landing', this)">
                                    <h6 class="mb-1">🚀 Landing Page</h6>
                                    <small class="text-muted">Hero Image grande, bloques de características y
                                        CTA.</small>
                                </div>
                                <div class="template-option p-3 border rounded bg-white cursor-pointer"
                                    onclick="selectTemplate('gallery', this)">
                                    <h6 class="mb-1">🖼️ Galería Visual</h6>
                                    <small class="text-muted">Enfoque en fotos, grid masonry y lightbox.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Áreas Dinámicas -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between">
                        <span>Contenido de la Página</span>
                        <span class="badge bg-primary" id="currentTemplateBadge">Clásica</span>
                    </div>
                    <div class="card-body p-4">

                        <!-- SECCIÓN: CLÁSICA (Default) -->
                        <div id="section-classic" class="template-section">
                            <label class="form-label fw-bold">Editor de Texto</label>
                            <textarea id="editorContent" name="content"></textarea>
                        </div>

                        <!-- SECCIÓN: LANDING PAGE -->
                        <div id="section-landing" class="template-section d-none">
                            <h5 class="text-primary border-bottom pb-2 mb-3">Cabecera (Hero Section)</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Título Principal (Hero)</label>
                                    <input type="text" name="hero_title" class="form-control"
                                        placeholder="Impactante y breve">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subtítulo</label>
                                    <input type="text" name="hero_subtitle" class="form-control"
                                        placeholder="Descripción corta">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Imagen de Fondo (Hero)</label>
                                <input type="file" name="hero_image" class="form-control" accept="image/*">
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Texto Botón (CTA)</label>
                                    <input type="text" name="cta_text" class="form-control"
                                        placeholder="Ej: Reservar Ahora">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Enlace Botón</label>
                                    <input type="text" name="cta_link" class="form-control" placeholder="/tours">
                                </div>
                            </div>

                            <h5 class="text-primary border-bottom pb-2 mb-3">Bloques de Características</h5>
                            <div id="features-container">
                                <div class="row g-2 mb-2 feature-row">
                                    <div class="col-md-4">
                                        <input type="text" name="feature_title[]" class="form-control form-control-sm"
                                            placeholder="Título Característica">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="feature_desc[]" class="form-control form-control-sm"
                                            placeholder="Descripción breve">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="feature_icon[]" class="form-select form-select-sm">
                                            <option value="star">⭐ Estrella</option>
                                            <option value="check">✅ Check</option>
                                            <option value="heart">❤️ Corazón</option>
                                            <option value="map">🗺️ Mapa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-2 feature-row">
                                    <div class="col-md-4">
                                        <input type="text" name="feature_title[]" class="form-control form-control-sm"
                                            placeholder="Título Característica 2">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="feature_desc[]" class="form-control form-control-sm"
                                            placeholder="Descripción breve">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="feature_icon[]" class="form-select form-select-sm">
                                            <option value="star">⭐ Estrella</option>
                                            <option value="check" selected>✅ Check</option>
                                            <option value="heart">❤️ Corazón</option>
                                            <option value="map">🗺️ Mapa</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Añade hasta 3 características clave.</small>
                            <div class="mt-4 border-top pt-3">
                                <label class="form-label fw-bold">Contenido Extra (Opcional)</label>
                                <textarea name="content_landing" class="form-control" rows="4"
                                    placeholder="Texto adicional debajo de los bloques..."></textarea>
                                <!-- Nota: Podríamos reutilizar el editor principal para esto si se desea -->
                            </div>
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
                                <input type="file" name="gallery_photos[]" class="form-control" multiple
                                    accept="image/*">
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