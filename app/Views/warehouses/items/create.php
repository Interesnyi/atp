<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Добавить имущество</h5>
    </div>
    <div class="card-body">
        <form action="/warehouses/items/store" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Наименование</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="article" class="form-label">Артикул</label>
                <input type="text" class="form-control" id="article" name="article">
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Категория</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Выберите категорию...</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php if (!isset($cat['is_deleted']) || !$cat['is_deleted']): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="has_volume" class="form-label">Объем (товар на розлив)</label>
                <select class="form-select" id="has_volume" name="has_volume">
                    <option value="0">Нет</option>
                    <option value="1">Да</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="/warehouses/items" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
.select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    height: 38px;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    box-shadow: none;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #212529;
    line-height: 2.1;
    padding-left: 0;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 10px;
}
.select2-container .select2-selection--single {
    display: flex;
    align-items: center;
    min-height: 38px;
}
</style>
<script>
$(document).ready(function() {
    $('#category_id').select2({
        width: '100%',
        placeholder: 'Выберите категорию...'
    });
});
</script> 