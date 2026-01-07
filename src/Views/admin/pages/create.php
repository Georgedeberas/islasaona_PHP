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

        <div class="flex-grow-1 bg-light">
            <!-- Header Sticky -->
            <div class="bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center sticky-top shadow-sm"
                style="z-index: 1000;">
                <div>
                    <h2 class="h4 m-0 fw-bold">Crear Nueva Página <span
                            class="badge bg-warning text-dark fs-6 ms-2">v2.2</span></h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="/admin/pages">Páginas</a></li>
                            <li class="breadcrumb-item active">Nueva</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="/admin/pages" class="btn btn-outline-secondary">Cancelar</a>
                    <!-- Submit button moved here for easy access, tied to form via ID -->
                    <button type="submit" form="createPageForm" class="btn btn-primary px-4 fw-bold">🚀 Publicar
                        Página</button>
                </div>
            </div>

            <form id="createPageForm" method="POST" enctype="multipart/form-data" class="p-4">
                <input type="hidden" name="template" id="selectedTemplate" value="classic">

                <div class="container-fluid p-0">
                    <div class="row g-4">

                        <!-- LEFT SIDEBAR: CONFIG & TEMPLATE -->
                        <div class="col-lg-3">
                            <!-- 1. General Config -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white fw-bold border-0 pt-3">⚙️ Configuración</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Título</label>
                                        <input type="text" name="title" class="form-control" required
                                            placeholder="Ej: Nuestra Historia">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Slug /
                                            URL</label>
                                        <input type="text" name="slug" class="form-control form-control-sm"
                                            placeholder="Autogenerado si vacío">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Orden</label>
                                        <input type="number" name="order_index" class="form-control form-control-sm"
                                            value="0">
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Template Selector -->
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white fw-bold border-0 pt-3">🎨 Plantilla</div>
                                <div class="card-body p-2 d-flex flex-column gap-2">
                                    <?php
                                    $templates = [
                                        'classic' => ['icon' => '📝', 'title' => 'Clásica', 'desc' => 'Texto simple.'],
                                        'landing' => ['icon' => '🚀', 'title' => 'Landing', 'desc' => 'Hero & Features.'],
                                        'gallery' => ['icon' => '🖼️', 'title' => 'Galería', 'desc' => 'Grid de fotos.']
                                    ];
                                    foreach ($templates as $key => $t):
                                        ?>
                                        <div class="template-option p-3 border rounded bg-white cursor-pointer position-relative <?= $key == 'classic' ? 'active-template' : '' ?>"
                                            onclick="selectTemplate('<?= $key ?>', this)">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="m-0 text-dark"><?= $t['icon'] ?>     <?= $t['title'] ?></h6>
                                                <button type="button" class="btn btn-xs btn-light border btn-preview"
                                                    onclick="event.stopPropagation(); showPreview('<?= $key ?>')"
                                                    title="Ver estructura">👁️</button>
                                            </div>
                                            <p class="m-0 text-muted small lh-sm"><?= $t['desc'] ?></p>
                                        </div>
                                    <?php endforeach; ?>

                                    <hr class="my-2">
                                    <button type="button" class="btn btn-warning btn-sm w-100 fw-bold text-dark"
                                        onclick="loadSampleData()">
                                        🪄 Cargar Ejemplo
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT MAIN AREA: DYNAMIC CONTENT -->
                        <div class="col-lg-9">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h5 class="m-0 text-secondary" id="contentTitle">Contenido: <span
                                            class="fw-bold text-dark">Clásica</span></h5>
                                </div>
                                <div class="card-body p-4 bg-white relative">

                                    <!-- SECCIÓN: CLÁSICA -->
                                    <div id="section-classic" class="template-section">
                                        <textarea id="editorContent" name="content" class="form-control"
                                            style="min-height: 500px;"></textarea>
                                    </div>

                                    <!-- SECCIÓN: LANDING PAGE -->
                                    <div id="section-landing" class="template-section d-none">
                                        <div class="bg-light p-4 rounded-3 mb-4 border border-dashed">
                                            <h6 class="text-uppercase text-muted fw-bold mb-3 small">Hero Section
                                                (Encabezado)</h6>
                                            <div class="row g-3">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-bold">Título Principal</label>
                                                    <input type="text" name="hero_title" id="hero_title"
                                                        class="form-control form-control-lg"
                                                        placeholder="Un título impactante">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold">Imagen de Fondo</label>
                                                    <input type="file" name="hero_image" class="form-control">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Subtítulo</label>
                                                    <input type="text" name="hero_subtitle" id="hero_subtitle"
                                                        class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Texto Botón</label>
                                                    <input type="text" name="cta_text" id="cta_text"
                                                        class="form-control" placeholder="Ej: Reservar">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Enlace Botón</label>
                                                    <input type="text" name="cta_link" id="cta_link"
                                                        class="form-control" placeholder="/tours">
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="text-uppercase text-muted fw-bold mb-3 small mt-5">Bloques de
                                            Características</h6>
                                        <div class="row g-3">
                                            <?php for ($i = 0; $i < 3; $i++): ?>
                                                <div class="col-md-4">
                                                    <div class="card h-100 border-light shadow-sm">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span class="badge bg-light text-dark border">Bloque
                                                                    <?= $i + 1 ?></span>
                                                                <select name="feature_icon[]"
                                                                    class="form-select form-select-sm w-auto border-0 bg-light">
                                                                    <option value="star">⭐</option>
                                                                    <option value="check">✅</option>
                                                                    <option value="heart">❤️</option>
                                                                    <option value="map">🗺️</option>
                                                                </select>
                                                            </div>
                                                            <input type="text" name="feature_title[]"
                                                                id="feature_title_<?= $i ?>"
                                                                class="form-control fw-bold mb-2" placeholder="Título">
                                                            <textarea name="feature_desc[]" id="feature_desc_<?= $i ?>"
                                                                class="form-control form-control-sm" rows="3"
                                                                placeholder="Descripción..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <!-- SECCIÓN: GALERÍA -->
                                    <div id="section-gallery" class="template-section d-none">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Descripción del Álbum</label>
                                                <textarea name="gallery_description" id="gallery_description"
                                                    class="form-control" rows="10"
                                                    placeholder="Describe qué verán los visitantes en esta galería..."></textarea>
                                            </div>
                                            <div class="col-md-8">
                                                <div
                                                    class="border-2 border-dashed border-light rounded-3 p-5 text-center bg-light">
                                                    <div class="display-1 text-muted mb-3">📸</div>
                                                    <h5 class="text-muted">Arrastra fotos aquí o haz clic</h5>
                                                    <input type="file" name="gallery_photos[]" class="form-control mt-3"
                                                        multiple accept="image/*">
                                                    <small class="text-muted d-block mt-2">Soporta JPG, PNG,
                                                        WebP</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

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