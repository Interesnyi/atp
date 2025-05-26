<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Редактировать поставщика: <?= htmlspecialchars($supplier['name']) ?></h5>
                    <a href="/warehouses/suppliers/view/<?= $supplier['id'] ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> К просмотру
                    </a>
                </div>
                <form method="post" action="/warehouses/api/suppliers/update/<?= $supplier['id'] ?>" class="card-body needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Наименование</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Контактное лицо</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?= htmlspecialchars($supplier['contact_person'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($supplier['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Примечание</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($supplier['description'] ?? '') ?></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Сохранить
                        </button>
                        <a href="/warehouses/suppliers/view/<?= $supplier['id'] ?>" class="btn btn-outline-secondary ms-2">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 