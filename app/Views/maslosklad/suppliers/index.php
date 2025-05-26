<?php include_once __DIR__ . '/../../layouts/default_header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Поставщики</h4>
                            <p class="text-sm mb-0">
                                <i class="fas fa-truck text-primary me-1"></i>
                                Управление поставщиками и контрагентами
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <?php if (isset($userPermissions['maslosklad.manage'])): ?>
                                <button type="button" class="btn bg-gradient-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                    <i class="fas fa-plus me-1"></i> Добавить поставщика
                                </button>
                            <?php endif; ?>
                            <a href="/maslosklad" class="btn bg-gradient-secondary btn-sm ms-1">
                                <i class="fas fa-arrow-left me-1"></i> К списку складов
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($suppliers)): ?>
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i> Список поставщиков пуст
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Наименование</th>
                                        <th>Контактное лицо</th>
                                        <th>Телефон</th>
                                        <th>Email</th>
                                        <th>Адрес</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($suppliers as $index => $supplier): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($supplier['name']) ?></strong>
                                                <?php if (!empty($supplier['description'])): ?>
                                                    <p class="text-xs text-muted mb-0"><?= htmlspecialchars($supplier['description']) ?></p>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($supplier['contact_person'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($supplier['phone'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($supplier['email'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($supplier['address'] ?? '-') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info view-supplier" data-id="<?= $supplier['id'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if (isset($userPermissions['maslosklad.manage'])): ?>
                                                        <button type="button" class="btn btn-warning edit-supplier" data-id="<?= $supplier['id'] ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger delete-supplier" data-id="<?= $supplier['id'] ?>" data-name="<?= htmlspecialchars($supplier['name']) ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
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

<!-- Модальное окно добавления поставщика -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Добавить поставщика</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="name" class="form-control-label required">Наименование</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="contact_person" class="form-control-label">Контактное лицо</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-control-label">Телефон</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-control-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="address" class="form-control-label">Адрес</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="description" class="form-control-label">Примечание</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="saveSupplier">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования поставщика -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSupplierModalLabel">Редактировать поставщика</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSupplierForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="edit_name" class="form-control-label required">Наименование</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_contact_person" class="form-control-label">Контактное лицо</label>
                                <input type="text" class="form-control" id="edit_contact_person" name="contact_person">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_phone" class="form-control-label">Телефон</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_email" class="form-control-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_address" class="form-control-label">Адрес</label>
                                <input type="text" class="form-control" id="edit_address" name="address">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="edit_description" class="form-control-label">Примечание</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="updateSupplier">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра поставщика -->
<div class="modal fade" id="viewSupplierModal" tabindex="-1" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSupplierModalLabel">Информация о поставщике</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 id="view_name" class="mb-3 text-primary"></h4>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Контактное лицо:</strong></p>
                        <p id="view_contact_person" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Телефон:</strong></p>
                        <p id="view_phone" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Email:</strong></p>
                        <p id="view_email" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Адрес:</strong></p>
                        <p id="view_address" class="mb-3">-</p>
                    </div>
                    <div class="col-md-12">
                        <p class="mb-1"><strong>Примечание:</strong></p>
                        <p id="view_description" class="mb-0">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/default_footer.php'; ?>

<script>
$(document).ready(function() {
    // Обработка клика по кнопке "Сохранить" в модальном окне добавления поставщика
    $('#saveSupplier').on('click', function() {
        let form = $('#addSupplierForm');
        
        // Валидация формы
        if (!form[0].checkValidity()) {
            form.addClass('was-validated');
            return;
        }
        
        // Отправка данных на сервер
        $.ajax({
            url: '/maslosklad/api/suppliers/create',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Скрываем модальное окно
                    $('#addSupplierModal').modal('hide');
                    
                    // Обновляем страницу
                    location.reload();
                } else {
                    alert('Ошибка: ' + response.message);
                }
            },
            error: function() {
                alert('Ошибка при отправке данных');
            }
        });
    });
    
    // Обработка клика по кнопке "Просмотр"
    $('.view-supplier').on('click', function() {
        let id = $(this).data('id');
        
        // Получаем данные поставщика с сервера
        $.ajax({
            url: '/maslosklad/api/suppliers/' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    // Заполняем модальное окно данными
                    $('#view_name').text(response.supplier.name);
                    $('#view_contact_person').text(response.supplier.contact_person || '-');
                    $('#view_phone').text(response.supplier.phone || '-');
                    $('#view_email').text(response.supplier.email || '-');
                    $('#view_address').text(response.supplier.address || '-');
                    $('#view_description').text(response.supplier.description || '-');
                    
                    // Показываем модальное окно
                    $('#viewSupplierModal').modal('show');
                } else {
                    alert('Ошибка: ' + response.message);
                }
            },
            error: function() {
                alert('Ошибка при получении данных');
            }
        });
    });
    
    // Обработка клика по кнопке "Редактировать"
    $('.edit-supplier').on('click', function() {
        let id = $(this).data('id');
        
        // Получаем данные поставщика с сервера
        $.ajax({
            url: '/maslosklad/api/suppliers/' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    // Заполняем форму редактирования данными
                    $('#edit_id').val(response.supplier.id);
                    $('#edit_name').val(response.supplier.name);
                    $('#edit_contact_person').val(response.supplier.contact_person || '');
                    $('#edit_phone').val(response.supplier.phone || '');
                    $('#edit_email').val(response.supplier.email || '');
                    $('#edit_address').val(response.supplier.address || '');
                    $('#edit_description').val(response.supplier.description || '');
                    
                    // Показываем модальное окно
                    $('#editSupplierModal').modal('show');
                } else {
                    alert('Ошибка: ' + response.message);
                }
            },
            error: function() {
                alert('Ошибка при получении данных');
            }
        });
    });
    
    // Обработка клика по кнопке "Сохранить" в модальном окне редактирования поставщика
    $('#updateSupplier').on('click', function() {
        let form = $('#editSupplierForm');
        let id = $('#edit_id').val();
        
        // Валидация формы
        if (!form[0].checkValidity()) {
            form.addClass('was-validated');
            return;
        }
        
        // Отправка данных на сервер
        $.ajax({
            url: '/maslosklad/api/suppliers/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Скрываем модальное окно
                    $('#editSupplierModal').modal('hide');
                    
                    // Обновляем страницу
                    location.reload();
                } else {
                    alert('Ошибка: ' + response.message);
                }
            },
            error: function() {
                alert('Ошибка при отправке данных');
            }
        });
    });
    
    // Обработка клика по кнопке "Удалить"
    $('.delete-supplier').on('click', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        // Запрашиваем подтверждение удаления
        if (confirm('Вы действительно хотите удалить поставщика "' + name + '"?')) {
            // Отправка запроса на удаление
            $.ajax({
                url: '/maslosklad/api/suppliers/delete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        // Обновляем страницу
                        location.reload();
                    } else {
                        alert('Ошибка: ' + response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при отправке данных');
                }
            });
        }
    });
});
</script>

<style>
/* Стили для обязательных полей */
.form-control-label.required:after {
    content: " *";
    color: red;
}
</style> 