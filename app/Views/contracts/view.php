<?php // @var $contract array ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/contracts">Договоры</a></li>
            <li class="breadcrumb-item active" aria-current="page">Просмотр</li>
        </ol>
    </nav>
    <h2>Договор №<?= htmlspecialchars($contract['contract_number']) ?></h2>
    <table class="table table-bordered w-auto">
        <tr><th>Дата</th><td><?= htmlspecialchars($contract['contract_date']) ?></td></tr>
        <tr><th>Заказчик</th><td><?= htmlspecialchars($contract['company_name'] ?: $contract['contact_person']) ?></td></tr>
        <tr><th>Контактное лицо (род. падеж)</th><td><?= htmlspecialchars($contract['contact_person_genitive']) ?></td></tr>
        <tr><th>Описание</th><td><?= nl2br(htmlspecialchars($contract['description'])) ?></td></tr>
        <tr><th>Файл договора</th><td><?php if (!empty($contract['contract_file'])): ?><a href="<?= htmlspecialchars($contract['contract_file']) ?>" target="_blank">Скачать</a><?php else: ?>—<?php endif; ?></td></tr>
    </table>
    <a href="/contracts/edit/<?= $contract['id'] ?>" class="btn btn-warning">Редактировать</a>
    <a href="/contracts/download/<?= $contract['id'] ?>" class="btn btn-outline-primary">Скачать Word</a>
    <a href="/contracts" class="btn btn-secondary">Назад</a>
</div> 