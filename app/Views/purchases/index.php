<div class="container mt-4">
    <h1>Закупки</h1>
    <a href="/purchases/create" class="btn btn-primary mb-3">Добавить закупку</a>
    <?php if (!empty($purchases)): ?>
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Комментарий</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?= (int)$purchase['id'] ?></td>
                        <td><?= htmlspecialchars($purchase['comment']) ?></td>
                        <td><?= htmlspecialchars($purchase['status']) ?></td>
                        <td><?= htmlspecialchars($purchase['created_at']) ?></td>
                        <td>
                            <a href="/purchases/edit/<?= (int)$purchase['id'] ?>" class="btn btn-sm btn-outline-primary">Редактировать</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Список закупок пуст.</div>
    <?php endif; ?>
</div> 