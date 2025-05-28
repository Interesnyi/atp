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
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-boxes fs-1 mb-3 text-success"></i>
                    <h5 class="card-title">Склады</h5>
                    <p class="card-text">Управление всеми складами, просмотр и настройка.</p>
                    <a href="/warehouses" class="btn btn-success">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-archive fs-1 mb-3 text-primary"></i>
                    <h5 class="card-title">Имущество</h5>
                    <p class="card-text">Управление имуществом склада. Добавление, редактирование, удаление.</p>
                    <a href="/warehouses/items" class="btn btn-primary">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-left-right fs-1 mb-3 text-warning"></i>
                    <h5 class="card-title">Операции</h5>
                    <p class="card-text">Журнал операций по всем складам. История изменений.</p>
                    <a href="/warehouses/operations" class="btn btn-warning">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-data fs-1 mb-3 text-info"></i>
                    <h5 class="card-title">Остатки</h5>
                    <p class="card-text">Просмотр текущих остатков по складам и категориям.</p>
                    <a href="/warehouses/inventory" class="btn btn-info">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-list-check fs-1 mb-3 text-secondary"></i>
                    <h5 class="card-title">Дела</h5>
                    <p class="card-text">Ваши задачи, напоминания и контроль исполнения.</p>
                    <a href="/tasks" class="btn btn-secondary">Перейти</a>
                </div>
            </div>
        </div>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="col-md-6 mb-4">
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