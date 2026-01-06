<!-- Sidebar -->
<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; min-height: 100vh;">
    <a href="/admin/dashboard"
        class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4 fw-bold text-warning">Mochileros RD</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="/admin/dashboard"
                class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active bg-warning text-dark' : '' ?>"
                aria-current="page">
                📊 Dashboard
            </a>
        </li>
        <li>
            <a href="/admin/tours"
                class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], '/admin/tours') !== false ? 'active bg-warning text-dark' : '' ?>">
                🏝️ Mis Tours
            </a>
        </li>
        <li>
            <a href="/admin/pages"
                class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], '/admin/pages') !== false ? 'active bg-warning text-dark' : '' ?>">
                📝 Contenidos (Páginas)
            </a>
        </li>
        <li>
            <a href="/admin/settings"
                class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active bg-warning text-dark' : '' ?>">
                ⚙️ Configuración
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="" width="32" height="32"
                class="rounded-circle me-2">
            <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="/" target="_blank">Ver Sitio Web</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="/admin/logout">Cerrar Sesión</a></li>
        </ul>
    </div>
</div>