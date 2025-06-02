<?php // @var $car array ?>
<div class="container mt-4">
    <h2>Автомобиль: <?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h2>
    <table class="table table-bordered w-auto">
        <tr><th>Клиент</th><td><?= htmlspecialchars($car['customer_name']) ?></td></tr>
        <tr><th>Марка</th><td><?= htmlspecialchars($car['brand']) ?></td></tr>
        <tr><th>Модель</th><td><?= htmlspecialchars($car['model']) ?></td></tr>
        <tr><th>Год</th><td><?= htmlspecialchars($car['year']) ?></td></tr>
        <tr><th>VIN</th><td><?= htmlspecialchars($car['vin']) ?></td></tr>
        <tr><th>Госномер</th><td><?= htmlspecialchars($car['license_plate']) ?></td></tr>
        <tr><th>Кузов</th><td><?= htmlspecialchars($car['body_number']) ?></td></tr>
        <tr><th>Двигатель</th><td><?= htmlspecialchars($car['engine_number']) ?></td></tr>
        <tr><th>Комментарий</th><td><?= htmlspecialchars($car['comment']) ?></td></tr>
    </table>
    <a href="/orders/cars/edit/<?= $car['id'] ?>" class="btn btn-warning">Редактировать</a>
    <a href="/orders/cars" class="btn btn-secondary">Назад</a>
</div> 