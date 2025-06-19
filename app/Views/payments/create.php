<?php /** @var array $buyers */
/** @var array $legalEntities */
/** @var array $invoices */ ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Новая оплата</h5>
    </div>
    <div class="card-body">
        <form method="post" action="/payments/store">
            <div class="mb-3">
                <label for="payment_date" class="form-label">Дата оплаты</label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" required>
            </div>
            <div class="mb-3">
                <label for="buyer_id" class="form-label">Плательщик (Покупатель)</label>
                <select class="form-select" id="buyer_id" name="buyer_id" required>
                    <option value="">Выберите покупателя</option>
                    <?php foreach ($buyers as $buyer): ?>
                        <option value="<?= $buyer['id'] ?? $buyer->id ?>"><?= htmlspecialchars($buyer['name'] ?? $buyer->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="legal_entity_id" class="form-label">Получатель (Юр.лицо)</label>
                <select class="form-select" id="legal_entity_id" name="legal_entity_id" required>
                    <option value="">Выберите юр.лицо</option>
                    <?php foreach ($legalEntities as $entity): ?>
                        <option value="<?= $entity['id'] ?? $entity->id ?>"><?= htmlspecialchars($entity['name'] ?? $entity->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Сумма</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="mb-3">
                <label for="invoice_ids" class="form-label">Привязанные счета</label>
                <select class="form-select" id="invoice_ids" name="invoice_ids[]" multiple>
                    <?php foreach ($invoices as $invoice): ?>
                        <option value="<?= $invoice['id'] ?? $invoice->id ?>">
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
                <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/payments" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div> 