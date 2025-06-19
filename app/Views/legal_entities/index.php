<?php /** @var array $entities */ ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Юридические лица</h4>
                    <a href="/legal-entities/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Добавить
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Название</th>
                                    <th>ИНН</th>
                                    <th>КПП</th>
                                    <th>Адрес</th>
                                    <th>Телефон</th>
                                    <th>Email</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($entities)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Нет юридических лиц</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($entities as $e): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($e['name']) ?></td>
                                            <td><?= htmlspecialchars($e['inn']) ?></td>
                                            <td><?= htmlspecialchars($e['kpp']) ?></td>
                                            <td><?= htmlspecialchars($e['address']) ?></td>
                                            <td><?= htmlspecialchars($e['phone']) ?></td>
                                            <td><?= htmlspecialchars($e['email']) ?></td>
                                            <td>
                                                <a href="/legal-entities/edit/<?= $e['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Редактировать"><i class="bi bi-pencil"></i></a>
                                                <form action="/legal-entities/delete/<?= $e['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить юр. лицо?');">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Удалить"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 