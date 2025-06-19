<?php /** @var array $invoice, $items, $files */ ?>
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/invoices">Счета</a></li>
            <li class="breadcrumb-item active" aria-current="page">Счёт №<?= htmlspecialchars($invoice['number']) ?></li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Счёт №<?= htmlspecialchars($invoice['number']) ?> от <?= date('d.m.Y', strtotime($invoice['date'])) ?></h4>
                    <div>
                        <a href="/invoices/edit/<?= $invoice['id'] ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Редактировать</a>
                        <form action="/invoices/delete/<?= $invoice['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить счёт?');">
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Удалить</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="mb-2"><b>Юр. лицо:</b> <?= htmlspecialchars($invoice['legal_entity_name']) ?></div>
                            <div class="mb-2"><b>Комментарий:</b> <?= htmlspecialchars($invoice['comment']) ?></div>
                        </div>
                        <div class="col-md-4">
                            <b>Статусы:</b><br>
                            <span class="badge bg-<?= $invoice['status_issued'] ? 'primary' : 'secondary' ?>">Выставлен</span>
                            <?php if (!empty($invoice['date_issued'])): ?>
                                <span class="text-muted small ms-1">(<?= date('d.m.Y', strtotime($invoice['date_issued'])) ?>)</span>
                            <?php endif; ?>
                            <br>
                            <span class="badge bg-<?= $invoice['status_shipped'] ? 'info' : 'secondary' ?>">Отгружен</span>
                            <?php if (!empty($invoice['date_shipped'])): ?>
                                <span class="text-muted small ms-1">(<?= date('d.m.Y', strtotime($invoice['date_shipped'])) ?>)</span>
                            <?php endif; ?>
                            <br>
                            <span class="badge bg-<?= $invoice['status_paid'] ? 'success' : 'secondary' ?>">Оплачен</span>
                            <?php if (!empty($invoice['date_paid'])): ?>
                                <span class="text-muted small ms-1">(<?= date('d.m.Y', strtotime($invoice['date_paid'])) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h5 class="mt-4 mb-3">Позиции по счёту</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Дата выдачи</th>
                                    <th>Имущество</th>
                                    <th>Количество</th>
                                    <th>Цена</th>
                                    <th>Сумма</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Нет позиций</td></tr>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= date('d.m.Y', strtotime($item['operation_date'])) ?></td>
                                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                                            <td><?php
                                                $price = ($item['quantity'] > 0) ? ($item['total_cost'] / $item['quantity']) : 0;
                                                echo number_format($price, 2, '.', ' ');
                                            ?></td>
                                            <td><?= number_format($item['total_cost'], 2, '.', ' ') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($items)): ?>
                        <div class="mb-2">
                            <b>Итого:</b> <?= count($items) ?> наименований, на сумму <?= number_format(array_sum(array_column($items, 'total_cost')), 2, '.', ' ') ?> руб.
                        </div>
                    <?php endif; ?>
                    <h5 class="mt-4 mb-3">Файлы по счёту</h5>
                    <ul class="list-group mb-3">
                        <?php if (empty($files)): ?>
                            <li class="list-group-item text-muted">Нет файлов</li>
                        <?php else: ?>
                            <?php foreach ($files as $file): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="/uploads/<?= htmlspecialchars($file['file_path']) ?>" target="_blank">
                                        <i class="bi bi-paperclip"></i> <?= htmlspecialchars($file['file_path']) ?>
                                    </a>
                                    <span class="text-muted small"><?= date('d.m.Y H:i', strtotime($file['uploaded_at'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div> 