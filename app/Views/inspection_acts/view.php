<?php // @var $act array ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/inspection-acts">Акты осмотра</a></li>
            <li class="breadcrumb-item active" aria-current="page">Просмотр</li>
        </ol>
    </nav>
    <h2>Акт осмотра №<?= $act['id'] ?></h2>
    <table class="table table-bordered w-auto">
        <tr><th>Дата</th><td><?= htmlspecialchars($act['date']) ?></td></tr>
        <tr><th>Заказчик</th><td><?= htmlspecialchars($act['company_name'] ?: $act['contact_person']) ?></td></tr>
        <tr><th>Автомобиль</th><td><?= htmlspecialchars($act['brand'] . ' ' . $act['model'] . ' (' . $act['year'] . ', ' . $act['license_plate'] . ')') ?></td></tr>
        <tr><th>Описание ТС</th><td><?= nl2br(htmlspecialchars($act['description'])) ?></td></tr>
        <tr><th>Внешние видимые повреждения</th><td><?= nl2br(htmlspecialchars($act['damages'])) ?></td></tr>
        <tr><th>Выводы</th><td><?= nl2br(htmlspecialchars($act['conclusion'])) ?></td></tr>
    </table>
    <a href="/inspection-acts/edit/<?= $act['id'] ?>" class="btn btn-warning">Редактировать</a>
    <a href="/inspection-acts/download/<?= $act['id'] ?>" class="btn btn-outline-primary">Скачать Word</a>
    <a href="/inspection-acts" class="btn btn-secondary">Назад</a>
</div> 