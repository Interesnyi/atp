<div class="d-flex justify-content-center align-items-center min-vh-80">
  <div class="card shadow-sm w-100" style="max-width:700px;">
    <div class="card-body">
      <h2 class="mb-4"><?= htmlspecialchars($title ?? 'Редактировать заказ-наряд') ?></h2>
      <form method="post" action="/orders/update/<?= $order['id'] ?>" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="customer_id" class="form-label">Заказчик</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <option value="">Выберите заказчика...</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $order['customer_id'] == $c['id'] ? 'selected' : '' ?>><?php if (!empty($c['is_individual'])) { echo htmlspecialchars($c['contact_person']); } else { echo htmlspecialchars($c['company_name']); } ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="car_id" class="form-label">Автомобиль</label>
                <input type="text" class="form-control" id="car_id" name="car_id" value="<?= htmlspecialchars($order['car_id']) ?>" required>
            </div>
            <div class="col-md-4">
                <label for="order_number" class="form-label">Номер заказ-наряда</label>
                <input type="text" class="form-control" id="order_number" name="order_number" value="<?= htmlspecialchars($order['order_number']) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="date_created" class="form-label">Дата приёма</label>
                <input type="datetime-local" class="form-control" id="date_created" name="date_created" value="<?= date('Y-m-d\TH:i', strtotime($order['date_created'])) ?>">
            </div>
            <div class="col-md-4">
                <label for="manager" class="form-label">Менеджер</label>
                <input type="text" class="form-control" id="manager" name="manager" value="<?= htmlspecialchars($order['manager']) ?>">
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Статус</label>
                <select class="form-select" id="status" name="status">
                    <option value="new" <?= $order['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                    <option value="in_progress" <?= $order['status'] == 'in_progress' ? 'selected' : '' ?>>В работе</option>
                    <option value="done" <?= $order['status'] == 'done' ? 'selected' : '' ?>>Выполнен</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Комментарий</label>
            <textarea class="form-control" id="comment" name="comment" rows="2"><?= htmlspecialchars($order['comment']) ?></textarea>
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
        <button type="submit" class="btn btn-success">Сохранить</button>
        <a href="/orders/view/<?= $order['id'] ?>" class="btn btn-secondary">Отмена</a>
      </form>
    </div>
  </div>
</div>

<script>
$(function() {
    var workTypes = <?php echo json_encode($workTypes); ?>;
    var users = <?php echo json_encode($users); ?>;
    var works = <?php echo json_encode($works); ?>;
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
    // Рендерим уже сохранённые работы
    if (Array.isArray(works)) {
        works.forEach(function(w) {
            $('#works-list').append(renderWorkRow(workIdx++, w.work_type_id, w.quantity, w.price, w.total, w.executor));
        });
    }
    $('#add-work-btn').on('click', function() {
        $('#works-list').append(renderWorkRow(workIdx++));
    });
});
</script> 