<?php // @var $acts array ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Акты осмотра</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Акты осмотра</h2>
        <a href="/inspection-acts/create" class="btn btn-success">+ Новый акт</a>
    </div>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Заказчик</th>
                <th>Автомобиль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($acts as $act): ?>
            <tr>
                <td><?= $act['id'] ?></td>
                <td><?= htmlspecialchars($act['date']) ?></td>
                <td><?= htmlspecialchars($act['company_name'] ?: $act['contact_person']) ?></td>
                <td><?= htmlspecialchars($act['brand'] . ' ' . $act['model'] . ' (' . $act['year'] . ', ' . $act['license_plate'] . ')') ?></td>
                <td>
                    <a href="/inspection-acts/show/<?= $act['id'] ?>" class="btn btn-outline-primary btn-sm" title="Просмотр"><i class="bi bi-eye"></i></a>
                    <a href="/inspection-acts/edit/<?= $act['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Редактировать"><i class="bi bi-pencil"></i></a>
                    <form action="/inspection-acts/delete/<?= $act['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить акт?');">
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Удалить"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div> 