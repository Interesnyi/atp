<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Управление ролями</h5>
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
                    
                    <p class="lead">Выберите роль для настройки прав доступа:</p>
                    
                    <div class="row">
                        <?php foreach ($roles as $role): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <?php if ($role === 'admin'): ?>
                                            <i class="bi bi-shield-lock fs-1 text-danger"></i>
                                        <?php elseif ($role === 'manager'): ?>
                                            <i class="bi bi-person-badge fs-1 text-primary"></i>
                                        <?php else: ?>
                                            <i class="bi bi-person fs-1 text-secondary"></i>
                                        <?php endif; ?>
                                        
                                        <h4 class="mt-3"><?= ucfirst(htmlspecialchars($role)) ?></h4>
                                        
                                        <p class="text-muted">
                                            <?php if ($role === 'admin'): ?>
                                                Администратор системы с полным доступом
                                            <?php elseif ($role === 'manager'): ?>
                                                Менеджер с расширенными правами
                                            <?php else: ?>
                                                Обычный пользователь с базовыми правами
                                            <?php endif; ?>
                                        </p>
                                        
                                        <a href="/roles/permissions/<?= $role ?>" class="btn btn-primary">
                                            <i class="bi bi-gear-fill"></i> Настроить права
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 