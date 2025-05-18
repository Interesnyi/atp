<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Пользователи системы</h5>
                    <?php if ($canCreate): ?>
                    <a href="/users/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Добавить пользователя
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['success'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Имя пользователя</th>
                                    <th>Email</th>
                                    <th>Роль</th>
                                    <th>Создан</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <?php if ($user['role'] === 'admin'): ?>
                                                    <span class="badge bg-danger">Администратор</span>
                                                <?php elseif ($user['role'] === 'manager'): ?>
                                                    <span class="badge bg-primary">Менеджер</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Пользователь</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d.m.Y H:i', strtotime($user['created_at'] ?? date('Y-m-d H:i:s'))) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <!-- Просмотр всегда доступен с правом users.view -->
                                                    <a href="/users/show/<?= $user['id'] ?>" class="btn btn-outline-primary" title="Просмотр">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    <?php if ($canEdit): ?>
                                                    <a href="/users/edit/<?= $user['id'] ?>" class="btn btn-outline-secondary" title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    
                                                    <?php 
                                                    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['id']) ? $_SESSION['id'] : 0);
                                                    // Показываем кнопку удаления, если есть право и это не текущий пользователь
                                                    if ($canDelete && $user['id'] != $currentUserId): 
                                                    ?>
                                                        <button type="button" class="btn btn-outline-danger" title="Удалить" 
                                                            data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $user['id'] ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Модальное окно подтверждения удаления -->
                                                <?php if ($canDelete && $user['id'] != $currentUserId): ?>
                                                <div class="modal fade" id="deleteUserModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Подтверждение удаления</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Вы действительно хотите удалить пользователя <strong><?= htmlspecialchars($user['username']) ?></strong>?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                                <a href="/users/delete/<?= $user['id'] ?>" class="btn btn-danger">Удалить</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-3">Пользователи не найдены</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Автоматическое закрытие уведомлений через 5 секунд
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) closeButton.click();
            });
        }, 5000);
    });
</script> 