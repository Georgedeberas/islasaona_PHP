<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Página - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#editorContent',
            height: 600, // Alto del editor
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            branding: false,
            promotion: false
        });
    </script>
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <?php require __DIR__ . '/../layout/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Editando: <span class="text-primary">
                        <?= htmlspecialchars($page['title']) ?>
                    </span></h2>
                <a href="/admin/pages" class="btn btn-outline-secondary">Cancelar</a>
            </div>

            <form method="POST">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contenido de la página</label>
                            <textarea id="editorContent"
                                name="content"><?= htmlspecialchars($page['content']) ?></textarea>
                            <div class="form-text mt-2">
                                Puedes usar negritas, listas, e insertar imágenes externas. Ten cuidado al copiar desde
                                Word.
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">💾 Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>