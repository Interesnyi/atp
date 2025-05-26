<div class="container py-4">
    <h4><?= htmlspecialchars(
        $title ?? 'Добавить операцию') ?></h4>
    <form method="post" action="/warehouses/operations/store" id="operationForm">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="item_id" class="form-label">Товар</label>
                <select class="form-select" id="item_id" name="item_id" required>
                    <option value="">Выберите товар...</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= $item['id'] ?>" data-has-volume="<?= $item['has_volume'] ?>">
                            <?= htmlspecialchars($item['name']) ?>
                            <?php if ($item['has_volume']): ?> (наливной)<?php else: ?> (штучный)<?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="operation_type_id" class="form-label">Тип операции</label>
                <select class="form-select" id="operation_type_id" name="operation_type_id" required>
                    <option value="">Выберите тип...</option>
                    <?php foreach ($operationTypes as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="operation_date" class="form-label">Дата операции</label>
                <input type="datetime-local" class="form-control" id="operation_date" name="operation_date" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="quantity" class="form-label">Количество (штучный)</label>
                <input type="number" step="1" min="0" class="form-control" id="quantity" name="quantity">
            </div>
            <div class="col-md-4">
                <label for="volume" class="form-label">Объём (л, наливной)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="volume" name="volume">
            </div>
            <div class="col-md-4">
                <label for="warehouse_id" class="form-label">Склад</label>
                <input type="number" class="form-control" id="warehouse_id" name="warehouse_id" required>
                <!-- Можно заменить на select, если есть справочник складов -->
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="supplier_id" class="form-label">Поставщик</label>
                <select class="form-select" id="supplier_id" name="supplier_id">
                    <option value="">---</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="buyer_id" class="form-label">Получатель</label>
                <select class="form-select" id="buyer_id" name="buyer_id">
                    <option value="">---</option>
                    <?php foreach ($buyers as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="price" class="form-label">Цена за единицу</label>
                <input type="number" step="0.01" min="0" class="form-control" id="price" name="price">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="description" class="form-label">Комментарий</label>
                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Сохранить</button>
        <a href="/warehouses/operations" class="btn btn-secondary">Отмена</a>
    </form>
    <script>
    // JS: показывать только нужное поле (quantity или volume) в зависимости от типа товара
    document.addEventListener('DOMContentLoaded', function() {
        const itemSelect = document.getElementById('item_id');
        const quantityInput = document.getElementById('quantity');
        const volumeInput = document.getElementById('volume');
        function toggleFields() {
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
        }
        itemSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
    </script>
</div> 