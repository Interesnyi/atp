<?php include_once __DIR__ . '/../../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Приёмка товара на склад</h4>
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
                    <form id="receptionForm" class="needs-validation" novalidate>
                        <input type="hidden" name="warehouse_id" value="<?= $warehouse['id'] ?>">
                        
                        <div class="row">
                            <!-- Левая колонка - информация о товаре -->
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Информация о товаре</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="category_id" class="form-control-label required">Категория</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="">Выберите категорию</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Пожалуйста, выберите категорию товара</div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="item_id" class="form-control-label required">Товар</label>
                                    <select class="form-control" id="item_id" name="item_id" required disabled>
                                        <option value="">Сначала выберите категорию</option>
                                    </select>
                                    <div class="invalid-feedback">Пожалуйста, выберите товар</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="unit" class="form-control-label">Единица измерения</label>
                                            <input type="text" class="form-control" id="unit" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label for="article" class="form-control-label">Артикул</label>
                                            <input type="text" class="form-control" id="article" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="newItemToggle">
                                    <label class="form-check-label" for="newItemToggle">Новый товар</label>
                                </div>
                                
                                <!-- Форма для нового товара (скрыта по умолчанию) -->
                                <div id="newItemForm" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label for="new_item_name" class="form-control-label required">Наименование товара</label>
                                        <input type="text" class="form-control" id="new_item_name" name="new_item_name">
                                        <div class="invalid-feedback">Введите наименование товара</div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="new_item_article" class="form-control-label">Артикул</label>
                                                <input type="text" class="form-control" id="new_item_article" name="new_item_article">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="new_item_unit" class="form-control-label">Единица измерения</label>
                                                <input type="text" class="form-control" id="new_item_unit" name="new_item_unit" value="шт">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="quantity" class="form-control-label required">Количество</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0.01" required>
                                    <div class="invalid-feedback">Пожалуйста, укажите количество товара</div>
                                </div>
                            </div>
                            
                            <!-- Правая колонка - информация о приемке -->
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Информация о приемке</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="supplier_id" class="form-control-label">Поставщик</label>
                                    <select class="form-control" id="supplier_id" name="supplier_id">
                                        <option value="">Выберите поставщика</option>
                                        <?php foreach ($suppliers as $id => $name): ?>
                                            <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="document_number" class="form-control-label">№ документа</label>
                                    <input type="text" class="form-control" id="document_number" name="document_number">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="price" class="form-control-label">Цена за единицу</label>
                                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="total_cost" class="form-control-label">Общая стоимость</label>
                                            <input type="number" class="form-control" id="total_cost" name="total_cost" step="0.01" min="0" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="operation_date" class="form-control-label">Дата приемки</label>
                                    <input type="datetime-local" class="form-control" id="operation_date" name="operation_date" value="<?= date('Y-m-d\TH:i') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="description" class="form-control-label">Примечание</label>
                                    <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn bg-gradient-success">
                                    <i class="fas fa-save me-2"></i> Оформить приемку
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
                <h5 class="modal-title">Товар принят на склад</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <p>Товар успешно принят на склад</p>
                <p id="successDetails" class="text-muted"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Закрыть</button>
                <a href="" class="btn bg-gradient-info" id="viewItemBtn">
                    <i class="fas fa-info-circle me-1"></i> Информация о товаре
                </a>
                <button type="button" class="btn bg-gradient-success" id="newReceptionBtn">
                    <i class="fas fa-plus me-1"></i> Еще приемка
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../layout/footer.php'; ?>

