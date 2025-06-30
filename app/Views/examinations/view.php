<?php // @var $exam array ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/examinations">Экспертизы</a></li>
            <li class="breadcrumb-item active" aria-current="page">Просмотр</li>
        </ol>
    </nav>
    <h2>Экспертиза №<?= $exam['id'] ?></h2>
    <table class="table table-bordered w-auto">
        <tr><th>Дата</th><td><?= htmlspecialchars($exam['date']) ?></td></tr>
        <tr><th>Заказчик</th><td><?= htmlspecialchars($exam['company_name'] ?: $exam['contact_person']) ?></td></tr>
        <tr><th>Автомобиль</th><td><?= htmlspecialchars($exam['brand'] . ' ' . $exam['model'] . ' (' . $exam['year'] . ', ' . $exam['license_plate'] . ')') ?></td></tr>
        <tr><th>Договор</th><td><?php if (!empty($exam['contract_number'])): ?>№<?= htmlspecialchars($exam['contract_number']) ?> от <?= htmlspecialchars($exam['contract_date']) ?><?php else: ?>—<?php endif; ?></td></tr>
    </table>
    <a href="/examinations/edit/<?= $exam['id'] ?>" class="btn btn-warning">Редактировать</a>
    <a href="/examinations/download/<?= $exam['id'] ?>" class="btn btn-outline-primary">Скачать Word</a>
    <a href="/examinations" class="btn btn-secondary">Назад</a>
</div> 