<?= $content ?? '' ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Система управления складами</h4>
                            <p class="small mb-0">
                                <i class="fas fa-warehouse me-2"></i>
                                <span>Выберите склад для работы</span>
                            </p>
                        </div>
                        <?php if (isset($userPermissions['maslosklad.manage'])): ?>
                        <div class="col-md-6 text-end">
                            <a href="/warehouses/manage" class="btn btn-sm btn-light">
                                <i class="fas fa-cog me-2"></i> Управление складами
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Статичные плитки типов складов -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-gradient-primary text-white">
                                    <h5 class="mb-0 text-dark">Материальный склад</h5>
                                </div>
                                <div class="card-body">
                                    <p>Хранение и учет материалов, запасных частей и комплектующих.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/material" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-gradient-success text-white">
                                    <h5 class="mb-0 text-dark">Инструментальный склад</h5>
                                </div>
                                <div class="card-body">
                                    <p>Хранение и учет инструментов, приспособлений и оборудования.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/tool" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-gradient-warning text-white">
                                    <h5 class="mb-0 text-dark">Склад ГСМ</h5>
                                </div>
                                <div class="card-body">
                                    <p>Хранение и учет горюче-смазочных материалов.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/oil" class="btn btn-warning btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-gradient-info text-white">
                                    <h5 class="mb-0 text-dark">Склад автозапчастей</h5>
                                </div>
                                <div class="card-body">
                                    <p>Хранение и учет запасных частей и комплектующих для автотехники.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/autoparts" class="btn btn-info btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Общие разделы для работы с системой -->
                    <h5 class="border-bottom pb-2 mb-3">Общие разделы</h5>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-box-open me-2"></i> Имущество</h5>
                                </div>
                                <div class="card-body">
                                    <p>Управление имуществом склада. Добавление, редактирование, удаление.</p>
                                    <form method="get" action="/warehouses/items" class="mb-2" id="itemTypeForm">
                                        <div class="input-group">
                                            <select name="warehouse_type_id" class="form-select" id="warehouseTypeSelect">
                                                <option value="">Все типы складов</option>
                                                <?php foreach ($warehouseTypes as $type): ?>
                                                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="button" class="btn btn-outline-primary" id="showItemsBtn">Показать</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/items" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-history me-2"></i> Операции</h5>
                                </div>
                                <div class="card-body">
                                    <p>Журнал операций по всем складам. История изменений.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/operations" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i> Поставщики</h5>
                                </div>
                                <div class="card-body">
                                    <p>Управление поставщиками и контрагентами. Контактная информация.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/suppliers" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Получатели</h5>
                                </div>
                                <div class="card-body">
                                    <p>Управление получателями и подразделениями. Данные о выдачах.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/buyers" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Отчеты</h5>
                                </div>
                                <div class="card-body">
                                    <p>Формирование и просмотр отчетов по движению товаров и материалов.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/reports" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Статистика</h5>
                                </div>
                                <div class="card-body">
                                    <p>Аналитика и статистические данные по работе складов и имуществу.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/statistics" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i> Остатки</h5>
                                </div>
                                <div class="card-body">
                                    <p>Просмотр текущих остатков по складам, категориям и товарам.</p>
                                </div>
                                <div class="card-footer bg-light">
                                    <a href="/warehouses/inventory" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i> Перейти
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Инициализация всплывающих подсказок
    $('[data-bs-toggle="tooltip"]').tooltip();

    document.getElementById('showItemsBtn').onclick = function() {
        var select = document.getElementById('warehouseTypeSelect');
        var val = select.value;
        if (val) {
            window.location.href = '/warehouses/items?warehouse_type_id=' + encodeURIComponent(val);
        } else {
            window.location.href = '/warehouses/items';
        }
    };
});
</script> 