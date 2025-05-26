<?php
// @var array $items
?>
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/warehouses">Склады</a></li>
            <li class="breadcrumb-item active" aria-current="page">Имущество</li>
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
        <div class="col-12 col-md-4 mb-2 mb-md-0">
            <form method="get" action="/warehouses/items" class="d-flex align-items-center">
                <select name="warehouse_type_id" class="form-select me-2">
                    <option value="">Все типы складов</option>
                    <?php foreach ($warehouseTypes as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= (!empty($selectedWarehouseTypeId) && $selectedWarehouseTypeId == $type['id']) ? 'selected' : '' ?>><?= htmlspecialchars($type['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-outline-primary">Показать</button>
            </form>
        </div>
        <div class="col-12 col-md-4">
            <input type="text" id="itemSearch" class="form-control" placeholder="Поиск по имуществу...">
        </div>
        <div class="col-12 col-md-4 text-end">
            <a href="/warehouses/items/categories" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list"></i> Категории имущества
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Имущество</h4>
                        <p class="text-sm mb-0 text-muted">Управление имуществом склада</p>
                    </div>
                    <a href="/warehouses/items/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus me-1"></i> Добавить
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="itemsTable" class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Наименование</th>
                                    <th>Артикул</th>
                                    <th>Категория</th>
                                    <th>Тип склада</th>
                                    <th width="150">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Список имущества пуст</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= $item['id'] ?></td>
                                            <td><?= htmlspecialchars($item['name']) ?></td>
                                            <td><?= htmlspecialchars($item['article']) ?></td>
                                            <td><?= htmlspecialchars($item['category_name'] ?? $item['category_id']) ?></td>
                                            <td>
                                                <?php
                                                // Получаем warehouse_type_id через категорию
                                                $category = null;
                                                if (!empty($categories)) {
                                                    foreach ($categories as $cat) {
                                                        if ($cat['id'] == ($item['category_id'] ?? null)) {
                                                            $category = $cat;
                                                            break;
                                                        }
                                                    }
                                                }
                                                $typeName = '-';
                                                if (!empty($category['warehouse_type_id']) && !empty($warehouseTypes)) {
                                                    foreach ($warehouseTypes as $type) {
                                                        if ($type['id'] == $category['warehouse_type_id']) {
                                                            $typeName = $type['name'];
                                                            break;
                                                        }
                                                    }
                                                }
                                                echo $typeName;
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/warehouses/items/edit/<?= $item['id'] ?>" class="btn btn-outline-secondary" title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="/warehouses/items/delete/<?= $item['id'] ?>" method="POST" style="display:inline;">
                                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Удалить?')" title="Удалить">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
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
$(document).ready(function() {
    // Сортировка по ID по убыванию при первой загрузке
    let rows = $('#itemsTable tbody tr').get();
    rows.sort(function(a, b) {
        let idA = parseInt($(a).find('td:first').text());
        let idB = parseInt($(b).find('td:first').text());
        return idB - idA;
    });
    $.each(rows, function(index, row) {
        $('#itemsTable tbody').append(row);
    });

    $('#itemSearch').on('input', function() {
        let q = $(this).val();
        let warehouseTypeId = $('select[name="warehouse_type_id"]').val();
        $.get('/warehouses/items/search', {q: q, warehouse_type_id: warehouseTypeId}, function(data) {
            let tbody = $('#itemsTable tbody');
            tbody.empty();
            if (data.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center py-4"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><p class="text-muted">Ничего не найдено</p></td></tr>');
            } else {
                // Сортировка по ID по убыванию для ajax-результатов
                data.sort(function(a, b) { return b.id - a.id; });
                data.forEach(function(item) {
                    tbody.append(
                        `<tr>
                            <td>${item.id}</td>
                            <td>${item.name}</td>
                            <td>${item.article ?? ''}</td>
                            <td>${item.category_name ?? ''}</td>
                            <td>${item.warehouse_type_name ?? '-'}</td>
                            <td>
                                <div class=\"btn-group btn-group-sm\">
                                    <a href=\"/warehouses/items/edit/${item.id}\" class=\"btn btn-outline-secondary\" title=\"Редактировать\">
                                        <i class=\"bi bi-pencil\"></i>
                                    </a>
                                    <form action=\"/warehouses/items/delete/${item.id}\" method=\"POST\" style=\"display:inline;\">
                                        <button type=\"submit\" class=\"btn btn-outline-danger\" onclick=\"return confirm('Удалить?')\" title=\"Удалить\">
                                            <i class=\"bi bi-trash\"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>`
                    );
                });
            }
        }, 'json');
    });
});
</script> 