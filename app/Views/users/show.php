<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Профиль пользователя</h5>
                    <div>
                        <a href="/users" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-arrow-left"></i> К списку
                        </a>
                        <a href="/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Редактировать
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <!-- Аватар пользователя (заглушка) -->
                            <div class="avatar-placeholder bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px; font-size: 3rem; margin: 0 auto;">
                                <i class="bi bi-person"></i>
                            </div>
                            
                            <!-- Роль пользователя с цветовым индикатором -->
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-danger">Администратор</span>
                            <?php elseif ($user['role'] === 'manager'): ?>
                                <span class="badge bg-warning">Менеджер</span>
                            <?php else: ?>
                                <span class="badge bg-info">Пользователь</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-9">
                            <h4><?= htmlspecialchars($user['username']) ?></h4>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 120px"><i class="bi bi-envelope me-2"></i>Email:</th>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                </tr>
                                
                                <tr>
                                    <th><i class="bi bi-calendar-event me-2"></i>Создан:</th>
                                    <td><?= isset($user['created_at']) ? date('d.m.Y H:i', strtotime($user['created_at'])) : 'Н/Д' ?></td>
                                </tr>
                                
                                <?php if (isset($user['updated_at']) && $user['updated_at']): ?>
                                <tr>
                                    <th><i class="bi bi-clock-history me-2"></i>Обновлен:</th>
                                    <td><?= date('d.m.Y H:i', strtotime($user['updated_at'])) ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if (isset($user['last_login']) && $user['last_login']): ?>
                                <tr>
                                    <th><i class="bi bi-box-arrow-in-right me-2"></i>Вход:</th>
                                    <td><?= date('d.m.Y H:i', strtotime($user['last_login'])) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Дополнительная информация и активность пользователя может быть добавлена здесь -->
                    <div class="border-top pt-3">
                        <h6>Активность пользователя</h6>
                        <p class="text-muted">Здесь будет отображаться информация о последней активности пользователя.</p>
                    </div>
                </div>
                
                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                <div class="card-footer">
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                        <i class="bi bi-trash"></i> Удалить пользователя
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<?php if ($user['id'] != $_SESSION['user_id']): ?>
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить пользователя <strong><?= htmlspecialchars($user['username']) ?></strong>?</p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> Это действие невозможно отменить!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <a href="/users/delete/<?= $user['id'] ?>" class="btn btn-danger">Удалить</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?> 