<?php include_once __DIR__ . '/../../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6">
                            <h4>Материальный склад: <?= htmlspecialchars($warehouse['name']) ?></h4>
                            <p class="text-sm mb-0">
                                <i class="fa fa-warehouse text-primary" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">Управление материальными ценностями</span>
                            </p>
                            <?php if (!empty($warehouse['location'])): ?>
                                <p class="text-xs text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($warehouse['location']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-6 text-end d-flex flex-column justify-content-end">
                            <div class="btn-group mb-2">
                                <a href="/maslosklad/material/<?= $warehouse['id'] ?>/reception" class="btn btn-sm bg-gradient-success">
                                    <i class="fas fa-plus-circle me-1"></i> Приёмка
                                </a>
                                <a href="/maslosklad/material/<?= $warehouse['id'] ?>/issue" class="btn btn-sm bg-gradient-warning">
                                    <i class="fas fa-minus-circle me-1"></i> Выдача
                                </a>
                                <a href="/maslosklad/material/<?= $warehouse['id'] ?>/writeoff" class="btn btn-sm bg-gradient-danger">
                                    <i class="fas fa-trash-alt me-1"></i> Списание
                                </a>
                                <?php if (isset($userPermissions['maslosklad.manage'])): ?>
                                <a href="/maslosklad/material/<?= $warehouse['id'] ?>/inventory" class="btn btn-sm bg-gradient-info">
                                    <i class="fas fa-clipboard-check me-1"></i> Инвентаризация
                                </a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="/maslosklad" class="btn btn-sm bg-gradient-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> К списку складов
                                </a>
                                <button type="button" class="btn btn-sm bg-gradient-dark" id="btnPrint">
                                    <i class="fas fa-print me-1"></i> Печать
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="p-3">
                        <?php if (empty($categories)): ?>
                            <div class="alert alert-info">
                                <strong>Внимание!</strong> Для этого склада не созданы категории товаров.
                                <?php if (isset($userPermissions['maslosklad.manage'])): ?>
                                    <a href="/maslosklad/categories" class="alert-link">Создать категории</a>
                                <?php endif; ?>
                            </div>
                        <?php elseif (empty($inventoryByCategory)): ?>
                            <div class="alert alert-info">
                                <strong>Склад пуст!</strong> На этом складе нет товаров.
                                <a href="/maslosklad/material/<?= $warehouse['id'] ?>/reception" class="alert-link">Оформить приёмку</a>
                            </div>
                        <?php else: ?>
                            <!-- Поиск по товарам -->
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Поиск товара...">
                                </div>
                            </div>

                            <!-- Вкладки категорий -->
                            <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#all" role="tab">
                                        Все товары
                                    </a>
                                </li>
                                <?php foreach ($categories as $category): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#category-<?= $category['id'] ?>" role="tab">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <!-- Содержимое вкладок -->
                            <div class="tab-content">
                                <!-- Вкладка "Все товары" -->
                                <div class="tab-pane fade show active" id="all" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Наименование</th>
                                                    <th>Категория</th>
                                                    <th>Артикул</th>
                                                    <th class="text-center">Количество</th>
                                                    <th>Единица</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="inventory-items">
                                                <?php 
                                                $totalItems = 0;
                                                foreach ($inventoryByCategory as $categoryItems): 
                                                    foreach ($categoryItems as $item):
                                                        $totalItems++;
                                                ?>
                                                    <tr class="item-row" data-search="<?= htmlspecialchars(mb_strtolower($item['item_name'] . ' ' . $item['item_article'] . ' ' . $item['category_name'])) ?>">
                                                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                                                        <td><?= htmlspecialchars($item['category_name']) ?></td>
                                                        <td><?= htmlspecialchars($item['item_article'] ?? '-') ?></td>
                                                        <td class="text-center">
                                                            <span class="<?= ($item['quantity'] <= 0) ? 'text-danger' : '' ?>">
                                                                <?= number_format($item['quantity'], 2, '.', ' ') ?>
                                                            </span>
                                                        </td>
                                                        <td><?= htmlspecialchars($item['unit'] ?? 'шт') ?></td>
                                                        <td>
                                                            <a href="/maslosklad/material/<?= $warehouse['id'] ?>/item/<?= $item['item_id'] ?>" class="btn btn-sm btn-info">
                                                                <i class="fas fa-info-circle"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php 
                                                    endforeach;
                                                endforeach; 
                                                ?>
                                                <?php if ($totalItems === 0): ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center py-3">
                                                            <i class="fas fa-box-open fa-2x text-muted"></i>
                                                            <p class="text-muted mt-2">На складе нет товаров</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Вкладки по категориям -->
                                <?php foreach ($categories as $category): ?>
                                    <div class="tab-pane fade" id="category-<?= $category['id'] ?>" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Наименование</th>
                                                        <th>Артикул</th>
                                                        <th class="text-center">Количество</th>
                                                        <th>Единица</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $hasItems = false;
                                                    if (isset($inventoryByCategory[$category['id']])): 
                                                        foreach ($inventoryByCategory[$category['id']] as $item):
                                                            $hasItems = true;
                                                    ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                                                            <td><?= htmlspecialchars($item['item_article'] ?? '-') ?></td>
                                                            <td class="text-center">
                                                                <span class="<?= ($item['quantity'] <= 0) ? 'text-danger' : '' ?>">
                                                                    <?= number_format($item['quantity'], 2, '.', ' ') ?>
                                                                </span>
                                                            </td>
                                                            <td><?= htmlspecialchars($item['unit'] ?? 'шт') ?></td>
                                                            <td>
                                                                <a href="/maslosklad/material/<?= $warehouse['id'] ?>/item/<?= $item['item_id'] ?>" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-info-circle"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                        endforeach;
                                                    endif;
                                                    
                                                    if (!$hasItems): 
                                                    ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center py-3">
                                                                <i class="fas fa-box-open fa-2x text-muted"></i>
                                                                <p class="text-muted mt-2">В этой категории нет товаров</p>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../layout/footer.php'; ?>

<script>
$(document).ready(function() {
    // Функция поиска товаров
    $('#searchInput').on('keyup', function() {
        let searchText = $(this).val().toLowerCase();
        $('.inventory-items .item-row').each(function() {
            let itemText = $(this).data('search');
            if (itemText.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Печать инвентаря
    $('#btnPrint').on('click', function() {
        window.print();
    });
});
</script>

<style media="print">
    /* Стили для печати */
    .navbar, .sidenav, .footer, .card-header, .nav-tabs, .btn {
        display: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .tab-pane {
        display: block !important;
        opacity: 1 !important;
    }
    
    h4 {
        font-size: 18px !important;
        margin-bottom: 15px !important;
    }
    
    table {
        width: 100% !important;
    }
    
    @page {
        size: landscape;
    }
</style> 