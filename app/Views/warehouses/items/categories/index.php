<?php
// @var array $categories
// @var array $warehouseTypes
?>
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/warehouses">Склады</a></li>
            <li class="breadcrumb-item"><a href="/warehouses/items">Имущество</a></li>
            <li class="breadcrumb-item active" aria-current="page">Категории имущества</li>
        </ol>
    </nav>
    <!-- Flash-сообщения -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="row mb-3">
        <div class="col-12 col-md-6">
            <form method="get" class="d-flex align-items-center">
                <select name="warehouse_type_id" class="form-select me-2" style="min-width:260px;">
                    <option value="">Все типы складов</option>
                    <?php foreach ($warehouseTypes as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= (isset($_GET['warehouse_type_id']) && $_GET['warehouse_type_id'] == $type['id']) ? 'selected' : '' ?>><?= htmlspecialchars($type['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-outline-primary me-2">Показать</button>
                <input type="text" name="q" class="form-control" placeholder="Поиск по категориям..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </form>
        </div>
        <div class="col-12 col-md-6 text-end">
            <a href="/warehouses/items/categories/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus me-1"></i> Добавить
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="categoriesTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Наименование</th>
                                    <th>Описание</th>
                                    <th>Тип склада</th>
                                    <th width="150">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Список категорий пуст</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <?php if (!isset($cat['is_deleted']) || !$cat['is_deleted']): ?>
                                        <tr>
                                            <td><?= $cat['id'] ?></td>
                                            <td><?= htmlspecialchars($cat['name']) ?></td>
                                            <td><?= htmlspecialchars($cat['description'] ?? '') ?></td>
                                            <td><?= !empty($cat['warehouse_type_name']) ? htmlspecialchars($cat['warehouse_type_name']) : '-' ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/warehouses/items/categories/edit/<?= $cat['id'] ?>" class="btn btn-outline-secondary" title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="/warehouses/items/categories/delete/<?= $cat['id'] ?>" method="POST" style="display:inline;">
                                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Удалить?')" title="Удалить">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Удаляю ajax-поиск, теперь фильтрация только через GET-форму, как в разделе имущества
</script> 