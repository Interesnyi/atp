<div class="container mt-4">
    <h1>Редактировать закупку №<?= htmlspecialchars($purchase['id']) ?></h1>
    <div class="mb-3">
        <b>Комментарий:</b> <?= nl2br(htmlspecialchars($purchase['comment'])) ?>
    </div>
    <hr>
    <h5>Позиции закупки</h5>
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Категория</th>
                <th>Имущество</th>
                <th>Количество</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($positions)): ?>
                <tr><td colspan="4" class="text-center">Позиции не добавлены</td></tr>
            <?php else: ?>
                <?php foreach ($positions as $pos): ?>
                    <tr>
                        <td><?= htmlspecialchars($pos['category_name']) ?></td>
                        <td><?= htmlspecialchars($pos['item_name']) ?></td>
                        <td><?= (int)$pos['quantity'] ?></td>
                        <td><!-- Здесь будут действия (удалить, добавить цену и т.д.) --></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <hr>
    <h5 class="d-flex justify-content-between align-items-center">
        <span>Добавить позицию</span>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus"></i> Добавить новое имущество
        </button>
    </h5>
    <form method="post" action="/purchases/add-item/<?= (int)$purchase['id'] ?>">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="category_id" class="form-label">Категория</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Выберите категорию...</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label for="item_id" class="form-label">Имущество</label>
                <select class="form-select" id="item_id" name="item_id" required>
                    <option value="">Выберите имущество...</option>
                    <?php foreach ($allItems as $item): ?>
                        <option value="<?= $item['id'] ?>" data-category="<?= $item['category_id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="quantity" class="form-label">Количество</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Добавить</button>
            </div>
        </div>
    </form>
    <a href="/purchases" class="btn btn-secondary mt-3">К списку закупок</a>
</div>

<!-- Модальное окно для добавления имущества -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addItemModalLabel">Добавить имущество</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
        <form id="add-item-form" action="/warehouses/items/store" method="POST">
          <div class="mb-3">
            <label for="modal_name" class="form-label">Наименование</label>
            <input type="text" class="form-control" id="modal_name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="modal_article" class="form-label">Артикул</label>
            <input type="text" class="form-control" id="modal_article" name="article">
          </div>
          <div class="mb-3">
            <label for="modal_category_id" class="form-label">Категория</label>
            <select class="form-select" id="modal_category_id" name="category_id" required>
              <option value="">Выберите категорию...</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="modal_has_volume" class="form-label">Объем (товар на розлив)</label>
            <select class="form-select" id="modal_has_volume" name="has_volume">
              <option value="0">Нет</option>
              <option value="1">Да</option>
            </select>
          </div>
          <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Фильтрация имущества по категории
$(document).ready(function() {
    $('#category_id').on('change', function() {
        var catId = $(this).val();
        $('#item_id option').each(function() {
            var itemCat = $(this).data('category');
            if (!catId || !itemCat || itemCat == catId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#item_id').val('');
    });
});
</script> 