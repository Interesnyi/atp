<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-6">
                            <h4>Получатели</h4>
                            <p class="text-sm mb-0">
                                <i class="bi bi-people text-primary me-1"></i>
                                Управление получателями и контрагентами
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="/warehouses/buyers/create" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus me-1"></i> Добавить получателя
                            </a>
                            <a href="/warehouses" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="bi bi-warehouse me-1"></i> Склады
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <input type="text" id="searchBuyers" class="form-control" placeholder="Поиск получателей...">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="buyersTable" class="table table-hover align-middle">
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
                                <?php if (empty($buyers)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Список получателей пуст</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($buyers as $buyer): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($buyer['name']) ?></strong>
                                                <?php if (!empty($buyer['description'])): ?>
                                                    <p class="text-xs text-muted mb-0"><?= htmlspecialchars(mb_substr($buyer['description'], 0, 70)) ?><?= (mb_strlen($buyer['description']) > 70) ? '...' : '' ?></p>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($buyer['contact_person'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($buyer['phone'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($buyer['email'] ?? '-') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/warehouses/buyers/view/<?= $buyer['id'] ?>" class="btn btn-outline-primary" title="Просмотр">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="/warehouses/buyers/edit/<?= $buyer['id'] ?>" class="btn btn-outline-secondary" title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger delete-buyer" data-id="<?= $buyer['id'] ?>" data-name="<?= htmlspecialchars($buyer['name']) ?>" title="Удалить">
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

<!-- DataTables и JS-логика аналогично suppliers -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#buyersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ru.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Все"]],
        responsive: true,
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rtip',
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [4] },
            { className: "text-center", targets: [4] }
        ]
    });
    $('#searchBuyers').on('keyup', function() {
        table.search(this.value).draw();
    });
    $(document).on('click', '.delete-buyer', function() {
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
                            <p>Вы действительно хотите удалить получателя <strong>"${name}"</strong>?</p>
                            <p class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Это действие нельзя отменить.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBuyer">
                                <i class="bi bi-trash me-1"></i> Удалить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
        $(document).off('click', '#confirmDeleteBuyer');
        $(document).on('click', '#confirmDeleteBuyer', function() {
            $.ajax({
                url: '/warehouses/api/buyers/delete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        deleteModal.hide();
                        let row = $("button.delete-buyer[data-id='" + id + "']").closest('tr');
                        table.row(row).remove().draw();
                        showNotification('success', 'Получатель успешно удалён');
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
    // Функция для уведомлений (аналогично suppliers)
    window.showNotification = function(type, message) {
        $('.toast').remove();
        let bgClass = 'bg-primary';
        let icon = 'info-circle';
        if (type === 'success') { bgClass = 'bg-success'; icon = 'check-circle'; }
        else if (type === 'error') { bgClass = 'bg-danger'; icon = 'exclamation-circle'; }
        else if (type === 'warning') { bgClass = 'bg-warning'; icon = 'exclamation-triangle'; }
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
        $('body').append(toastHtml);
        setTimeout(function() { $('.toast').toast('hide'); }, 3000);
    }
    setTimeout(function() { $('.alert-dismissible .btn-close').click(); }, 5000);
});
</script> 