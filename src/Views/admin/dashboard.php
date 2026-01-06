<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mochileros RD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Mochileros RD Admin</a>
            <div class="d-flex">
                <a href="/" target="_blank" class="btn btn-outline-light me-2">Ver Sitio</a>
                <a href="/admin/logout" class="btn btn-outline-danger">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestión de Tours</h2>
            <a href="/admin/tours/create" class="btn btn-success">+ Nuevo Tour</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Precio (Adulto)</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tours as $tour): ?>
                            <tr>
                                <td>
                                    <?= $tour['id'] ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (isset($tour['cover_image'])): ?>
                                            <img src="/<?= $tour['cover_image'] ?>"
                                                style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 4px;">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($tour['title']) ?>
                                    </div>
                                </td>
                                <td>$
                                    <?= number_format($tour['price_adult'], 2) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $tour['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $tour['is_active'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/tours/edit?id=<?= $tour['id'] ?>"
                                        class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>