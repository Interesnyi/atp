<div class="d-flex justify-content-center align-items-center min-vh-80">
  <div class="card shadow-sm w-100" style="max-width:700px;">
    <div class="card-body">
      <h2 class="mb-4"><?= htmlspecialchars($title ?? 'Создать заказ-наряд') ?></h2>
      <form method="post" action="/orders/store" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="customer_id" class="form-label">Заказчик</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <option value="">Выберите заказчика...</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?php if (!empty($c['is_individual'])) { echo htmlspecialchars($c['contact_person']); } else { echo htmlspecialchars($c['company_name']); } ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="car_id" class="form-label">Автомобиль</label>
                <select class="form-select" id="car_id" name="car_id" required disabled>
                    <option value="">Сначала выберите заказчика...</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="date_created" class="form-label">Дата приёма</label>
                <input type="datetime-local" class="form-control" id="date_created" name="date_created" value="<?= date('Y-m-d\TH:i') ?>">
            </div>
            <div class="col-md-6">
                <label for="manager" class="form-label">Менеджер</label>
                <select class="form-select" id="manager" name="manager">
                    <option value="">Выберите менеджера...</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= htmlspecialchars($u['username']) ?>"><?= htmlspecialchars($u['username']) ?><?php if (!empty($u['email'])): ?> (<?= htmlspecialchars($u['email']) ?>)<?php endif; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Работы</label>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:30%">Наименование</th>
                            <th style="width:10%">Кол-во</th>
                            <th style="width:15%">Цена</th>
                            <th style="width:15%">Сумма</th>
                            <th style="width:20%">Исполнитель</th>
                            <th style="width:10%"></th>
                        </tr>
                    </thead>
                    <tbody id="works-list"></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-work-btn"><i class="bi bi-plus"></i> Добавить работу</button>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Комментарий</label>
            <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Сохранить</button>
        <a href="/orders" class="btn btn-secondary">Отмена</a>
      </form>
    </div>
  </div>
</div>
<script>
$(function() {
    // Динамическая подгрузка автомобилей по заказчику
    $('#customer_id').on('change', function() {
        var customerId = $(this).val();
        var carSelect = $('#car_id');
        carSelect.prop('disabled', true).html('<option>Загрузка...</option>');
        if (!customerId) {
            carSelect.html('<option value="">Сначала выберите заказчика...</option>').prop('disabled', true);
            return;
        }
        $.get('/orders/api/cars_by_customer/' + customerId, function(data) {
            carSelect.empty();
            if (data.length === 0) {
                carSelect.append('<option value="">Нет автомобилей</option>');
            } else {
                carSelect.append('<option value="">Выберите автомобиль...</option>');
                data.forEach(function(car) {
                    var text = car.brand + ' ' + car.model + ' (' + car.license_plate + ')';
                    carSelect.append('<option value="' + car.id + '">' + text + '</option>');
                });
            }
            carSelect.prop('disabled', false);
        }, 'json');
    });

    // Динамическое добавление работ
    var workTypes = <?php echo json_encode($workTypes); ?>;
    var users = <?php echo json_encode($users); ?>;
    function renderWorkRow(idx, selected, qty, price, sum, executor) {
        var row = $('<tr class="work-row"></tr>');
        var select = $('<select class="form-select form-select-sm work-type-select" name="works['+idx+'][work_type_id]" required></select>');
        select.append('<option value="">Выберите работу...</option>');
        workTypes.forEach(function(wt) {
            var opt = $('<option></option>').val(wt.id).text(wt.name + (wt.code ? ' ('+wt.code+')' : ''));
            if (selected && selected == wt.id) opt.prop('selected', true);
            select.append(opt);
        });
        var qtyInput = $('<input type="number" min="1" step="1" class="form-control form-control-sm work-qty" name="works['+idx+'][quantity]" value="'+(qty||1)+'" required>');
        var priceInput = $('<input type="number" min="0" step="0.01" class="form-control form-control-sm work-price" name="works['+idx+'][price]" value="'+(price||'')+'" required>');
        var sumInput = $('<input type="number" min="0" step="0.01" class="form-control form-control-sm work-sum" name="works['+idx+'][total]" value="'+(sum||'')+'" readonly tabindex="-1">');
        var execSelect = $('<select class="form-select form-select-sm" name="works['+idx+'][executor]" required><option value="">Исполнитель...</option></select>');
        users.forEach(function(u) {
            var opt = $('<option></option>').val(u.username).text(u.username + (u.email ? ' ('+u.email+')' : ''));
            if (executor && executor == u.username) opt.prop('selected', true);
            execSelect.append(opt);
        });
        var removeBtn = $('<button type="button" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>');
        removeBtn.on('click', function() { row.remove(); });
        row.append($('<td>').append(select));
        row.append($('<td>').append(qtyInput));
        row.append($('<td>').append(priceInput));
        row.append($('<td>').append(sumInput));
        row.append($('<td>').append(execSelect));
        row.append($('<td>').append(removeBtn));
        // Автоматический пересчёт суммы
        function recalcSum() {
            var q = parseFloat(qtyInput.val()) || 0;
            var p = parseFloat(priceInput.val()) || 0;
            sumInput.val((q*p).toFixed(2));
        }
        qtyInput.on('input', recalcSum);
        priceInput.on('input', recalcSum);
        select.on('change', function() {
            var wt = workTypes.find(function(w){return w.id==select.val();});
            if (wt && wt.price) priceInput.val(wt.price);
            recalcSum();
        });
        recalcSum();
        return row;
    }
    var workIdx = 0;
    $('#add-work-btn').on('click', function() {
        $('#works-list').append(renderWorkRow(workIdx++));
    });
});
</script> 