<?php // @var $work array ?>
<div class="container mt-4">
    <h2>Редактировать работу</h2>
    <form method="post" action="/orders/work_types/update/<?= $work['id'] ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Наименование</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($work['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="code" class="form-label">Код</label>
            <input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($work['code']) ?>">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Цена</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($work['price']) ?>">
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Категория</label>
            <select class="form-select" id="category_id" name="category_id">
                <option value="">Без категории</option>
                <?php foreach ((new \App\Models\WorkCategory())->getAllCategories() as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($work['category_id'] ?? null) == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/orders/work_types" class="btn btn-secondary">Отмена</a>
    </form>
</div> 