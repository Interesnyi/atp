<?php /** @var array $payment */
/** @var array $buyers */
/** @var array $legalEntities */
/** @var array $invoices */
/** @var array $selectedInvoices */ ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Редактировать оплату</h5>
    </div>
    <div class="card-body">
        <form method="post" action="/payments/update/<?= $payment['id'] ?>">
            <div class="mb-3">
                <label for="payment_date" class="form-label">Дата оплаты</label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?= htmlspecialchars($payment['payment_date']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="buyer_id" class="form-label">Плательщик (Покупатель)</label>
                <select class="form-select" id="buyer_id" name="buyer_id" required>
                    <option value="">Выберите покупателя</option>
                    <?php foreach ($buyers as $buyer): ?>
                        <option value="<?= $buyer['id'] ?? $buyer->id ?>" <?= ($payment['buyer_id'] == ($buyer['id'] ?? $buyer->id)) ? 'selected' : '' ?>><?= htmlspecialchars($buyer['name'] ?? $buyer->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="legal_entity_id" class="form-label">Получатель (Юр.лицо)</label>
                <select class="form-select" id="legal_entity_id" name="legal_entity_id" required>
                    <option value="">Выберите юр.лицо</option>
                    <?php foreach ($legalEntities as $entity): ?>
                        <option value="<?= $entity['id'] ?? $entity->id ?>" <?= ($payment['legal_entity_id'] == ($entity['id'] ?? $entity->id)) ? 'selected' : '' ?>><?= htmlspecialchars($entity['name'] ?? $entity->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Сумма</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?= htmlspecialchars($payment['amount']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="invoice_ids" class="form-label">Привязанные счета</label>
                <select class="form-select" id="invoice_ids" name="invoice_ids[]" multiple>
                    <?php foreach ($invoices as $invoice): ?>
                        <option value="<?= $invoice['id'] ?? $invoice->id ?>" <?= in_array(($invoice['id'] ?? $invoice->id), $selectedInvoices) ? 'selected' : '' ?>>
                            Счет №<?= htmlspecialchars($invoice['number'] ?? '') ?>
                            от <?= !empty($invoice['date']) ? date('d.m.Y', strtotime($invoice['date'])) : '-' ?>
                            (<?= number_format($invoice['total_amount'] ?? 0, 2, ',', ' ') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Можно выбрать несколько счетов</div>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Комментарий</label>
                <textarea class="form-control" id="comment" name="comment" rows="2"><?= htmlspecialchars($payment['comment']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/payments" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div> 