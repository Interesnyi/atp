<?php /** @var array $invoice, $legalEntities */ ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h4 class="mb-0">Редактировать счёт №<?= htmlspecialchars($invoice['number']) ?></h4>
                </div>
                <div class="card-body">
                    <form method="post" action="/invoices/update/<?= $invoice['id'] ?>">
                        <div class="mb-3">
                            <label for="number" class="form-label">Номер счёта</label>
                            <input type="text" class="form-control" id="number" name="number" value="<?= htmlspecialchars($invoice['number']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Дата</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($invoice['date']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="legal_entity_id" class="form-label">Юр. лицо</label>
                            <select class="form-select" id="legal_entity_id" name="legal_entity_id" required>
                                <option value="">Выберите...</option>
                                <?php foreach ($legalEntities as $entity): ?>
                                    <option value="<?= $entity['id'] ?>" <?= $invoice['legal_entity_id'] == $entity['id'] ? 'selected' : '' ?>><?= htmlspecialchars($entity['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Статусы</label>
                            <div class="row mb-1 align-items-center">
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="status_issued" name="status_issued" value="1" <?= $invoice['status_issued'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="status_issued">Выставлен</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <input type="date" class="form-control form-control-sm" id="date_issued" name="date_issued" value="<?= htmlspecialchars($invoice['date_issued'] ?? '') ?>" placeholder="Дата выставления">
                                </div>
                            </div>
                            <div class="row mb-1 align-items-center">
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="status_shipped" name="status_shipped" value="1" <?= $invoice['status_shipped'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="status_shipped">Отгружен</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <input type="date" class="form-control form-control-sm" id="date_shipped" name="date_shipped" value="<?= htmlspecialchars($invoice['date_shipped'] ?? '') ?>" placeholder="Дата отгрузки">
                                </div>
                            </div>
                            <div class="row mb-1 align-items-center">
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="status_paid" name="status_paid" value="1" <?= $invoice['status_paid'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="status_paid">Оплачен</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <input type="date" class="form-control form-control-sm" id="date_paid" name="date_paid" value="<?= htmlspecialchars($invoice['date_paid'] ?? '') ?>" placeholder="Дата оплаты">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Комментарий</label>
                            <textarea class="form-control" id="comment" name="comment" rows="2"><?= htmlspecialchars($invoice['comment']) ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <a href="/invoices/show/<?= $invoice['id'] ?>" class="btn btn-secondary">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        [
            {date: 'date_issued', checkbox: 'status_issued'},
            {date: 'date_shipped', checkbox: 'status_shipped'},
            {date: 'date_paid', checkbox: 'status_paid'}
        ].forEach(function(pair) {
            var dateInput = document.getElementById(pair.date);
            var checkbox = document.getElementById(pair.checkbox);
            if (dateInput && checkbox) {
                dateInput.addEventListener('input', function() {
                    if (dateInput.value) checkbox.checked = true;
                });
            }
        });
    });
</script> 