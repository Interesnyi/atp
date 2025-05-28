<?php /** @var array $tasks */ ?>
<?php
$statusDict = [
    'active' => 'Активно',
    'done' => 'Выполнено',
    'canceled' => 'Отменено',
];
?>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Дела</h1>
            <a href="/tasks/create" class="btn btn-primary mb-3">Добавить дело</a>
            <form method="post" action="/tasks/notify" style="display:inline-block;">
                <button type="submit" class="btn btn-outline-info mb-3 ms-2">Отправить уведомления</button>
            </form>
            <?php if (!empty($_SESSION['notify_log'])): ?>
                <div class="alert alert-info">
                    <b>Лог отправки уведомлений:</b><br>
                    <ul class="mb-0">
                        <?php foreach ($_SESSION['notify_log'] as $msg): ?>
                            <li><?= htmlspecialchars($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['notify_log']); ?>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Дата</th>
                            <th>Описание</th>
                            <th>Исполнитель</th>
                            <th>Срок до</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tasks)): ?>
                        <tr><td colspan="6" class="text-center">Нет дел</td></tr>
                        <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d.m.Y', strtotime($task['created_at']))) ?></td>
                            <td><?= htmlspecialchars($task['title']) ?><br><small class="text-muted"><?= nl2br(htmlspecialchars($task['description'])) ?></small></td>
                            <td><?= htmlspecialchars($task['executor_name'] ?? '-') ?></td>
                            <td><?= $task['due_date'] ? htmlspecialchars(date('d.m.Y', strtotime($task['due_date']))) : '-' ?></td>
                            <td><?= $statusDict[$task['status']] ?? htmlspecialchars($task['status']) ?></td>
                            <td>
                                <a href="/tasks/edit/<?= $task['id'] ?>" class="btn btn-sm btn-outline-secondary">Редактировать</a>
                                <form method="post" action="/tasks/delete/<?= $task['id'] ?>" style="display:inline-block" onsubmit="return confirm('Удалить дело?');">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
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