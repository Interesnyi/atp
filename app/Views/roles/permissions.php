<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Права доступа для роли: <?= ucfirst(htmlspecialchars($role)) ?></h5>
                    <a href="/roles" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Вернуться к списку ролей
                    </a>
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
                    
                    <form action="/roles/permissions/<?= htmlspecialchars($role) ?>" method="POST">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <button type="submit" class="btn btn-primary me-2">Сохранить изменения</button>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Выбрать все</label>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($role === 'admin'): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Администраторы автоматически имеют все права доступа. Настройка прав для этой роли не требуется.
                        </div>
                        <?php else: ?>
                            <?php foreach ($groupedPermissions as $groupName => $permissions): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><?= htmlspecialchars($groupName) ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach ($permissions as $permission): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" 
                                                            name="permissions[]" 
                                                            value="<?= $permission['id'] ?>" 
                                                            id="permission_<?= $permission['id'] ?>" 
                                                            <?= $permission['assigned'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="permission_<?= $permission['id'] ?>">
                                                            <?= htmlspecialchars($permission['name']) ?>
                                                            <small class="text-muted d-block"><?= htmlspecialchars($permission['description']) ?></small>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if ($role !== 'admin'): ?>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Обработка выбора всех прав
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                permissionCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
            });
        }
        
        // Проверяем, все ли чекбоксы отмечены при загрузке
        function updateSelectAll() {
            if (permissionCheckboxes.length === 0) return;
            
            const allChecked = Array.from(permissionCheckboxes).every(function(checkbox) {
                return checkbox.checked;
            });
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
            }
        }
        
        // Обработчик изменения отдельных чекбоксов
        permissionCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateSelectAll);
        });
        
        // Вызываем при загрузке страницы
        updateSelectAll();
    });
</script> 