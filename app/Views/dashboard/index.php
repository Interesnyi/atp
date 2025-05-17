<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Панель управления</h1>
            <p class="lead">
                Добро пожаловать, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Пользователь' ?>!
                Выберите нужный раздел для работы.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fs-1 mb-3 text-primary"></i>
                    <h5 class="card-title">Финансы</h5>
                    <p class="card-text">Управление доходами и расходами, контроль средств</p>
                    <a href="/balance" class="btn btn-primary">Перейти</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-boxes fs-1 mb-3 text-success"></i>
                    <h5 class="card-title">Склад масел</h5>
                    <p class="card-text">Работа с инвентарем, учет товаров и поставок</p>
                    <a href="/maslosklad" class="btn btn-success">Перейти</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle fs-1 mb-3 text-info"></i>
                    <h5 class="card-title">Профиль</h5>
                    <p class="card-text">Управление личными данными и настройками</p>
                    <a href="/profile" class="btn btn-info">Перейти</a>
                </div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 mb-3 text-danger"></i>
                    <h5 class="card-title">Пользователи</h5>
                    <p class="card-text">Управление учетными записями пользователей</p>
                    <a href="/users" class="btn btn-danger">Перейти</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div> 