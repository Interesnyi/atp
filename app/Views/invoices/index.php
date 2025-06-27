<?php /** @var array $invoices, $filters */ ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Счета</h4>
                    <div>
                        <a href="/legal-entities" class="btn btn-outline-secondary btn-sm me-2">
                            <i class="bi bi-building"></i> Юр. лица
                        </a>
                        <a href="/invoices/create" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus"></i> Новый счёт
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>№</th>
                                    <th>Дата</th>
                                    <th>Юр. лицо</th>
                                    <th>Статус</th>
                                    <th>Комментарий</th>
                                    <th>Сумма</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($invoices)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Счета не найдены</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($invoices as $inv): ?>
                                        <tr>
                                            <td><a href="/invoices/show/<?= $inv['id'] ?>"><?= htmlspecialchars($inv['number']) ?></a></td>
                                            <td><?= date('d.m.Y', strtotime($inv['date'])) ?></td>
                                            <td><?= htmlspecialchars($inv['legal_entity_name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $inv['status_issued'] ? 'primary' : 'secondary' ?>">Выставлен<?php if (!empty($inv['date_issued'])): ?> (<?= date('d.m.Y', strtotime($inv['date_issued'])) ?>)<?php endif; ?></span>
                                                <span class="badge bg-<?= $inv['status_shipped'] ? 'info' : 'secondary' ?>">Отгружен<?php if (!empty($inv['date_shipped'])): ?> (<?= date('d.m.Y', strtotime($inv['date_shipped'])) ?>)<?php endif; ?></span>
                                                <span class="badge bg-<?= $inv['status_paid'] ? 'success' : 'secondary' ?>">Оплачен<?php if (!empty($inv['date_paid'])): ?> (<?= date('d.m.Y', strtotime($inv['date_paid'])) ?>)<?php endif; ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($inv['comment']) ?></td>
                                            <td>
                                                <?php if (!empty($inv['manual_amount'])): ?>
                                                    <span title="Фактическая сумма / Сумма в счёте">
                                                        <?= number_format($inv['total_amount'], 2, '.', ' ') ?>
                                                        <span class="text-muted">/</span>
                                                        <b><?= number_format($inv['manual_amount'], 2, '.', ' ') ?></b>
                                                    </span>
                                                <?php else: ?>
                                                    <?= number_format($inv['total_amount'], 2, '.', ' ') ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="/invoices/show/<?= $inv['id'] ?>" class="btn btn-outline-primary btn-sm" title="Просмотр"><i class="bi bi-eye"></i></a>
                                                <a href="/invoices/edit/<?= $inv['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Редактировать"><i class="bi bi-pencil"></i></a>
                                                <form action="/invoices/delete/<?= $inv['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить счёт?');">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Удалить"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 