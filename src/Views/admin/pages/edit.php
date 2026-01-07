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

            <form method="POST">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Título</label>
                                <input type="text" name="title" class="form-control"
                                    value="<?= htmlspecialchars($page['title']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Slug (URL)</label>
                                <input type="text" name="slug" class="form-control"
                                    value="<?= htmlspecialchars($page['slug']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Orden</label>
                                <input type="number" name="order_index" value="<?= $page['order_index'] ?? 0 ?>"
                                    class="form-control text-center">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Contenido</label>
                            <textarea id="editorContent"
                                name="content"><?= htmlspecialchars($page['content']) ?></textarea>
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