<?= $content ?? '' ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-6">
                            <h4>Поставщики</h4>
                            <p class="text-sm mb-0">
                                <i class="fas fa-truck text-primary me-1"></i>
                                Управление поставщиками и контрагентами
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="/warehouses/suppliers/create" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus me-1"></i> Добавить поставщика
                            </a>
                            <a href="/warehouses" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="bi bi-warehouse me-1"></i> Склады
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <input type="text" class="form-control" placeholder="Поиск поставщиков...">
                        </div>
                    </div>
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
                    
                    <?php if (!empty($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table id="suppliersTable" class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Наименование</th>
                                    <th>Контактное лицо</th>
                                    <th>Телефон</th>
                                    <th>Email</th>
                                    <th width="150">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($suppliers)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Список поставщиков пуст</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($suppliers as $supplier): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($supplier['name']) ?></strong>
                                                <?php if (!empty($supplier['description'])): ?>
                                                    <p class="text-xs text-muted mb-0"><?= htmlspecialchars(mb_substr($supplier['description'], 0, 70)) ?><?= (mb_strlen($supplier['description']) > 70) ? '...' : '' ?></p>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($supplier['contact_person'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($supplier['phone'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($supplier['email'] ?? '-') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/warehouses/suppliers/view/<?= $supplier['id'] ?>" class="btn btn-outline-primary" title="Просмотр">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="/warehouses/suppliers/edit/<?= $supplier['id'] ?>" class="btn btn-outline-secondary" title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger delete-supplier" data-id="<?= $supplier['id'] ?>" data-name="<?= htmlspecialchars($supplier['name']) ?>" title="Удалить">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
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

<!-- Модальное окно редактирования поставщика -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editSupplierModalLabel">Редактировать поставщика</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSupplierForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="edit_name" class="form-control-label required">Наименование</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="edit_inn" class="form-control-label">ИНН</label>
                                <input type="text" class="form-control" id="edit_inn" name="inn" pattern="[0-9]{10,12}" title="ИНН должен содержать 10 или 12 цифр">
                                <small class="form-text text-muted">10 цифр для организаций, 12 для ИП</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_contact_person" class="form-control-label">Контактное лицо</label>
                                <input type="text" class="form-control" id="edit_contact_person" name="contact_person">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_phone" class="form-control-label">Телефон</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone" placeholder="+7 (___) ___-__-__">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
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
                    </div>
                    
                    <div class="form-group mb-0">
                        <label for="edit_description" class="form-control-label">Примечание</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-warning" id="updateSupplier">
                    <i class="fas fa-save me-1"></i> Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра поставщика -->
<div class="modal fade" id="viewSupplierModal" tabindex="-1" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewSupplierModalLabel">Информация о поставщике</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card border-0 shadow-none">
                    <div class="card-header bg-light">
                        <h4 id="view_name" class="mb-0 text-primary"></h4>
                    </div>
                    <div class="card-body p-3">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="fas fa-id-card text-info me-2 mt-1"></i>
                                    <div>
                                        <p class="mb-1 fw-bold">ИНН:</p>
                                        <p id="view_inn" class="mb-0">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="fas fa-user text-info me-2 mt-1"></i>
                                    <div>
                                        <p class="mb-1 fw-bold">Контактное лицо:</p>
                                        <p id="view_contact_person" class="mb-0">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="fas fa-phone text-info me-2 mt-1"></i>
                                    <div>
                                        <p class="mb-1 fw-bold">Телефон:</p>
                                        <p id="view_phone" class="mb-0">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="fas fa-envelope text-info me-2 mt-1"></i>
                                    <div>
                                        <p class="mb-1 fw-bold">Email:</p>
                                        <p id="view_email" class="mb-0">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="fas fa-map-marker-alt text-info me-2 mt-1"></i>
                                    <div>
                                        <p class="mb-1 fw-bold">Адрес:</p>
                                        <p id="view_address" class="mb-0">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                    <div>
                                        <p class="mb-1 fw-bold">Примечание:</p>
                                        <p id="view_description" class="mb-0">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-warning edit-from-view">
                    <i class="fas fa-edit me-1"></i> Редактировать
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Подключение DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Подключение jQuery InputMask для масок ввода -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>

<script>
$(document).ready(function() {
    // Инициализация InputMask для телефонов
    $("#phone, #edit_phone").inputmask("+7 (999) 999-99-99", { "placeholder": "_" });
    
    // Инициализация InputMask для ИНН
    $("#inn, #edit_inn").inputmask("9{10,12}", { "placeholder": "" });
    
    // Инициализация DataTables с улучшенными настройками
    var table = $('#suppliersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ru.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Все"]],
        responsive: true,
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rtip',
        order: [[0, 'asc']], // Сортировка по наименованию
        columnDefs: [
            { orderable: false, targets: [4] }, // Отключаем сортировку для столбца действий
            { className: "text-center", targets: [4] } // Центрируем текст в столбце действий
        ],
        initComplete: function () {
            // Добавляем кастомный поиск
            this.api().columns([1, 2, 3, 4, 5, 6]).every(function (i) {
                var column = this;
                var title = $(column.header()).text();
                $('<input type="text" placeholder="Поиск" class="form-control form-control-sm" />')
                    .appendTo($(column.header()))
                    .on('keyup change', function () {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
            });
        }
    });
    
    // Поиск в реальном времени в общем поле поиска
    $('#searchSuppliers').on('keyup', function() {
        table.search(this.value).draw();
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
            url: '/warehouses/api/suppliers/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Скрываем модальное окно
                    $('#editSupplierModal').modal('hide');
                    
                    // Показываем уведомление об успехе
                    showNotification('success', 'Данные поставщика успешно обновлены');
                    
                    // Обновляем страницу
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('error', 'Ошибка: ' + response.message);
                }
            },
            error: function() {
                showNotification('error', 'Ошибка при отправке данных');
            }
        });
    });
    
    // Обработка клика по кнопке "Удалить"
    $(document).on('click', '.delete-supplier', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        $('#deleteConfirmModal').remove();
        $('body').append(`
            <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Подтверждение удаления</h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Вы действительно хотите удалить поставщика <strong>"${name}"</strong>?</p>
                            <p class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Это действие нельзя отменить.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-danger" id="confirmDelete">
                                <i class="fas fa-trash me-1"></i> Удалить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
        // Снимаем предыдущий обработчик и навешиваем новый
        $(document).off('click', '#confirmDelete');
        $(document).on('click', '#confirmDelete', function() {
            $.ajax({
                url: '/warehouses/api/suppliers/delete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        deleteModal.hide();
                        let row = $("button.delete-supplier[data-id='" + id + "']").closest('tr');
                        table.row(row).remove().draw();
                        showNotification('success', 'Поставщик успешно удален');
                    } else {
                        deleteModal.hide();
                        showNotification('error', 'Ошибка: ' + response.message);
                    }
                },
                error: function() {
                    deleteModal.hide();
                    showNotification('error', 'Ошибка при отправке данных');
                }
            });
        });
    });
    
    // Функция для отображения уведомлений
    function showNotification(type, message) {
        // Удаляем предыдущие уведомления
        $('.toast').remove();
        
        // Определяем классы в зависимости от типа уведомления
        let bgClass = 'bg-primary';
        let icon = 'info-circle';
        
        if (type === 'success') {
            bgClass = 'bg-success';
            icon = 'check-circle';
        } else if (type === 'error') {
            bgClass = 'bg-danger';
            icon = 'exclamation-circle';
        } else if (type === 'warning') {
            bgClass = 'bg-warning';
            icon = 'exclamation-triangle';
        }
        
        // Создаем HTML уведомления
        let toastHtml = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header ${bgClass} text-white">
                        <i class="fas fa-${icon} me-2"></i>
                        <strong class="me-auto">Уведомление</strong>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `;
        
        // Добавляем уведомление на страницу
        $('body').append(toastHtml);
        
        // Автоматически скрываем уведомление через 3 секунды
        setTimeout(function() {
            $('.toast').toast('hide');
        }, 3000);
    }
    
    // Автоматическое закрытие уведомлений через 5 секунд
    setTimeout(function() {
        $('.alert-dismissible .btn-close').click();
    }, 5000);
    
    // Очистка форм при закрытии модальных окон
    $('#editSupplierModal').on('hidden.bs.modal', function() {
        $('#editSupplierForm')[0].reset();
        $('#editSupplierForm').removeClass('was-validated');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Проверяем наличие ?added=1 в URL
    if (window.location.search.includes('added=1')) {
        showNotification('success', 'Поставщик успешно добавлен');
        // Удаляем параметр из URL, чтобы не повторялось при обновлении
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('added');
            window.history.replaceState({}, document.title, url.pathname);
        }
    }
});
</script>

<style>
/* Стили для обязательных полей */
.form-control-label.required:after {
    content: " *";
    color: red;
}

/* Стили для DataTables */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    margin-bottom: 10px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.2em 0.8em;
}

/* Скрываем стандартный поиск DataTables, так как используем свой */
.dataTables_filter {
    display: none;
}

/* Стили для кнопок действий */
.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

/* Улучшения для таблицы */
#suppliersTable thead th {
    vertical-align: top;
}

#suppliersTable thead input {
    margin-top: 5px;
    width: 100%;
    font-size: 0.8em;
}

/* Выделение строки при наведении */
#suppliersTable tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style> 