<script>
$(document).ready(function() {
    // Обработка выбора категории
    $('#category_id').on('change', function() {
        let categoryId = $(this).val();
        let itemSelect = $('#item_id');
        
        // Очищаем селект товаров
        itemSelect.empty().prop('disabled', true);
        itemSelect.append('<option value="">Загрузка...</option>');
        
        if (!categoryId) {
            itemSelect.empty().append('<option value="">Сначала выберите категорию</option>');
            return;
        }
        
        // Загружаем товары для выбранной категории
        $.ajax({
            url: '/api/items/by-category/' + categoryId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                itemSelect.empty().prop('disabled', false);
                itemSelect.append('<option value="">Выберите товар</option>');
                
                if (response.success && response.items.length > 0) {
                    response.items.forEach(function(item) {
                        itemSelect.append(`<option value="${item.id}" 
                                            data-unit="${item.unit}" 
                                            data-article="${item.article || ''}">${item.name}</option>`);
                    });
                } else {
                    itemSelect.append('<option value="">В этой категории нет товаров</option>');
                }
            },
            error: function() {
                itemSelect.empty().prop('disabled', false);
                itemSelect.append('<option value="">Ошибка загрузки товаров</option>');
            }
        });
    });
    
    // Обработка выбора товара
    $('#item_id').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        $('#unit').val(selectedOption.data('unit') || 'шт');
        $('#article').val(selectedOption.data('article') || '');
    });
    
    // Переключение формы нового товара
    $('#newItemToggle').on('change', function() {
        if ($(this).is(':checked')) {
            $('#newItemForm').show();
            $('#item_id').prop('disabled', true).val('');
            $('#unit').val('');
            $('#article').val('');
        } else {
            $('#newItemForm').hide();
            $('#item_id').prop('disabled', $('#category_id').val() === '');
            $('#new_item_name').val('');
            $('#new_item_article').val('');
            $('#new_item_unit').val('шт');
        }
    });
    
    // Расчет общей стоимости
    $('#quantity, #price').on('input', function() {
        let quantity = parseFloat($('#quantity').val()) || 0;
        let price = parseFloat($('#price').val()) || 0;
        $('#total_cost').val((quantity * price).toFixed(2));
    });
    
    // Отмена приемки
    $('#btnCancel').on('click', function() {
        window.location.href = '/maslosklad/material/<?= $warehouse['id'] ?>';
    });
    
    // Валидация и отправка формы
    $('#receptionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Проверка валидации формы
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        
        // Дополнительная валидация
        let isNewItem = $('#newItemToggle').is(':checked');
        if (isNewItem && !$('#new_item_name').val().trim()) {
            $('#new_item_name').addClass('is-invalid');
            return;
        }
        
        if (!isNewItem && !$('#item_id').val()) {
            $('#item_id').addClass('is-invalid');
            return;
        }
        
        // Сбор данных формы
        let formData = new FormData(this);
        
        // Если новый товар
        if (isNewItem) {
            formData.append('is_new_item', '1');
        }
        
        // Отправка данных
        $.ajax({
            url: '/maslosklad/material/<?= $warehouse['id'] ?>/process-reception',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Показываем модальное окно успешного завершения
                    $('#successDetails').text('Количество: ' + $('#quantity').val() + ' ' + ($('#newItemToggle').is(':checked') ? $('#new_item_unit').val() : $('#unit').val()));
                    
                    // Устанавливаем ссылку на просмотр товара
                    let itemId = response.item_id || $('#item_id').val();
                    $('#viewItemBtn').attr('href', '/maslosklad/material/<?= $warehouse['id'] ?>/item/' + itemId);
                    
                    // Показываем модальное окно
                    $('#successModal').modal('show');
                } else {
                    // Показываем сообщение об ошибке
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка',
                        text: response.message || 'Не удалось оформить приемку товара'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка',
                    text: 'Произошла ошибка при отправке данных'
                });
            }
        });
    });
    
    // Новая приемка после успешного завершения
    $('#newReceptionBtn').on('click', function() {
        // Сбрасываем форму
        $('#receptionForm').removeClass('was-validated')[0].reset();
        $('#newItemForm').hide();
        $('#item_id').prop('disabled', true).empty().append('<option value="">Сначала выберите категорию</option>');
        $('#unit').val('');
        $('#article').val('');
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