<?php /** @var array $payment */
/** @var array $buyer */
/** @var array $legalEntity */
/** @var array $invoices */ ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Просмотр оплаты</h5>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">ID</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($payment['id']) ?></dd>

            <dt class="col-sm-3">Дата оплаты</dt>
            <dd class="col-sm-9"><?= !empty($payment['payment_date']) ? date('d.m.Y', strtotime($payment['payment_date'])) : '-' ?></dd>

            <dt class="col-sm-3">Плательщик</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($buyer['name'] ?? '') ?></dd>

            <dt class="col-sm-3">Получатель (Юр.лицо)</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($legalEntity['name'] ?? '') ?></dd>

            <dt class="col-sm-3">Сумма</dt>
            <dd class="col-sm-9"><?= number_format($payment['amount'], 2, ',', ' ') ?></dd>

            <dt class="col-sm-3">Комментарий</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($payment['comment']) ?></dd>

            <dt class="col-sm-3">Привязанные счета</dt>
            <dd class="col-sm-9">
                <?php if (empty($invoices)): ?>
                    <span class="text-muted">Нет</span>
                <?php else: ?>
                    <ul class="mb-0">
                        <?php foreach ($invoices as $invoice): ?>
                            <li>
                                Счет №<?= htmlspecialchars($invoice['number'] ?? '') ?>
                                от <?= !empty($invoice['date']) ? date('d.m.Y', strtotime($invoice['date'])) : '-' ?>
                                (<?= number_format($invoice['total_amount'] ?? 0, 2, ',', ' ') ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </dd>
        </dl>
        <a href="/payments/edit/<?= $payment['id'] ?>" class="btn btn-primary">Редактировать</a>
        <a href="/payments" class="btn btn-secondary">Назад к списку</a>
    </div>
</div> 