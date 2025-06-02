<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/warehouses">Склады</a></li>
            <li class="breadcrumb-item active" aria-current="page">Остатки</li>
        </ol>
    </nav>
    <h4>Остатки</h4>
    <?php if (!empty($userPermissions) && (in_array('admin', $userPermissions) || in_array('maslosklad.manage', $userPermissions))): ?>
        <div class="mb-3">
            <form method="post" action="/warehouses/inventory/recalc" class="mb-3">
                <button type="submit" class="btn btn-warning">Пересчитать остатки</button>
            </form>
            <a href="/warehouses/inventory/log" target="_blank" class="btn btn-secondary ms-2">Логи остатков</a>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <form class="row g-2 mb-3" id="inventoryFilterForm" autocomplete="off" method="get" action="/warehouses/inventory">
        <div class="col-md-3 col-12">
            <select name="warehouse_type_id" id="warehouseTypeFilter" class="form-select">
                <option value="">Все типы складов</option>
                <?php foreach ($warehouseTypes as $wt): ?>
                    <option value="<?= $wt['id'] ?>" <?= ($filters['warehouse_type_id'] == $wt['id']) ? 'selected' : '' ?>><?= htmlspecialchars($wt['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5 col-12">
            <input type="text" name="search" id="inventorySearch" class="form-control" placeholder="Поиск по наименованию или артикулу..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
        <div class="col-md-2 col-6">
            <button type="submit" class="btn btn-primary w-100">Показать</button>
        </div>
    </form>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="inventoryTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Наименование</th>
                            <th>Дата обновления</th>
                            <th>Категория</th>
                            <th>Место хранения</th>
                            <th>Остаток</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Нет данных по остаткам</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><a href="/warehouses/items/history/<?= $item['item_id'] ?? $item['id'] ?>"><?= htmlspecialchars($item['item_name'] ?? '') ?></a></td>
                                    <td><?= !empty($item['last_update']) ? date('d.m.Y H:i', strtotime($item['last_update'])) : '' ?></td>
                                    <td><?= htmlspecialchars($item['category_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['warehouse_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?> <?= htmlspecialchars($item['unit'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    function updateInventoryTable() {
        let params = {
            search: $('#inventorySearch').val(),
            warehouse_type_id: $('#warehouseTypeFilter').val()
        };
        $.get('/warehouses/inventory/search', params, function(html) {
            $('#inventoryTableBody').html(html);
        });
    }
    $('#inventorySearch').on('input', function() {
        updateInventoryTable();
    });
    $('#warehouseTypeFilter').on('change', function() {
        updateInventoryTable();
    });
    $('#inventoryFilterForm').on('submit', function(e) { e.preventDefault(); });
});
</script> 