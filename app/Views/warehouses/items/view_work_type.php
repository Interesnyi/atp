<?php // @var $work array ?>
<div class="container mt-4">
    <h2>Работа: <?= htmlspecialchars($work['name']) ?></h2>
    <table class="table table-bordered w-auto">
        <tr><th>Наименование</th><td><?= htmlspecialchars($work['name']) ?></td></tr>
        <tr><th>Код</th><td><?= htmlspecialchars($work['code']) ?></td></tr>
        <tr><th>Цена</th><td><?= htmlspecialchars($work['price']) ?></td></tr>
    </table>
    <a href="/orders/work_types/edit/<?= $work['id'] ?>" class="btn btn-warning">Редактировать</a>
    <a href="/orders/work_types" class="btn btn-secondary">Назад</a>
</div> 