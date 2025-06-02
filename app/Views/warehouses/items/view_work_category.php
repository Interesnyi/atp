<div class="container mt-4">
    <h2>Категория работ: <?= htmlspecialchars($category['name']) ?></h2>
    <table class="table table-bordered w-auto">
        <tr><th>Наименование</th><td><?= htmlspecialchars($category['name']) ?></td></tr>
        <tr><th>Описание</th><td><?= htmlspecialchars($category['description']) ?></td></tr>
        <tr><th>Дата создания</th><td><?= htmlspecialchars($category['created_at']) ?></td></tr>
    </table>
    <a href="/orders/work_categories/edit/<?= $category['id'] ?>" class="btn btn-warning">Редактировать</a>
    <a href="/orders/work_categories" class="btn btn-secondary">Назад</a>
</div> 