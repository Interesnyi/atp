<?php /** @var array $operations, $suppliers, $buyers, $propertyTypes, $filters, $title, $warehouseTypes, $categories, $documentNumbers */ ?>
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
                    <div class="row mb-3">
                        <form class="col-md-12 d-flex align-items-center" method="get" action="/warehouses/operations" id="mainFilterForm">
                            <div class="me-2" style="min-width:200px;">
                                <select name="warehouse_type_id" class="form-select" id="warehouseTypeSelect">
                                    <option value="">Все типы складов</option>
                                    <?php foreach ($warehouseTypes as $wt): ?>
                                        <option value="<?= $wt['id'] ?>" <?= (($filters['warehouse_type_id'] ?? '') == $wt['id']) ? 'selected' : '' ?>><?= htmlspecialchars($wt['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex-grow-1 me-2">
                                <input type="text" name="search" class="form-control" placeholder="Поиск по имуществу..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" id="searchInput">
                            </div>
                            <div class="me-2" style="min-width:180px;">
                                <select name="operation_type" class="form-select" id="operationTypeSelect">
                                    <option value="">Тип операции</option>
                                    <option value="1" <?= ($filters['operation_type'] == 1) ? 'selected' : '' ?>>Приемка</option>
                                    <option value="2" <?= ($filters['operation_type'] == 2) ? 'selected' : '' ?>>Выдача</option>
                                    <option value="3" <?= ($filters['operation_type'] == 3) ? 'selected' : '' ?>>Списание</option>
                                    <option value="4" <?= ($filters['operation_type'] == 4) ? 'selected' : '' ?>>Перемещение</option>
                                    <option value="5" <?= ($filters['operation_type'] == 5) ? 'selected' : '' ?>>Розлив</option>
                                    <option value="6" <?= ($filters['operation_type'] == 6) ? 'selected' : '' ?>>Инвентаризация</option>
                                </select>
                            </div>
                            <div class="me-2" style="min-width:200px;">
                                <select name="buyer_id" class="form-select" id="buyerSelect">
                                    <option value="">Все получатели</option>
                                    <?php foreach ($buyers as $buyer): ?>
                                        <option value="<?= $buyer['id'] ?>" <?= (($filters['buyer_id'] ?? '') == $buyer['id']) ? 'selected' : '' ?>><?= htmlspecialchars($buyer['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="me-2" style="min-width:200px;">
                                <select name="document_number" class="form-select" id="documentNumberSelect">
                                    <option value="">Все документы</option>
                                    <?php foreach ($documentNumbers as $docNum): ?>
                                        <option value="<?= htmlspecialchars($docNum) ?>" <?= (($filters['document_number'] ?? '') == $docNum) ? 'selected' : '' ?>><?= htmlspecialchars($docNum) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="operationsTable" class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Дата</th>
                                    <th>Тип операции</th>
                                    <th>Имущество</th>
                                    <th>Поставщик</th>
                                    <th>Получатель</th>
                                    <th>Номер документа</th>
                                    <th>Количество</th>
                                    <th>Сумма</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($operations)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
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
                                            <td><?= htmlspecialchars($op['supplier_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($op['buyer_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($op['document_number'] ?? '-') ?></td>
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
<?php if (!empty($filters['document_number']) && !empty($operations)): ?>
    <?php
        $totalSum = 0;
        $dates = array_column($operations, 'operation_date');
        $minDate = min($dates);
        $maxDate = max($dates);
        foreach ($operations as $op) {
            $totalSum += floatval($op['total_cost'] ?? $op['summa'] ?? 0);
        }
    ?>
    <div class="card mt-3">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
            <div class="fw-bold fs-5">
                Документ: <?= htmlspecialchars($filters['document_number']) ?>
            </div>
            <div class="text-muted ms-3">
                <?= date('d.m.Y', strtotime($minDate)) ?> - <?= date('d.m.Y', strtotime($maxDate)) ?>
            </div>
            <div class="ms-3">
                <span class="badge bg-primary fs-6"><?= number_format($totalSum, 2, '.', ' ') ?> руб.</span>
            </div>
            <div class="ms-3">
                <span class="badge bg-secondary fs-6"><?= count($operations) ?> позиций</span>
            </div>
        </div>
    </div>
<?php endif; ?>
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

// Автоотправка формы при изменении типа склада или типа операции
$('#warehouseTypeSelect, #operationTypeSelect, #buyerSelect, #documentNumberSelect').on('change', function() {
    $('#mainFilterForm').submit();
});
// Автопоиск по имуществу с debounce
let searchTimeout = null;
$('#searchInput').on('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        $('#mainFilterForm').submit();
    }, 500);
});
</script> 