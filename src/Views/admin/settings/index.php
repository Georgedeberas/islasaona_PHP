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
                    <form method="POST" action="/admin/settings" enctype="multipart/form-data">

                        <?php
                        $val = function ($k) use ($settingsMap) {
                            return htmlspecialchars($settingsMap[$k]['setting_value'] ?? '');
                        };
                        ?>

                        <!-- Navigation Tabs -->
                        <ul class="nav nav-tabs mb-4 border-bottom-0">
                            <li class="nav-item">
                                <button class="nav-link active fw-bold" data-bs-toggle="tab"
                                    data-bs-target="#tab-general" type="button">🏢 General & Footer</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-home-hero"
                                    type="button">🏠 Portada</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-home-content"
                                    type="button">🧩 Secciones</button>
                            </li>
                        </ul>

                        <div class="tab-content border rounded p-4 bg-white shadow-sm">

                            <!-- TAB 1: GENERAL & FOOTER -->
                            <div class="tab-pane fade show active" id="tab-general">
                                <h5 class="text-secondary border-bottom pb-2 mb-3">📞 Contacto</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Teléfono Footer</label>
                                        <input type="text" name="contact_phone" class="form-control"
                                            value="<?= $val('contact_phone') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Email</label>
                                        <input type="email" name="contact_email" class="form-control"
                                            value="<?= $val('contact_email') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Dirección Física</label>
                                        <input type="text" name="contact_address" class="form-control"
                                            value="<?= $val('contact_address') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Horarios</label>
                                        <input type="text" name="contact_hours" class="form-control"
                                            value="<?= $val('contact_hours') ?>">
                                    </div>
                                </div>

                                <h5 class="text-secondary border-bottom pb-2 mt-4 mb-3">🌐 Redes Sociales</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Facebook URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="fab fa-facebook text-primary"></i></span>
                                            <input type="url" name="social_facebook" class="form-control"
                                                value="<?= $val('social_facebook') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Instagram URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="fab fa-instagram text-danger"></i></span>
                                            <input type="url" name="social_instagram" class="form-control"
                                                value="<?= $val('social_instagram') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">TikTok URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="fab fa-tiktok text-dark"></i></span>
                                            <input type="url" name="social_tiktok" class="form-control"
                                                value="<?= $val('social_tiktok') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">TripAdvisor URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="fas fa-plane text-success"></i></span>
                                            <input type="url" name="social_tripadvisor" class="form-control"
                                                value="<?= $val('social_tripadvisor') ?>">
                                        </div>
                                    </div>
                                </div>

                                <h5 class="text-secondary border-bottom pb-2 mt-4 mb-3">⚖️ Legal & Sistema</h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small">Copyright Text</label>
                                        <input type="text" name="legal_copyright" class="form-control"
                                            value="<?= $val('legal_copyright') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Link Privacidad</label>
                                        <input type="text" name="legal_privacy_link" class="form-control"
                                            value="<?= $val('legal_privacy_link') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Link Términos</label>
                                        <input type="text" name="legal_terms_link" class="form-control"
                                            value="<?= $val('legal_terms_link') ?>">
                                    </div>
                                </div>
                                <div class="mt-3 p-3 bg-danger-subtle border border-danger rounded">
                                    <label class="fw-bold text-danger mb-1">Modo Mantenimiento</label>
                                    <select name="maintenance_mode" class="form-select border-danger">
                                        <option value="0" <?= $val('maintenance_mode') == '0' ? 'selected' : '' ?>>🟢 Sitio
                                            Activo</option>
                                        <option value="1" <?= $val('maintenance_mode') == '1' ? 'selected' : '' ?>>🔴
                                            MANTENIMIENTO ACTIVADO</option>
                                    </select>
                                </div>
                            </div>

                            <!-- TAB 2: HOME HERO -->
                            <div class="tab-pane fade" id="tab-home-hero">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold">Imagen de Fondo</label>
                                        <div
                                            class="ratio ratio-16x9 bg-secondary rounded mb-2 overflow-hidden position-relative group">
                                            <img src="/<?= $val('home_hero_bg') ?>"
                                                class="object-fit-cover w-100 h-100">
                                            <div
                                                class="position-absolute bottom-0 start-0 bg-dark text-white px-2 py-1 small opacity-75">
                                                Actual</div>
                                        </div>
                                        <input type="file" name="home_hero_bg" class="form-control" accept="image/*">
                                        <div class="form-text small">Se redimensionará y convertirá a WebP
                                            automáticamente.</div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Título Principal (H1)</label>
                                            <input type="text" name="home_hero_title"
                                                class="form-control form-control-lg fw-bold"
                                                value="<?= $val('home_hero_title') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <textarea name="home_hero_subtitle" class="form-control"
                                                rows="2"><?= $val('home_hero_subtitle') ?></textarea>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Texto Botón</label>
                                                <input type="text" name="home_hero_cta_text" class="form-control"
                                                    value="<?= $val('home_hero_cta_text') ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Enlace Botón</label>
                                                <input type="text" name="home_hero_cta_link" class="form-control"
                                                    value="<?= $val('home_hero_cta_link') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: HOME SECTIONS -->
                            <div class="tab-pane fade" id="tab-home-content">

                                <div class="card mb-4 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0 fw-bold"><i class="fas fa-star me-2"></i>Tours Destacados
                                            (Carrusel)</h6>
                                    </div>
                                    <div class="card-body">
                                        <label class="small text-muted mb-2">Selecciona qué tours se mostrarán en la
                                            portada:</label>
                                        <select name="home_featured_tours[]" class="form-select h-auto" multiple
                                            style="min-height: 150px;">
                                            <?php
                                            // Decode JSON safely
                                            $json = $settingsMap['home_featured_tours']['setting_value'] ?? '[]';
                                            $selectedIds = json_decode($json, true);
                                            if (!is_array($selectedIds))
                                                $selectedIds = [];

                                            foreach ($allTours as $t):
                                                ?>
                                                <option value="<?= $t['id'] ?>" <?= in_array($t['id'], $selectedIds) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($t['title']) ?> (ID: <?= $t['id'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text small">Usa Ctrl+Click (Win) o Cmd+Click (Mac) para
                                            seleccionar varios.</div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                        <h6 class="mb-0 fw-bold">Sección: "Bienvenida"</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="home_show_welcome"
                                                value="1" <?= $val('home_show_welcome') == '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label small">Mostrar</label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <label class="form-label-sm fw-bold">Título</label>
                                            <input type="text" name="home_welcome_title" class="form-control"
                                                value="<?= $val('home_welcome_title') ?>">
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label-sm fw-bold">Texto</label>
                                            <textarea name="home_welcome_text" class="form-control"
                                                rows="3"><?= $val('home_welcome_text') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                        <h6 class="mb-0 fw-bold">Sección: "Por qué elegirnos"</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="home_show_why"
                                                value="1" <?= $val('home_show_why') == '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label small">Mostrar</label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <label class="form-label-sm fw-bold">Título</label>
                                            <input type="text" name="home_why_title" class="form-control"
                                                value="<?= $val('home_why_title') ?>">
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label-sm fw-bold">Texto</label>
                                            <textarea name="home_why_text" class="form-control"
                                                rows="3"><?= $val('home_why_text') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4 pb-5">
                            <button type="submit" name="submit" class="btn btn-dark btn-lg">💾 Guardar
                                Configuración</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>