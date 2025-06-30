<?php // @var $contracts array ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Договоры</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Договоры</h2>
        <a href="/contracts/create" class="btn btn-success">+ Новый договор</a>
    </div>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Номер</th>
                <th>Дата</th>
                <th>Заказчик</th>
                <th>Описание</th>
                <th>Файл</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($contracts as $contract): ?>
            <tr>
                <td><?= $contract['id'] ?></td>
                <td><?= htmlspecialchars($contract['contract_number']) ?></td>
                <td><?= htmlspecialchars($contract['contract_date']) ?></td>
                <td><?= htmlspecialchars($contract['company_name'] ?: $contract['contact_person']) ?></td>
                <td><?= htmlspecialchars($contract['description']) ?></td>
                <td>
                    <?php if (!empty($contract['contract_file'])): ?>
                        <a href="<?= htmlspecialchars($contract['contract_file']) ?>" target="_blank">Скачать</a>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                    <a href="/contracts/show/<?= $contract['id'] ?>" class="btn btn-outline-primary btn-sm" title="Просмотр"><i class="bi bi-eye"></i></a>
                    <a href="/contracts/edit/<?= $contract['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Редактировать"><i class="bi bi-pencil"></i></a>
                    <form action="/contracts/delete/<?= $contract['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить договор?');">
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Удалить"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div> 