<?php /** @var array $operations, $suppliers, $buyers, $propertyTypes, $filters, $title, $warehouseTypes */ ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-6">
                            <h4><?= htmlspecialchars($title) ?></h4>
                            <p class="text-sm mb-0">
                                <i class="bi bi-list-task text-primary me-1"></i>
                                Учёт всех операций по складам
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="/warehouses/operations/create" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus me-1"></i> Добавить операцию
                            </a>
                        </div>
                    </div>
                    <form class="row g-2 mb-3" method="get">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Поиск по имуществу..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="supplier_id" class="form-select">
                                <option value="">Поставщик</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($filters['supplier_id'] == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="buyer_id" class="form-select">
                                <option value="">Получатель</option>
                                <?php foreach ($buyers as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= ($filters['buyer_id'] == $b['id']) ? 'selected' : '' ?>><?= htmlspecialchars($b['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="property_type_id" class="form-select">
                                <option value="">Тип имущества</option>
                                <?php foreach ($propertyTypes as $pt): ?>
                                    <option value="<?= $pt['id'] ?>" <?= ($filters['property_type_id'] == $pt['id']) ? 'selected' : '' ?>><?= htmlspecialchars($pt['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="warehouse_type_id" class="form-select">
                                <option value="">Тип склада</option>
                                <?php foreach ($warehouseTypes as $wt): ?>
                                    <option value="<?= $wt['id'] ?>" <?= ($filters['warehouse_type_id'] == $wt['id']) ? 'selected' : '' ?>><?= htmlspecialchars($wt['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="operation_type" class="form-select">
                                <option value="">Тип операции</option>
                                <option value="1" <?= ($filters['operation_type'] == 1) ? 'selected' : '' ?>>Приемка</option>
                                <option value="2" <?= ($filters['operation_type'] == 2) ? 'selected' : '' ?>>Выдача</option>
                                <option value="3" <?= ($filters['operation_type'] == 3) ? 'selected' : '' ?>>Списание</option>
                                <option value="4" <?= ($filters['operation_type'] == 4) ? 'selected' : '' ?>>Перемещение</option>
                                <option value="5" <?= ($filters['operation_type'] == 5) ? 'selected' : '' ?>>Розлив</option>
                                <option value="6" <?= ($filters['operation_type'] == 6) ? 'selected' : '' ?>>Инвентаризация</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="operationsTable" class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Дата</th>
                                    <th>Тип операции</th>
                                    <th>Имущество</th>
                                    <th>Тип имущества</th>
                                    <th>Поставщик</th>
                                    <th>Получатель</th>
                                    <th>Количество</th>
                                    <th>Сумма</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($operations)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Список операций пуст</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($operations as $op): ?>
                                        <tr>
                                            <td data-order="<?= htmlspecialchars($op['operation_date'] ?? '') ?>">
                                                <?= !empty($op['operation_date']) ? date('d.m.Y', strtotime($op['operation_date'])) : '-' ?>
                                            </td>
                                            <td><?= htmlspecialchars($op['operation_type_name'] ?? $op['sign_of_calculation'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($op['item_name'] ?? $op['property_name'] ?? '-') ?></td>
                                            <td>
                                                <?= htmlspecialchars($op['property_type_name'] ?? '-') ?>
                                                <?php if (isset($op['volume']) && $op['volume'] > 0): ?>
                                                    <span class="badge bg-info ms-1">Наливной</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary ms-1">Штучный</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($op['supplier_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($op['buyer_name'] ?? '-') ?></td>
                                            <td>
                                                <?php if (isset($op['volume']) && $op['volume'] > 0): ?>
                                                    <?= htmlspecialchars($op['volume']) ?> л
                                                <?php else: ?>
                                                    <?= htmlspecialchars($op['quantity'] ?? $op['count'] ?? '-') ?> <?= htmlspecialchars($op['unit'] ?? '') ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($op['total_cost'] ?? $op['summa'] ?? '-') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/warehouses/operations/view/<?= $op['id'] ?>" class="btn btn-outline-primary" title="Просмотр">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="/warehouses/operations/edit/<?= $op['id'] ?>" class="btn btn-outline-secondary" title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger delete-operation" data-id="<?= $op['id'] ?>" data-name="<?= htmlspecialchars($op['item_name'] ?? $op['property_name'] ?? '-') ?>" title="Удалить">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#operationsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ru.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Все"]],
        responsive: true,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [8] },
            { className: "text-center", targets: [8] }
        ]
    });
});
</script> 