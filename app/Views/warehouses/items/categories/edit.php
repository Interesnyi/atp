<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Редактировать категорию имущества</h5>
                </div>
                <div class="card-body">
                    <form action="/warehouses/items/categories/update/<?= $category['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Наименование</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_type_id" class="form-label">Тип склада</label>
                            <select class="form-select" id="warehouse_type_id" name="warehouse_type_id" required>
                                <option value="">Выберите тип склада...</option>
                                <?php foreach ($warehouseTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= ($category['warehouse_type_id'] ?? null) == $type['id'] ? 'selected' : '' ?>><?= htmlspecialchars($type['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <a href="/warehouses/items/categories" class="btn btn-secondary">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 