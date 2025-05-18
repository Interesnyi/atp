<?php include_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-6">
                            <h4>Система управления складами</h4>
                            <p class="text-sm mb-0">
                                <i class="fa fa-warehouse text-info" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">Выберите склад для работы</span>
                            </p>
                        </div>
                        <?php if (isset($userPermissions['maslosklad.manage'])): ?>
                        <div class="col-6 text-end">
                            <a href="/maslosklad/warehouses" class="btn btn-sm bg-gradient-primary">
                                <i class="fas fa-cog me-2"></i> Управление складами
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <?php if (empty($warehousesByType)): ?>
                        <div class="p-4 text-center">
                            <h4 class="text-muted">Нет доступных складов</h4>
                            <?php if (isset($userPermissions['maslosklad.manage'])): ?>
                                <p>Для начала работы необходимо создать склады</p>
                                <a href="/maslosklad/warehouses" class="btn btn-primary">Создать склады</a>
                            <?php else: ?>
                                <p>Обратитесь к администратору для создания складов</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="px-4">
                            <?php foreach ($warehouseTypes as $warehouseType): ?>
                                <?php if (isset($warehousesByType[$warehouseType['id']])): ?>
                                    <h5 class="mt-4 mb-3"><?= htmlspecialchars($warehouseType['name']) ?></h5>
                                    <div class="row">
                                        <?php foreach ($warehousesByType[$warehouseType['id']] as $warehouse): ?>
                                            <div class="col-md-4 col-sm-6 mb-4">
                                                <div class="card h-100">
                                                    <div class="card-header bg-gradient-<?= $this->getWarehouseTypeColor($warehouseType['code']) ?> text-white">
                                                        <h5 class="mb-0"><?= htmlspecialchars($warehouse['name']) ?></h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php if (!empty($warehouse['description'])): ?>
                                                            <p class="text-sm"><?= htmlspecialchars($warehouse['description']) ?></p>
                                                        <?php endif; ?>
                                                        <?php if (!empty($warehouse['location'])): ?>
                                                            <p class="text-muted mb-0">
                                                                <i class="fas fa-map-marker-alt me-2"></i> <?= htmlspecialchars($warehouse['location']) ?>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="card-footer">
                                                        <a href="/maslosklad/warehouse/<?= $warehouse['id'] ?>" class="btn btn-sm btn-primary w-100">
                                                            <i class="fas fa-arrow-right me-1"></i> Перейти к складу
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>

<script>
$(document).ready(function() {
    // Инициализация всплывающих подсказок
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script> 