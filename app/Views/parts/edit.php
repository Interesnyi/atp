<?php /** @var array $part */ ?>
<div class="container mt-4">
    <h2>Редактировать запчасть</h2>
    <form action="/parts/update/<?= $part['id'] ?>" method="post" class="row g-3">
        <div class="col-md-4">
            <label for="article" class="form-label">Артикул</label>
            <input type="text" class="form-control" id="article" name="article" value="<?= htmlspecialchars($part['article']) ?>" required>
        </div>
        <div class="col-md-6">
            <label for="name" class="form-label">Наименование</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($part['name']) ?>" required>
        </div>
        <div class="col-md-2">
            <label for="price" class="form-label">Цена</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($part['price']) ?>" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="/parts" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div> 