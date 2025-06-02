<div class="container py-4">
    <h4><?= htmlspecialchars($title ?? 'Редактировать операцию') ?></h4>
    <form method="post" action="/warehouses/operations/update/<?= $operation['id'] ?>" id="operationForm">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="item_id" class="form-label">Товар</label>
                <select class="form-select" id="item_id" name="item_id" required>
                    <option value="">Выберите товар...</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= $item['id'] ?>" data-has-volume="<?= $item['has_volume'] ?>" <?= ($operation['item_id'] == $item['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($item['name']) ?><?php if ($item['has_volume']): ?> (наливной)<?php else: ?> (штучный)<?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="operation_type_id" class="form-label">Тип операции</label>
                <select class="form-select" id="operation_type_id" name="operation_type_id" required>
                    <option value="">Выберите тип...</option>
                    <?php foreach ($operationTypes as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= ($operation['operation_type_id'] == $type['id']) ? 'selected' : '' ?>><?= htmlspecialchars($type['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="operation_date" class="form-label">Дата операции</label>
                <input type="datetime-local" class="form-control" id="operation_date" name="operation_date" value="<?= date('Y-m-d\TH:i', strtotime($operation['operation_date'])) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="quantity" class="form-label">Количество (штучный)</label>
                <input type="number" step="1" min="0" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($operation['quantity']) ?>">
            </div>
            <div class="col-md-4">
                <label for="volume" class="form-label">Объём (л, наливной)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="volume" name="volume" value="<?= htmlspecialchars($operation['volume']) ?>">
            </div>
            <div class="col-md-4">
                <label for="warehouse_id" class="form-label">Место хранения</label>
                <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                    <option value="">Выберите место хранения...</option>
                    <?php foreach ($warehouses as $w): ?>
                        <option value="<?= $w['id'] ?>" <?= ($operation['warehouse_id'] == $w['id']) ? 'selected' : '' ?>><?= htmlspecialchars($w['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4" id="warehouse_id_to_wrap" style="display:none;">
                <label for="warehouse_id_to" class="form-label">Место хранения (получатель)</label>
                <select class="form-select" id="warehouse_id_to" name="warehouse_id_to">
                    <option value="">Выберите место хранения...</option>
                    <?php foreach ($warehouses as $w): ?>
                        <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="supplier_id" class="form-label">Поставщик</label>
                <select class="form-select" id="supplier_id" name="supplier_id">
                    <option value="">---</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($operation['supplier_id'] == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="buyer_id" class="form-label">Получатель</label>
                <select class="form-select" id="buyer_id" name="buyer_id">
                    <option value="">---</option>
                    <?php foreach ($buyers as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= ($operation['buyer_id'] == $b['id']) ? 'selected' : '' ?>><?= htmlspecialchars($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="description" class="form-label">Комментарий</label>
                <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($operation['description']) ?></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Сохранить</button>
        <a href="/warehouses/operations" class="btn btn-secondary">Отмена</a>
    </form>
    <hr>
    <div class="mt-4">
        <h5>Варианты операций и обязательные поля</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Тип операции</th>
                        <th>Обязательные поля</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>Приёмка</b></td>
                        <td>Товар, Дата, Количество/Объём, Склад, Поставщик</td>
                        <td>Увеличивает остаток на складе. Поставщик обязателен.</td>
                    </tr>
                    <tr>
                        <td><b>Выдача</b></td>
                        <td>Товар, Дата, Количество/Объём, Склад, Получатель</td>
                        <td>Уменьшает остаток на складе. Получатель обязателен.</td>
                    </tr>
                    <tr>
                        <td><b>Списание</b></td>
                        <td>Товар, Дата, Количество/Объём, Склад, Причина списания</td>
                        <td>Уменьшает остаток на складе. Причина списания обязательна (укажите в комментарии).</td>
                    </tr>
                    <tr>
                        <td><b>Инвентаризация</b></td>
                        <td>Товар, Дата, Количество/Объём, Склад</td>
                        <td>Устанавливает остаток на складе в указанное значение (перезаписывает).</td>
                    </tr>
                    <tr>
                        <td><b>Перемещение</b></td>
                        <td>Товар, Дата, Количество/Объём, Склад-отправитель, Склад-получатель</td>
                        <td>Уменьшает остаток на складе-отправителе, увеличивает на складе-получателе.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-muted small">
            <b>Комментарий</b> — опционально для всех операций.<br>
            <b>Цена за единицу</b> — опционально для приёмки.
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemSelect = document.getElementById('item_id');
        const quantityInput = document.getElementById('quantity');
        const volumeInput = document.getElementById('volume');
        const opTypeSelect = document.getElementById('operation_type_id');
        const supplierWrap = document.getElementById('supplier_id').parentElement;
        const buyerWrap = document.getElementById('buyer_id').parentElement;
        const priceWrap = document.getElementById('price') ? document.getElementById('price').parentElement : null;
        const warehouseToWrap = document.getElementById('warehouse_id_to_wrap');
        const warehouseTo = document.getElementById('warehouse_id_to');
        const warehouseFrom = document.getElementById('warehouse_id');
        const comment = document.getElementById('description');

        function toggleFields() {
            // Тип товара: штучный/наливной
            const selected = itemSelect.options[itemSelect.selectedIndex];
            const hasVolume = selected ? selected.getAttribute('data-has-volume') : '0';
            if (hasVolume == '1') {
                volumeInput.parentElement.style.display = '';
                volumeInput.required = true;
                quantityInput.parentElement.style.display = 'none';
                quantityInput.required = false;
            } else {
                quantityInput.parentElement.style.display = '';
                quantityInput.required = true;
                volumeInput.parentElement.style.display = 'none';
                volumeInput.required = false;
            }
            // Тип операции
            const opType = opTypeSelect.options[opTypeSelect.selectedIndex]?.textContent.trim().toLowerCase();
            // Поставщик только для приёмки
            if (opType === 'приёмка') {
                supplierWrap.style.display = '';
                document.getElementById('supplier_id').required = true;
            } else {
                supplierWrap.style.display = 'none';
                document.getElementById('supplier_id').required = false;
            }
            // Получатель только для выдачи
            if (opType === 'выдача') {
                buyerWrap.style.display = '';
                document.getElementById('buyer_id').required = true;
            } else {
                buyerWrap.style.display = 'none';
                document.getElementById('buyer_id').required = false;
            }
            // Цена только для приёмки
            if (priceWrap) {
                if (opType === 'приёмка') {
                    priceWrap.style.display = '';
                    document.getElementById('price').required = false;
                } else {
                    priceWrap.style.display = 'none';
                    document.getElementById('price').required = false;
                }
            }
            // Место хранения (получатель) только для перемещения
            if (opType === 'перемещение') {
                warehouseToWrap.style.display = '';
                warehouseTo.required = true;
                warehouseFrom.required = true;
            } else {
                warehouseToWrap.style.display = 'none';
                warehouseTo.required = false;
            }
            // Комментарий обязателен для списания
            if (opType === 'списание') {
                comment.required = true;
                comment.placeholder = 'Укажите причину списания';
            } else {
                comment.required = false;
                comment.placeholder = '';
            }
        }
        itemSelect.addEventListener('change', toggleFields);
        opTypeSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
    </script>
</div> 