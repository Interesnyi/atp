<?php
// @var $workTypes array
?>
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/orders">Заказ-наряды</a></li>
            <li class="breadcrumb-item active" aria-current="page">Работы</li>
        </ol>
    </nav>
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
    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Справочник работ</h4>
            <div>
                <a href="/orders/work_categories" class="btn btn-outline-secondary me-2">Категории</a>
                <a href="/orders/work_types/create" class="btn btn-primary">Добавить работу</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Наименование</th>
                            <th>Категория</th>
                            <th>Код</th>
                            <th>Цена</th>
                            <th>Дата создания</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($workTypes as $work): ?>
                        <tr>
                            <td><?= $work['id'] ?></td>
                            <td><?= htmlspecialchars($work['name']) ?></td>
                            <td><?= htmlspecialchars($work['category_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($work['code']) ?></td>
                            <td><?= $work['price'] ?></td>
                            <td><?= $work['created_at'] ?></td>
                            <td>
                                <a href="/orders/work_types/show/<?= $work['id'] ?>" class="btn btn-sm btn-outline-primary" title="Просмотр"><i class="bi bi-eye"></i></a>
                                <a href="/orders/work_types/edit/<?= $work['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Редактировать"><i class="bi bi-pencil"></i></a>
                                <form action="/orders/work_types/delete/<?= $work['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить работу?');">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 