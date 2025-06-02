<?php // @var $customers array ?>
<div class="container mt-4">
    <h2>Добавить автомобиль</h2>
    <form method="post" action="/orders/cars/store">
        <div class="mb-3">
            <label for="customer_id" class="form-label">Клиент</label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="">Выберите клиента</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?php if (!empty($c['is_individual'])) { echo htmlspecialchars($c['contact_person']); } else { echo htmlspecialchars($c['company_name']); } ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="brand" class="form-label">Марка</label>
            <input type="text" class="form-control" id="brand" name="brand" required>
        </div>
        <div class="mb-3">
            <label for="model" class="form-label">Модель</label>
            <input type="text" class="form-control" id="model" name="model" required>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Год</label>
            <input type="number" class="form-control" id="year" name="year">
        </div>
        <div class="mb-3">
            <label for="vin" class="form-label">VIN</label>
            <input type="text" class="form-control" id="vin" name="vin">
        </div>
        <div class="mb-3">
            <label for="license_plate" class="form-label">Госномер</label>
            <input type="text" class="form-control" id="license_plate" name="license_plate">
        </div>
        <div class="mb-3">
            <label for="body_number" class="form-label">Кузов</label>
            <input type="text" class="form-control" id="body_number" name="body_number">
        </div>
        <div class="mb-3">
            <label for="engine_number" class="form-label">Двигатель</label>
            <input type="text" class="form-control" id="engine_number" name="engine_number">
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Комментарий</label>
            <textarea class="form-control" id="comment" name="comment"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/orders/cars" class="btn btn-secondary">Отмена</a>
    </form>
</div> 