<?php
// @var $cars array
?>
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/orders">Заказ-наряды</a></li>
            <li class="breadcrumb-item active" aria-current="page">Автомобили</li>
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
            <h4 class="mb-0">Автомобили</h4>
            <a href="/orders/cars/create" class="btn btn-primary"><i class="bi bi-plus"></i> Добавить авто</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Марка</th>
                            <th>Модель</th>
                            <th>Год</th>
                            <th>VIN</th>
                            <th>Госномер</th>
                            <th>Дата создания</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?= $car['id'] ?></td>
                            <td><?= htmlspecialchars($car['customer_name']) ?></td>
                            <td><?= htmlspecialchars($car['brand']) ?></td>
                            <td><?= htmlspecialchars($car['model']) ?></td>
                            <td><?= htmlspecialchars($car['year']) ?></td>
                            <td><?= htmlspecialchars($car['vin']) ?></td>
                            <td><?= htmlspecialchars($car['license_plate']) ?></td>
                            <td><?= $car['created_at'] ?></td>
                            <td>
                                <a href="/orders/cars/show/<?= $car['id'] ?>" class="btn btn-sm btn-outline-primary" title="Просмотр"><i class="bi bi-eye"></i></a>
                                <a href="/orders/cars/edit/<?= $car['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Редактировать"><i class="bi bi-pencil"></i></a>
                                <form action="/orders/cars/delete/<?= $car['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить автомобиль?');">
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