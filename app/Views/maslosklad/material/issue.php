<?php include_once __DIR__ . '/../../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Выдача товара со склада</h4>
                            <p class="text-sm mb-0">
                                <i class="fas fa-warehouse text-primary me-1"></i>
                                <?= htmlspecialchars($warehouse['name']) ?>
                                <span class="badge bg-gradient-primary ms-2">
                                    <?= htmlspecialchars($warehouse['warehouse_type_name']) ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="/maslosklad/material/<?= $warehouse['id'] ?>" class="btn btn-sm bg-gradient-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Вернуться к складу
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="issueForm" class="needs-validation" novalidate>
                        <input type="hidden" name="warehouse_id" value="<?= $warehouse['id'] ?>">
                        
                        <div class="row">
                            <!-- Левая колонка - информация о товаре -->
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Информация о товаре</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="item_id" class="form-control-label required">Товар</label>
                                    <select class="form-control" id="item_id" name="item_id" required>
                                        <option value="">Выберите товар</option>
                                        <?php foreach ($inventory as $item): ?>
                                            <?php if ($item['quantity'] > 0): ?>
                                                <option value="<?= $item['item_id'] ?>" 
                                                        data-name="<?= htmlspecialchars($item['item_name']) ?>"
                                                        data-quantity="<?= $item['quantity'] ?>"
                                                        data-unit="<?= htmlspecialchars($item['unit'] ?? 'шт') ?>">
                                                    <?= htmlspecialchars($item['item_name']) ?> 
                                                    (<?= $item['quantity'] ?> <?= htmlspecialchars($item['unit'] ?? 'шт') ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Пожалуйста, выберите товар</div>
                                    <?php if (empty($inventory)): ?>
                                        <small class="text-danger">На складе нет товаров для выдачи</small>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="item_info" class="form-control-label">Информация о товаре</label>
                                    <div class="card p-3" id="item_info">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Наименование:</span>
                                            <strong id="selected_item_name">-</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <span class="text-muted">Доступное количество:</span>
                                            <strong id="selected_item_quantity">-</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <span class="text-muted">Единица измерения:</span>
                                            <strong id="selected_item_unit">-</strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="quantity" class="form-control-label required">Количество для выдачи</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0.01" required>
                                    <div class="invalid-feedback" id="quantity_feedback">Пожалуйста, укажите количество товара</div>
                                </div>
                            </div>
                            
                            <!-- Правая колонка - информация о выдаче -->
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Информация о выдаче</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="customer_id" class="form-control-label">Получатель</label>
                                    <select class="form-control" id="customer_id" name="customer_id">
                                        <option value="">Выберите получателя</option>
                                        <?php foreach ($customers as $id => $name): ?>
                                            <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="document_number" class="form-control-label">№ документа</label>
                                    <input type="text" class="form-control" id="document_number" name="document_number">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="operation_date" class="form-control-label">Дата выдачи</label>
                                    <input type="datetime-local" class="form-control" id="operation_date" name="operation_date" value="<?= date('Y-m-d\TH:i') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="description" class="form-control-label">Примечание / Назначение</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn bg-gradient-warning" id="btnSubmit">
                                    <i class="fas fa-paper-plane me-2"></i> Выдать товар
                                </button>
                                <button type="button" class="btn bg-gradient-secondary" id="btnCancel">
                                    <i class="fas fa-times me-2"></i> Отмена
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно успешного завершения -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Товар выдан со склада</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <p>Товар успешно выдан со склада</p>
                <p id="successDetails" class="text-muted"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Закрыть</button>
                <a href="/maslosklad/material/<?= $warehouse['id'] ?>" class="btn bg-gradient-primary">
                    <i class="fas fa-warehouse me-1"></i> К складу
                </a>
                <button type="button" class="btn bg-gradient-warning" id="newIssueBtn">
                    <i class="fas fa-plus me-1"></i> Еще выдача
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения выдачи -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение выдачи</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите выдать со склада:</p>
                <p class="mb-1"><strong id="confirm_item_name"></strong></p>
                <p class="mb-1">Количество: <strong id="confirm_quantity"></strong></p>
                <p class="mb-1">Получатель: <strong id="confirm_customer">Не указан</strong></p>
                <p class="mb-0">Документ: <strong id="confirm_document">Не указан</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn bg-gradient-warning" id="confirmIssueBtn">
                    <i class="fas fa-check me-1"></i> Подтвердить выдачу
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../layout/footer.php'; ?>

<script>
$(document).ready(function() {
    // Обработка выбора товара
    $('#item_id').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        let itemName = selectedOption.data('name') || '-';
        let itemQuantity = selectedOption.data('quantity') || 0;
        let itemUnit = selectedOption.data('unit') || 'шт';
        
        $('#selected_item_name').text(itemName);
        $('#selected_item_quantity').text(itemQuantity + ' ' + itemUnit);
        $('#selected_item_unit').text(itemUnit);
        
        // Сбрасываем количество
        $('#quantity').val('').attr('max', itemQuantity);
    });
    
    // Проверка количества при вводе
    $('#quantity').on('input', function() {
        let selectedOption = $('#item_id').find('option:selected');
        let availableQuantity = selectedOption.data('quantity') || 0;
        let enteredQuantity = parseFloat($(this).val()) || 0;
        
        if (enteredQuantity > availableQuantity) {
            $(this).addClass('is-invalid');
            $('#quantity_feedback').text('Количество не может превышать доступное (' + availableQuantity + ')');
        } else if (enteredQuantity <= 0) {
            $(this).addClass('is-invalid');
            $('#quantity_feedback').text('Количество должно быть больше нуля');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Отмена выдачи
    $('#btnCancel').on('click', function() {
        window.location.href = '/maslosklad/material/<?= $warehouse['id'] ?>';
    });
    
    // Валидация и подтверждение выдачи
    $('#issueForm').on('submit', function(e) {
        e.preventDefault();
        
        // Проверка валидации формы
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        
        // Дополнительная валидация количества
        let selectedOption = $('#item_id').find('option:selected');
        let availableQuantity = selectedOption.data('quantity') || 0;
        let enteredQuantity = parseFloat($('#quantity').val()) || 0;
        
        if (enteredQuantity > availableQuantity) {
            $('#quantity').addClass('is-invalid');
            $('#quantity_feedback').text('Количество не может превышать доступное (' + availableQuantity + ')');
            return;
        }
        
        // Заполняем данные подтверждения
        $('#confirm_item_name').text(selectedOption.data('name'));
        $('#confirm_quantity').text(enteredQuantity + ' ' + selectedOption.data('unit'));
        
        let customerId = $('#customer_id').val();
        if (customerId) {
            $('#confirm_customer').text($('#customer_id option:selected').text());
        } else {
            $('#confirm_customer').text('Не указан');
        }
        
        let documentNumber = $('#document_number').val();
        if (documentNumber) {
            $('#confirm_document').text(documentNumber);
        } else {
            $('#confirm_document').text('Не указан');
        }
        
        // Показываем модальное окно подтверждения
        $('#confirmModal').modal('show');
    });
    
    // Подтверждение и отправка данных
    $('#confirmIssueBtn').on('click', function() {
        // Сбор данных формы
        let formData = new FormData($('#issueForm')[0]);
        
        // Отправка данных
        $.ajax({
            url: '/maslosklad/material/<?= $warehouse['id'] ?>/process-issue',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                // Закрываем модальное окно подтверждения
                $('#confirmModal').modal('hide');
                
                if (response.success) {
                    // Показываем модальное окно успешного завершения
                    let selectedOption = $('#item_id').find('option:selected');
                    let itemName = selectedOption.data('name');
                    let quantity = $('#quantity').val();
                    let unit = selectedOption.data('unit');
                    
                    $('#successDetails').text(itemName + ' - ' + quantity + ' ' + unit);
                    
                    // Показываем модальное окно
                    $('#successModal').modal('show');
                } else {
                    // Показываем сообщение об ошибке
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка',
                        text: response.message || 'Не удалось выдать товар со склада'
                    });
                }
            },
            error: function() {
                // Закрываем модальное окно подтверждения
                $('#confirmModal').modal('hide');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка',
                    text: 'Произошла ошибка при отправке данных'
                });
            }
        });
    });
    
    // Новая выдача после успешного завершения
    $('#newIssueBtn').on('click', function() {
        // Сбрасываем форму
        $('#issueForm').removeClass('was-validated')[0].reset();
        
        // Сбрасываем информацию о товаре
        $('#selected_item_name').text('-');
        $('#selected_item_quantity').text('-');
        $('#selected_item_unit').text('-');
        
        // Устанавливаем текущую дату
        $('#operation_date').val(new Date().toISOString().slice(0, 16));
        
        // Закрываем модальное окно
        $('#successModal').modal('hide');
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