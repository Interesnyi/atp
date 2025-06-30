<?php /** @var array $parts */ ?>
<div class="container mt-4">
    <h2>Справочник запчастей</h2>
    <a href="/parts/create" class="btn btn-success mb-3">Добавить запчасть</a>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Артикул</th>
                    <th>Наименование</th>
                    <th>Цена</th>
                    <th style="width: 160px;">Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($parts as $part): ?>
                <tr>
                    <td><?= htmlspecialchars($part['article']) ?></td>
                    <td><?= htmlspecialchars($part['name']) ?></td>
                    <td><?= number_format($part['price'], 2, '.', ' ') ?></td>
                    <td>
                        <a href="/parts/edit/<?= $part['id'] ?>" class="btn btn-primary btn-sm">Редактировать</a>
                        <form action="/parts/delete/<?= $part['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить запчасть?');">
                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 