<?php
// src/Views/admin/articles/edit.php
$isEdit = isset($article);
$action = $isEdit ? 'Editar Artículo' : 'Nuevo Artículo';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= $action ?>
    </h1>
    <a href="/admin/articles" class="btn btn-outline-secondary">Cancelar</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">¡Guardado correctamente!</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="row g-4">

    <!-- Main Editor -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Título del Artículo</label>
                    <input type="text" name="title" class="form-control form-control-lg" required
                        value="<?= $isEdit ? htmlspecialchars($article['title']) : '' ?>"
                        placeholder="Ej: Los 5 mejores lugares para fotos en Saona...">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Contenido</label>
                    <textarea name="content" id="editor" class="form-control"
                        rows="15"><?= $isEdit ? htmlspecialchars($article['content'] ?? '') : '' ?></textarea>
                    <div class="form-text">Si, este es un editor visual. Puedes agregar negritas, listas y enlaces.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Extracto (Resumen corto)</label>
                    <textarea name="excerpt" class="form-control" rows="3"
                        maxlength="255"><?= $isEdit ? htmlspecialchars($article['excerpt'] ?? '') : '' ?></textarea>
                    <div class="form-text">Se muestra en la lista del blog (Max 255 caracteres).</div>
                </div>
            </div>
        </div>

        <!-- SEO Box -->
        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 fw-bold text-primary">Optimización SEO (Google)</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Meta Título</label>
                    <input type="text" name="seo_title" class="form-control"
                        value="<?= $isEdit ? htmlspecialchars($article['seo_title'] ?? '') : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Descripción</label>
                    <textarea name="seo_description" class="form-control"
                        rows="2"><?= $isEdit ? htmlspecialchars($article['seo_description'] ?? '') : '' ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" class="form-control bg-light"
                        value="<?= $isEdit ? htmlspecialchars($article['slug']) : '' ?>"
                        placeholder="Dejar vacío para autogenerar">
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Options -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1"
                        <?= ($isEdit && $article['is_published']) ? 'checked' : 'checked' ?>>
                    <label class="form-check-label fw-bold" for="is_published">Publicar Inmediatamente</label>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Guardar Cambios</button>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold">Imagen Destacada</h6>
            </div>
            <div class="card-body text-center">
                <?php if ($isEdit && !empty($article['image_path'])): ?>
                    <img src="/<?= $article['image_path'] ?>" class="img-fluid rounded mb-3" style="max-height: 200px;">
                <?php endif; ?>

                <input type="file" name="image" class="form-control" accept="image/*">
                <div class="form-text mt-2">Recomendado: 1200x630px JPG/WebP</div>
            </div>
        </div>
    </div>

</form>

<!-- Include Trumbowyg (Lightweight WYSIWYG Editor) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/ui/trumbowyg.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/trumbowyg.min.js"></script>
<script>
    $('#editor').trumbowyg({
        btns: [
            ['viewHTML'],
            ['undo', 'redo'], // Only supported in some browsers
            ['formatting'],
            ['strong', 'em'],
            ['link'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ],
        autogrow: true
    });
</script>