<?php include_once __DIR__ . '/../../layouts/default_header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Боковое меню навигации -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Материальный склад</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="/maslosklad/material/<?= $warehouse['id'] ?>" class="list-group-item list-group-item-action <?= !isset($activeSection) || $activeSection == 'overview' ? 'active' : '' ?>">
                            <i class="fas fa-home me-2"></i> Обзор
                        </a>
                        <a href="/maslosklad/material/<?= $warehouse['id'] ?>/inventory" class="list-group-item list-group-item-action <?= isset($activeSection) && $activeSection == 'inventory' ? 'active' : '' ?>">
                            <i class="fas fa-boxes me-2"></i> Инвентарь
                        </a>
                        <a href="/maslosklad/material/<?= $warehouse['id'] ?>/reception" class="list-group-item list-group-item-action <?= isset($activeSection) && $activeSection == 'reception' ? 'active' : '' ?>">
                            <i class="fas fa-dolly-flatbed me-2"></i> Приёмка
                        </a>
                        <a href="/maslosklad/material/<?= $warehouse['id'] ?>/issue" class="list-group-item list-group-item-action <?= isset($activeSection) && $activeSection == 'issue' ? 'active' : '' ?>">
                            <i class="fas fa-shipping-fast me-2"></i> Выдача
                        </a>
                        <a href="/maslosklad/material/<?= $warehouse['id'] ?>/writeoff" class="list-group-item list-group-item-action <?= isset($activeSection) && $activeSection == 'writeoff' ? 'active' : '' ?>">
                            <i class="fas fa-trash-alt me-2"></i> Списание
                        </a>
                        <a href="/maslosklad/material/<?= $warehouse['id'] ?>/operations" class="list-group-item list-group-item-action <?= isset($activeSection) && $activeSection == 'operations' ? 'active' : '' ?>">
                            <i class="fas fa-history me-2"></i> Журнал операций
                        </a>
                        <a href="/maslosklad/material/<?= $warehouse['id'] ?>/reports" class="list-group-item list-group-item-action <?= isset($activeSection) && $activeSection == 'reports' ? 'active' : '' ?>">
                            <i class="fas fa-chart-bar me-2"></i> Отчеты
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Информация о складе</h5>
                </div>
                <div class="card-body">
                    <h6><?= htmlspecialchars($warehouse['name']) ?></h6>
                    <p class="text-muted">
                        <i class="fas fa-map-marker-alt me-2"></i> <?= htmlspecialchars($warehouse['location']) ?>
                    </p>
                    <p><?= htmlspecialchars($warehouse['description']) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Основной контент -->
        <div class="col-md-9">
            <?php if (!isset($activeSection) || $activeSection == 'overview'): ?>
                <!-- Обзор склада -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Обзор склада</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3><?= isset($totalItems) ? $totalItems : '0' ?></h3>
                                        <p class="mb-0">Наименований</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3><?= isset($totalReceptions) ? $totalReceptions : '0' ?></h3>
                                        <p class="mb-0">Приёмок</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3><?= isset($totalIssues) ? $totalIssues : '0' ?></h3>
                                        <p class="mb-0">Выдач</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3><?= isset($totalWriteoffs) ? $totalWriteoffs : '0' ?></h3>
                                        <p class="mb-0">Списаний</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Последние операции -->
                        <h5 class="mt-4 mb-3">Последние операции</h5>
                        <?php if (isset($recentOperations) && !empty($recentOperations)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th>Тип</th>
                                            <th>Товар</th>
                                            <th>Количество</th>
                                            <th>Пользователь</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOperations as $operation): ?>
                                            <tr>
                                                <td><?= date('d.m.Y H:i', strtotime($operation['created_at'])) ?></td>
                                                <td>
                                                    <?php if ($operation['type'] == 'reception'): ?>
                                                        <span class="badge bg-success">Приёмка</span>
                                                    <?php elseif ($operation['type'] == 'issue'): ?>
                                                        <span class="badge bg-info">Выдача</span>
                                                    <?php elseif ($operation['type'] == 'writeoff'): ?>
                                                        <span class="badge bg-warning">Списание</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($operation['item_name']) ?></td>
                                                <td><?= $operation['quantity'] ?> <?= htmlspecialchars($operation['unit']) ?></td>
                                                <td><?= htmlspecialchars($operation['username']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Нет недавних операций</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php
            // Здесь будет подключаться соответствующий шаблон в зависимости от активного раздела
            if (isset($content)) {
                echo $content;
            }
            ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/default_footer.php'; ?>

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