<?php include_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-6">
                            <h4>Управление складами</h4>
                            <p class="text-sm mb-0">
                                <i class="fa fa-warehouse text-info" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">Создание и редактирование складов</span>
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <button type="button" class="btn btn-sm bg-gradient-success" data-bs-toggle="modal" data-bs-target="#createWarehouseModal">
                                <i class="fas fa-plus me-2"></i> Создать склад
                            </button>
                            <a href="/maslosklad" class="btn btn-sm bg-gradient-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Назад к списку
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <?php if (empty($warehouses)): ?>
                        <div class="p-4 text-center">
                            <h4 class="text-muted">Нет доступных складов</h4>
                            <p>Создайте первый склад для начала работы</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Название</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Тип склада</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Расположение</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Описание</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($warehouses as $warehouse): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm"><?= htmlspecialchars($warehouse['name']) ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-<?= $this->getWarehouseTypeColor($warehouse['warehouse_type_name']) ?>">
                                                    <?= htmlspecialchars($warehouse['warehouse_type_name']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    <?= !empty($warehouse['location']) ? htmlspecialchars($warehouse['location']) : '-' ?>
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0">
                                                    <?= !empty($warehouse['description']) ? htmlspecialchars(substr($warehouse['description'], 0, 50)) . (strlen($warehouse['description']) > 50 ? '...' : '') : '-' ?>
                                                </p>
                                            </td>
                                            <td class="align-middle">
                                                <button type="button" class="btn btn-link text-secondary mb-0 edit-warehouse-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editWarehouseModal"
                                                        data-id="<?= $warehouse['id'] ?>"
                                                        data-name="<?= htmlspecialchars($warehouse['name']) ?>"
                                                        data-type-id="<?= $warehouse['type_id'] ?>"
                                                        data-location="<?= htmlspecialchars($warehouse['location'] ?? '') ?>"
                                                        data-description="<?= htmlspecialchars($warehouse['description'] ?? '') ?>">
                                                    <i class="fas fa-edit text-secondary"></i>
                                                </button>
                                                <button type="button" class="btn btn-link text-danger mb-0 delete-warehouse-btn"
                                                        data-id="<?= $warehouse['id'] ?>"
                                                        data-name="<?= htmlspecialchars($warehouse['name']) ?>">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для создания склада -->
<div class="modal fade" id="createWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="createWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createWarehouseModalLabel">Создание нового склада</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createWarehouseForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="warehouseName" class="form-control-label">Название склада</label>
                        <input type="text" class="form-control" id="warehouseName" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="warehouseType" class="form-control-label">Тип склада</label>
                        <select class="form-control" id="warehouseType" name="type_id" required>
                            <option value="">Выберите тип склада</option>
                            <?php foreach ($warehouseTypes as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="warehouseLocation" class="form-control-label">Расположение</label>
                        <input type="text" class="form-control" id="warehouseLocation" name="location">
                    </div>
                    <div class="form-group mb-3">
                        <label for="warehouseDescription" class="form-control-label">Описание</label>
                        <textarea class="form-control" id="warehouseDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать склад</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно для редактирования склада -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="editWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWarehouseModalLabel">Редактирование склада</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editWarehouseForm">
                <input type="hidden" id="editWarehouseId" name="id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="editWarehouseName" class="form-control-label">Название склада</label>
                        <input type="text" class="form-control" id="editWarehouseName" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editWarehouseType" class="form-control-label">Тип склада</label>
                        <select class="form-control" id="editWarehouseType" name="type_id" required>
                            <option value="">Выберите тип склада</option>
                            <?php foreach ($warehouseTypes as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editWarehouseLocation" class="form-control-label">Расположение</label>
                        <input type="text" class="form-control" id="editWarehouseLocation" name="location">
                    </div>
                    <div class="form-group mb-3">
                        <label for="editWarehouseDescription" class="form-control-label">Описание</label>
                        <textarea class="form-control" id="editWarehouseDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно для подтверждения удаления -->
<div class="modal fade" id="deleteWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="deleteWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteWarehouseModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить склад <strong id="deleteWarehouseName"></strong>?</p>
                <p class="text-danger">Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteWarehouse">Удалить</button>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>

<script>
$(document).ready(function() {
    // Создание склада
    $('#createWarehouseForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/maslosklad/create-warehouse',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Показываем уведомление об успехе
                    showNotification('success', response.message);
                    
                    // Закрываем модальное окно и перезагружаем страницу
                    $('#createWarehouseModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    // Показываем уведомление об ошибке
                    showNotification('error', response.message);
                }
            },
            error: function() {
                showNotification('error', 'Произошла ошибка при создании склада');
            }
        });
    });
    
    // Редактирование склада - заполнение формы
    $('.edit-warehouse-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var typeId = $(this).data('type-id');
        var location = $(this).data('location');
        var description = $(this).data('description');
        
        $('#editWarehouseId').val(id);
        $('#editWarehouseName').val(name);
        $('#editWarehouseType').val(typeId);
        $('#editWarehouseLocation').val(location);
        $('#editWarehouseDescription').val(description);
    });
    
    // Редактирование склада - отправка формы
    $('#editWarehouseForm').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#editWarehouseId').val();
        
        $.ajax({
            url: '/maslosklad/update-warehouse/' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Показываем уведомление об успехе
                    showNotification('success', response.message);
                    
                    // Закрываем модальное окно и перезагружаем страницу
                    $('#editWarehouseModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    // Показываем уведомление об ошибке
                    showNotification('error', response.message);
                }
            },
            error: function() {
                showNotification('error', 'Произошла ошибка при обновлении склада');
            }
        });
    });
    
    // Удаление склада - подготовка модального окна
    $('.delete-warehouse-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#deleteWarehouseName').text(name);
        $('#confirmDeleteWarehouse').data('id', id);
        
        $('#deleteWarehouseModal').modal('show');
    });
    
    // Удаление склада - подтверждение
    $('#confirmDeleteWarehouse').on('click', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '/maslosklad/delete-warehouse/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Показываем уведомление об успехе
                    showNotification('success', response.message);
                    
                    // Закрываем модальное окно и перезагружаем страницу
                    $('#deleteWarehouseModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    // Показываем уведомление об ошибке
                    showNotification('error', response.message);
                    $('#deleteWarehouseModal').modal('hide');
                }
            },
            error: function() {
                showNotification('error', 'Произошла ошибка при удалении склада');
                $('#deleteWarehouseModal').modal('hide');
            }
        });
    });
    
    // Функция для отображения уведомлений
    function showNotification(type, message) {
        var icon = type === 'success' ? 'fas fa-check' : 'fas fa-exclamation-triangle';
        var color = type === 'success' ? 'success' : 'danger';
        
        $.notify({
            icon: icon,
            message: message
        }, {
            type: color,
            timer: 3000,
            placement: {
                from: 'top',
                align: 'right'
            }
        });
    }
});
</script> 