<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/warehouses">Склады</a></li>
            <li class="breadcrumb-item active" aria-current="page">Остатки</li>
        </ol>
    </nav>
    <h4>Остатки</h4>
    <form class="row g-2 mb-3" id="inventoryFilterForm" autocomplete="off">
        <div class="col-md-4 col-12">
            <input type="text" name="search" id="inventorySearch" class="form-control" placeholder="Поиск по наименованию или артикулу..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
        <div class="col-md-2 col-6">
            <select name="warehouse_id" id="warehouseFilter" class="form-select">
                <option value="">Все склады</option>
                <?php foreach ($warehouses as $w): ?>
                    <option value="<?= $w['id'] ?>" <?= ($filters['warehouse_id'] == $w['id']) ? 'selected' : '' ?>><?= htmlspecialchars($w['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 col-6">
            <select name="category_id" id="categoryFilter" class="form-select">
                <option value="">Все категории</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 col-6">
            <select name="has_volume" id="typeFilter" class="form-select">
                <option value="" <?= ($filters['has_volume'] === null || $filters['has_volume'] === '') ? 'selected' : '' ?>>Все типы</option>
                <option value="0" <?= ($filters['has_volume'] === 0 || $filters['has_volume'] === '0') ? 'selected' : '' ?>>Штучное</option>
                <option value="1" <?= ($filters['has_volume'] === 1 || $filters['has_volume'] === '1') ? 'selected' : '' ?>>На розлив</option>
            </select>
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
                            <th>Склад</th>
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
                                    <td><?= htmlspecialchars($item['last_update'] ?? '') ?></td>
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
            warehouse_id: $('#warehouseFilter').val(),
            category_id: $('#categoryFilter').val(),
            has_volume: $('#typeFilter').val()
        };
        $.get('/warehouses/inventory/search', params, function(html) {
            $('#inventoryTableBody').html(html);
        });
    }
    $('#inventorySearch').on('input', function() {
        updateInventoryTable();
    });
    $('#warehouseFilter, #categoryFilter, #typeFilter').on('change', function() {
        updateInventoryTable();
    });
    $('#inventoryFilterForm').on('submit', function(e) { e.preventDefault(); });
});
</script> 