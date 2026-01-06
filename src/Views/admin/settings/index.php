<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="d-flex">
        <!-- Sidebar -->
        <?php require __DIR__ . '/../layout/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between mb-4">
                <h2>Configuración del Sitio</h2>
                <a href="/admin/dashboard" class="btn btn-secondary">Volver al Dashboard</a>
            </div>

            <?php if (isset($_GET['saved'])): ?>
                <div class="alert alert-success">Configuración guardada correctamente.</div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="/admin/settings">

                        <?php
                        $groups = [
                            'Contacto' => ['phone_main', 'whatsapp_number', 'email_contact', 'address'],
                            'Redes Sociales' => ['facebook_url', 'instagram_url', 'tiktok_url'],
                            'General' => ['company_name', 'footer_text']
                        ];

                        $settingsMap = [];
                        foreach ($settings as $s) {
                            $settingsMap[$s['setting_key']] = $s;
                        }
                        ?>

                        <?php foreach ($groups as $groupName => $keys): ?>
                            <h4 class="mt-4 mb-3 border-bottom pb-2 text-primary"><?= $groupName ?></h4>
                            <div class="row">
                                <?php foreach ($keys as $key):
                                    $s = $settingsMap[$key] ?? null;
                                    if (!$s)
                                        continue;
                                    ?>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold"><?= $s['label'] ?></label>
                                        <?php if ($s['type'] === 'textarea'): ?>
                                            <textarea name="<?= $s['setting_key'] ?>" class="form-control"
                                                rows="3"><?= htmlspecialchars($s['setting_value']) ?></textarea>
                                        <?php else: ?>
                                            <input type="text" name="<?= $s['setting_key'] ?>" class="form-control"
                                                value="<?= htmlspecialchars($s['setting_value']) ?>">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="submit" class="btn btn-primary btn-lg">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>