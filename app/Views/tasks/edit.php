<?php /** @var array $task, $users */ ?>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <h2>Редактировать дело</h2>
            <form method="post" action="/tasks/update/<?= $task['id'] ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Название</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($task['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="executor_id" class="form-label">Исполнитель</label>
                    <select class="form-select" id="executor_id" name="executor_id">
                        <option value="">Не назначен</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= ($task['executor_id'] == $user['id']) ? 'selected' : '' ?>><?= htmlspecialchars($user['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Срок до</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?= htmlspecialchars($task['due_date']) ?>">
                </div>
                <div class="mb-3">
                    <label for="remind_at" class="form-label">Напомнить (дата и время)</label>
                    <input type="datetime-local" class="form-control" id="remind_at" name="remind_at" value="<?= $task['remind_at'] ? date('Y-m-d\TH:i', strtotime($task['remind_at'])) : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Статус</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= ($task['status'] == 'active') ? 'selected' : '' ?>>Активно</option>
                        <option value="done" <?= ($task['status'] == 'done') ? 'selected' : '' ?>>Выполнено</option>
                        <option value="canceled" <?= ($task['status'] == 'canceled') ? 'selected' : '' ?>>Отменено</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="/tasks" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
</div> 