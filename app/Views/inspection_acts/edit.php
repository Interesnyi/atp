<?php // @var $act array, $customers array ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/inspection-acts">Акты осмотра</a></li>
            <li class="breadcrumb-item active" aria-current="page">Редактировать</li>
        </ol>
    </nav>
    <h2>Редактировать акт осмотра</h2>
    <form method="post" action="/inspection-acts/update/<?= $act['id'] ?>">
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="date" class="form-label">Дата</label>
                <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($act['date']) ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="customer_id" class="form-label">Заказчик</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <option value="">Выберите заказчика</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $act['customer_id'] == $c['id'] ? 'selected' : '' ?>><?= !empty($c['company_name']) ? htmlspecialchars($c['company_name']) : htmlspecialchars($c['contact_person']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="car_id" class="form-label">Автомобиль</label>
                <select class="form-select" id="car_id" name="car_id" required>
                    <option value="<?= $act['car_id'] ?>">
                        <?= htmlspecialchars($act['brand'] . ' ' . $act['model'] . ' (' . $act['year'] . ', ' . $act['license_plate'] . ')') ?>
                    </option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="contract_id" class="form-label">Договор</label>
                <select class="form-select" id="contract_id" name="contract_id">
                    <option value="">Без привязки</option>
                    <?php foreach ($contracts as $contract): ?>
                        <option value="<?= $contract['id'] ?>" <?= $act['contract_id'] == $contract['id'] ? 'selected' : '' ?>>№<?= htmlspecialchars($contract['contract_number']) ?> от <?= htmlspecialchars($contract['contract_date']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание транспортного средства</label>
            <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($act['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="damages" class="form-label">Внешние видимые повреждения</label>
            <textarea class="form-control" id="damages" name="damages" rows="2"><?= htmlspecialchars($act['damages']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="conclusion" class="form-label">Выводы</label>
            <textarea class="form-control" id="conclusion" name="conclusion" rows="2"><?= htmlspecialchars($act['conclusion']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/inspection-acts" class="btn btn-secondary">Отмена</a>
    </form>
</div>
<script>
// Динамическая подгрузка автомобилей по заказчику
const customerSelect = document.getElementById('customer_id');
const carSelect = document.getElementById('car_id');
customerSelect.addEventListener('change', function() {
    const customerId = this.value;
    carSelect.innerHTML = '<option value="">Загрузка...</option>';
    if (customerId) {
        fetch('/orders/api/cars_by_customer/' + customerId)
            .then(res => res.json())
            .then(data => {
                carSelect.innerHTML = '<option value="">Выберите автомобиль</option>';
                data.forEach(car => {
                    carSelect.innerHTML += `<option value="${car.id}">${car.brand} ${car.model} (${car.year}, ${car.license_plate})</option>`;
                });
            });
    } else {
        carSelect.innerHTML = '<option value="">Сначала выберите заказчика</option>';
    }
});
</script> 