<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Поставщик: <?= htmlspecialchars($supplier['name']) ?></h5>
                    <a href="/warehouses/suppliers" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> К списку
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Контактное лицо</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($supplier['contact_person'] ?? '-') ?></dd>
                        <dt class="col-sm-4">Телефон</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($supplier['phone'] ?? '-') ?></dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($supplier['email'] ?? '-') ?></dd>
                        <dt class="col-sm-4">Примечание</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($supplier['description'] ?? '-') ?></dd>
                    </dl>
                </div>
                <div class="card-footer text-end">
                    <a href="/warehouses/suppliers/edit/<?= $supplier['id'] ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 