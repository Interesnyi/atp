<?php /** @var array $payments */ ?>
<?php /** @var array $totals */ ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Оплаты</h4>
                    <a href="/payments/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Новая оплата
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Дата</th>
                                    <th>Плательщик</th>
                                    <th>Получатель</th>
                                    <th>Сумма</th>
                                    <th>Счета</th>
                                    <th>Комментарий</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Оплаты не найдены</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= $payment['id'] ?></td>
                                            <td><?= !empty($payment['payment_date']) ? date('d.m.Y', strtotime($payment['payment_date'])) : '-' ?></td>
                                            <td><?= htmlspecialchars($payment['buyer_name']) ?></td>
                                            <td><?= htmlspecialchars($payment['legal_entity_name']) ?></td>
                                            <td><?= number_format($payment['amount'], 2, ',', ' ') ?></td>
                                            <td>
                                                <?php if (!empty($payment['invoice_numbers'])): ?>
                                                    <?= htmlspecialchars(implode(', ', $payment['invoice_numbers'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($payment['comment']) ?></td>
                                            <td>
                                                <a href="/payments/show/<?= $payment['id'] ?>" class="btn btn-outline-primary btn-sm" title="Просмотр"><i class="bi bi-eye"></i></a>
                                                <a href="/payments/edit/<?= $payment['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Редактировать"><i class="bi bi-pencil"></i></a>
                                                <form action="/payments/delete/<?= $payment['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить оплату?');">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Удалить"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($totals)): ?>
                        <div class="mt-4">
                            <h6>Итого по парам Получатель — Плательщик:</h6>
                            <ul class="mb-0">
                                <?php foreach ($totals as $pair => $sum): ?>
                                    <li><strong><?= htmlspecialchars($pair) ?>:</strong> <?= number_format($sum, 2, ',', ' ') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 