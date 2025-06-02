<?php /** @var array $task */ ?>
<div class="container mt-4">
    <h2><?= htmlspecialchars($task['title']) ?></h2>
    <div class="mb-3">
        <b>Описание:</b> <?= nl2br(htmlspecialchars($task['description'])) ?><br>
        <b>Исполнитель:</b> <?= htmlspecialchars($task['executor_id'] ? ($users[array_search($task['executor_id'], array_column($users, 'id'))]['username'] ?? '—') : '—') ?><br>
        <b>Срок до:</b> <?= $task['due_date'] ? date('d.m.Y', strtotime($task['due_date'])) : '—' ?><br>
        <b>Статус:</b> <?= htmlspecialchars($task['status']) ?><br>
    </div>
    <hr>
    <h5>Этапы / Заметки</h5>
    <?php if (!empty($notes)): ?>
        <ul class="list-group mb-3">
            <?php foreach ($notes as $note): ?>
                <li class="list-group-item">
                    <span class="text-muted small"><?= date('d.m.Y H:i', strtotime($note['created_at'])) ?>:</span> <?= nl2br(htmlspecialchars($note['note'])) ?>
                    <?php $files = (new \App\Models\Task())->getFilesByNoteId($note['id']); if ($files): ?>
                        <div class="mt-2">
                            <b>Файлы:</b>
                            <?php foreach ($files as $file): ?>
                                <a href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank" class="me-2">
                                    <i class="bi bi-paperclip"></i> <?= htmlspecialchars($file['file_name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="text-muted mb-3">Нет заметок или этапов.</div>
    <?php endif; ?>
    <form method="post" action="/tasks/add-note/<?= $task['id'] ?>" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="note" class="form-label">Добавить этап / заметку</label>
            <textarea class="form-control" id="note" name="note" rows="2" required></textarea>
        </div>
        <div class="mb-3">
            <label for="note_files" class="form-label">Файлы (можно несколько)</label>
            <input type="file" class="form-control" id="note_files" name="note_files[]" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Добавить</button>
        <a href="/tasks" class="btn btn-secondary">Назад к списку</a>
    </form>
</div> 