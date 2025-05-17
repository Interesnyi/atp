<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">ELDIR</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2"></i> Дашборд</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/maslosklad"><i class="bi bi-boxes"></i> Склад</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/balance"><i class="bi bi-cash-stack"></i> Касса</a>
                    </li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/users"><i class="bi bi-people"></i> Пользователи</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i> <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Пользователь' ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="/profile"><i class="bi bi-person"></i> Профиль</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Выйти</a></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav> 