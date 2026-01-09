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

        <div class="flex-grow-1 bg-light">
            <!-- Header Sticky -->
            <div class="bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center sticky-top shadow-sm"
                style="z-index: 1000;">
                <div>
                    <h2 class="h4 m-0 fw-bold">Editando: <span
                            class="text-primary"><?= htmlspecialchars($page['title']) ?></span></h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="/admin/pages">Páginas</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="/admin/pages" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" form="editPageForm" class="btn btn-primary px-4 fw-bold">💾 Guardar
                        Cambios</button>
                </div>
            </div>

            <form id="editPageForm" method="POST" enctype="multipart/form-data" class="p-4">
                <input type="hidden" name="template" id="selectedTemplate"
                    value="<?= $page['template'] ?? 'classic' ?>">

                <div class="container-fluid p-0">
                    <div class="row g-4">

                        <!-- LEFT SIDEBAR -->
                        <div class="col-lg-3">
                            <!-- 1. General Config -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white fw-bold border-0 pt-3">⚙️ Configuración</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Título</label>
                                        <input type="text" name="title" class="form-control" required
                                            value="<?= htmlspecialchars($page['title']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Slug /
                                            URL</label>
                                        <input type="text" name="slug" class="form-control form-control-sm"
                                            value="<?= htmlspecialchars($page['slug']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Orden</label>
                                        <input type="number" name="order_index" class="form-control form-control-sm"
                                            value="<?= $page['order_index'] ?>">
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
                                    $currentTpl = $page['template'] ?? 'classic';
                                    foreach ($templates as $key => $t):
                                        ?>
                                        <div class="template-option p-3 border rounded bg-white cursor-pointer position-relative <?= $currentTpl == $key ? 'active-template' : '' ?>"
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
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT MAIN AREA -->
                        <div class="col-lg-9">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h5 class="m-0 text-secondary" id="contentTitle">Contenido</h5>
                                </div>
                                <div class="card-body p-4 bg-white relative">

                                    <?php $meta = $page['meta_data'] ?? []; ?>

                                    <!-- SECCIÓN: CLÁSICA -->
                                    <div id="section-classic"
                                        class="template-section <?= $currentTpl == 'classic' ? '' : 'd-none' ?>">
                                        <textarea id="editorContent" name="content" class="form-control"
                                            style="min-height: 500px;"><?= htmlspecialchars($page['content']) ?></textarea>
                                    </div>

                                    <!-- SECCIÓN: LANDING PAGE -->
                                    <div id="section-landing"
                                        class="template-section <?= $currentTpl == 'landing' ? '' : 'd-none' ?>">
                                        <div class="bg-light p-4 rounded-3 mb-4 border border-dashed">
                                            <h6 class="text-uppercase text-muted fw-bold mb-3 small">Hero Section</h6>
                                            <div class="row g-3">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-bold">Título Principal</label>
                                                    <input type="text" name="hero_title" id="hero_title"
                                                        class="form-control form-control-lg"
                                                        value="<?= htmlspecialchars($meta['hero_title'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold">Imagen de Fondo</label>
                                                    <?php if (!empty($meta['hero_image'])): ?>
                                                        <div class="mb-2"><img src="<?= $meta['hero_image'] ?>"
                                                                style="height: 60px; border-radius: 4px;" class="border">
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="file" name="hero_image" class="form-control">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Subtítulo</label>
                                                    <input type="text" name="hero_subtitle" id="hero_subtitle"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($meta['hero_subtitle'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Texto Botón</label>
                                                    <input type="text" name="cta_text" id="cta_text"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($meta['cta_text'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Enlace Botón</label>
                                                    <input type="text" name="cta_link" id="cta_link"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($meta['cta_link'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="text-uppercase text-muted fw-bold mb-3 small mt-5">Bloques de
                                            Características</h6>
                                        <div class="row g-3">
                                            <?php
                                            $features = $meta['features'] ?? [];
                                            for ($i = 0; $i < 3; $i++):
                                                $f = $features[$i] ?? ['title' => '', 'desc' => '', 'icon' => 'check'];
                                                ?>
                                                <div class="col-md-4">
                                                    <div class="card h-100 border-light shadow-sm">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span class="badge bg-light text-dark border">Bloque
                                                                    <?= $i + 1 ?></span>
                                                                <select name="feature_icon[]"
                                                                    class="form-select form-select-sm w-auto border-0 bg-light">
                                                                    <option value="star" <?= $f['icon'] == 'star' ? 'selected' : '' ?>>⭐</option>
                                                                    <option value="check"
                                                                        <?= $f['icon'] == 'check' ? 'selected' : '' ?>>✅</option>
                                                                    <option value="heart"
                                                                        <?= $f['icon'] == 'heart' ? 'selected' : '' ?>>❤️</option>
                                                                    <option value="map" <?= $f['icon'] == 'map' ? 'selected' : '' ?>>🗺️</option>
                                                                </select>
                                                            </div>
                                                            <input type="text" name="feature_title[]"
                                                                id="feature_title_<?= $i ?>"
                                                                class="form-control fw-bold mb-2" placeholder="Título"
                                                                value="<?= htmlspecialchars($f['title']) ?>">
                                                            <textarea name="feature_desc[]" id="feature_desc_<?= $i ?>"
                                                                class="form-control form-control-sm" rows="3"
                                                                placeholder="Descripción..."><?= htmlspecialchars($f['desc']) ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <!-- SECCIÓN: GALERÍA -->
                                    <div id="section-gallery"
                                        class="template-section <?= $currentTpl == 'gallery' ? '' : 'd-none' ?>">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Descripción del Álbum</label>
                                                <textarea name="gallery_description" id="gallery_description"
                                                    class="form-control"
                                                    rows="10"><?= htmlspecialchars($meta['gallery_description'] ?? '') ?></textarea>
                                            </div>
                                            <div class="col-md-8">
                                                <div
                                                    class="border-2 border-dashed border-light rounded-3 p-4 bg-light mb-3">
                                                    <h6 class="text-muted">Añadir Más Fotos</h6>
                                                    <input type="file" name="gallery_photos[]" class="form-control mt-2"
                                                        multiple accept="image/*">
                                                </div>

                                                <?php if (!empty($meta['images'])): ?>
                                                    <h6 class="text-muted small text-uppercase fw-bold mb-2">Fotos Actuales
                                                    </h6>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <?php foreach ($meta['images'] as $img): ?>
                                                            <div class="position-relative">
                                                                <img src="<?= $img ?>"
                                                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;"
                                                                    class="border shadow-sm">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- SEO Box -->
                        <div class="card shadow-sm border-0 mt-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="m-0 fw-bold text-primary">Optimización SEO (Google)</h6>
                            </div>
                            <div class="card-body p-4 bg-white">
                                <div class="mb-3">
                                    <label class="form-label">Meta Título</label>
                                    <input type="text" name="meta_title" class="form-control"
                                        placeholder="Título que aparece en Google (opcional)"
                                        value="<?= htmlspecialchars($meta['meta_title'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Meta Descripción</label>
                                    <textarea name="meta_description" class="form-control" rows="2"
                                        placeholder="Resumen para Google (opcional)"><?= htmlspecialchars($meta['meta_description'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Palabras Clave (Keywords)</label>
                                    <input type="text" name="keywords" class="form-control"
                                        placeholder="tours, saona, playa, ..."
                                        value="<?= htmlspecialchars($meta['keywords'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .cursor-pointer { cursor: pointer; transition: all 0.2s; }
        .template-option:hover { border-color: #0d6efd !important; background-color: #f8f9fa !important; }
        .active-template { border: 2px solid #0d6efd !important; background-color: #e7f1ff !important; }
    </style>

    <!-- External Libs -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Admin UI -->
    <script src="/assets/js/admin_ui.js"></script>
</body>
</html>