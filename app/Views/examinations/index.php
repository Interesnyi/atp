<?php // @var $exams array ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Экспертизы</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Экспертизы</h2>
        <a href="/examinations/create" class="btn btn-success">+ Новая экспертиза</a>
    </div>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Заказчик</th>
                <th>Автомобиль</th>
                <th>Договор</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($exams as $exam): ?>
            <tr>
                <td><?= $exam['id'] ?></td>
                <td><?= htmlspecialchars($exam['date']) ?></td>
                <td><?= htmlspecialchars($exam['company_name'] ?: $exam['contact_person']) ?></td>
                <td><?= htmlspecialchars($exam['brand'] . ' ' . $exam['model'] . ' (' . $exam['year'] . ', ' . $exam['license_plate'] . ')') ?></td>
                <td><?php if (!empty($exam['contract_number'])): ?>№<?= htmlspecialchars($exam['contract_number']) ?> от <?= htmlspecialchars($exam['contract_date']) ?><?php else: ?>—<?php endif; ?></td>
                <td>
                    <a href="/examinations/show/<?= $exam['id'] ?>" class="btn btn-outline-primary btn-sm" title="Просмотр"><i class="bi bi-eye"></i></a>
                    <a href="/examinations/edit/<?= $exam['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Редактировать"><i class="bi bi-pencil"></i></a>
                    <form action="/examinations/delete/<?= $exam['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить экспертизу?');">
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Удалить"><i class="bi bi-trash"></i></button>
                    </form>
                    <a href="/examinations/download/<?= $exam['id'] ?>" class="btn btn-outline-success btn-sm" title="Скачать Word"><i class="bi bi-download"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div> 