<?php
// @var array $item
// @var array $inventories
// @var array $operations
// @var array $warehousesById
?>
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/warehouses">Склады</a></li>
            <li class="breadcrumb-item"><a href="/warehouses/inventory">Остатки</a></li>
            <li class="breadcrumb-item active" aria-current="page">История имущества</li>
        </ol>
    </nav>
    <h4 class="mb-3"><?= htmlspecialchars($item['name']) ?> <small class="text-muted">(ID: <?= $item['id'] ?>)</small></h4>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Информация</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Артикул</dt>
                        <dd class="col-7"><?= htmlspecialchars($item['article'] ?? '-') ?></dd>
                        <dt class="col-5">Категория</dt>
                        <dd class="col-7"><?= htmlspecialchars($item['category_name'] ?? '-') ?></dd>
                        <dt class="col-5">Ед. изм.</dt>
                        <dd class="col-7"><?= htmlspecialchars($item['unit'] ?? '-') ?></dd>
                        <dt class="col-5">Описание</dt>
                        <dd class="col-7"><?= htmlspecialchars($item['description'] ?? '-') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Остатки по складам</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Склад</th>
                                <th>Тип склада</th>
                                <th>Количество</th>
                                <th>Дата обновления</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($inventories)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Нет остатков</td></tr>
                        <?php else: ?>
                            <?php foreach ($inventories as $inv): ?>
                                <tr>
                                    <td><?= htmlspecialchars($inv['warehouse_name'] ?? ($warehousesById[$inv['warehouse_id']]['name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($inv['warehouse_type_name'] ?? ($warehousesById[$inv['warehouse_id']]['warehouse_type_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($inv['quantity']) ?></td>
                                    <td><?= htmlspecialchars($inv['last_update'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">История операций</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Дата</th>
                            <th>Склад</th>
                            <th>Тип операции</th>
                            <th>Количество</th>
                            <th>Пользователь</th>
                            <th>Документ</th>
                            <th>Комментарий</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($operations)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Нет операций</td></tr>
                    <?php else: ?>
                        <?php foreach ($operations as $op): ?>
                            <tr>
                                <td><?= htmlspecialchars($op['operation_date'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($op['warehouse_name'] ?? ($warehousesById[$op['warehouse_id']]['name'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($op['operation_type_name'] ?? $op['operation_type_id']) ?></td>
                                <td><?= htmlspecialchars($op['quantity']) ?></td>
                                <td><?= htmlspecialchars($op['username'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($op['document_number'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($op['description'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 