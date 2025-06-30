<div class="d-flex justify-content-center align-items-center min-vh-80">
  <div class="card shadow-sm w-100" style="max-width:900px;">
    <div class="card-body">
        <h2>Заказ-наряд №<?= htmlspecialchars($order['order_number']) ?></h2>
        <div class="mb-3">
            <b>Дата приёма:</b> <?= htmlspecialchars($order['date_created']) ?>
            <b>Статус:</b> <?= htmlspecialchars($order['status']) ?>
            <b>Менеджер:</b> <?= htmlspecialchars($order['manager']) ?>
        </div>
        <?php if (!empty($order['contract_number'])): ?>
        <div class="mb-3">
            <b>Договор:</b> №<?= htmlspecialchars($order['contract_number']) ?> от <?= htmlspecialchars($order['contract_date']) ?>
        </div>
        <?php endif; ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <h5>Заказчик</h5>
                <div><?= htmlspecialchars($order['customer_name']) ?></div>
            </div>
            <div class="col-md-6">
                <h5>Автомобиль</h5>
                <div><?= htmlspecialchars($order['brand']) ?> <?= htmlspecialchars($order['model']) ?> (<?= htmlspecialchars($order['license_plate']) ?>)</div>
            </div>
        </div>
        <h5>Работы</h5>
        <table class="table table-bordered table-sm">
            <thead><tr><th>Наименование</th><th>Кол-во</th><th>Цена</th><th>Сумма</th><th>Исполнитель</th></tr></thead>
            <tbody>
            <?php foreach ($works as $w): ?>
                <tr>
                    <td><?= htmlspecialchars($w['name']) ?></td>
                    <td><?= htmlspecialchars($w['quantity']) ?></td>
                    <td><?= htmlspecialchars($w['price']) ?></td>
                    <td><?= htmlspecialchars($w['total']) ?></td>
                    <td><?= htmlspecialchars($w['executor']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="3" class="text-end">ИТОГО</th>
                <th colspan="2">
                    <?php
                    $totalWorks = 0;
                    foreach ($works as $w) {
                        $totalWorks += (float)$w['total'];
                    }
                    echo number_format($totalWorks, 2, '.', ' ');
                    ?>
                </th>
            </tr>
            </tfoot>
        </table>
        <h4 class="mt-4">Запчасти</h4>
        <?php if (!empty($orderParts)): ?>
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Наименование</th>
                    <th>Артикул</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orderParts as $part): ?>
                <tr>
                    <td><?= htmlspecialchars($part['name']) ?></td>
                    <td><?= htmlspecialchars($part['article']) ?></td>
                    <td><?= htmlspecialchars($part['quantity']) ?></td>
                    <td><?= number_format($part['price'], 2, '.', ' ') ?></td>
                    <td><?= number_format($part['total'], 2, '.', ' ') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted">Нет запчастей</p>
        <?php endif; ?>
        <h5>Материалы исполнителя</h5>
        <table class="table table-bordered table-sm">
            <thead><tr><th>Наименование</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr></thead>
            <tbody>
            <?php foreach ($materials as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['quantity']) ?></td>
                    <td><?= htmlspecialchars($m['price']) ?></td>
                    <td><?= htmlspecialchars($m['total']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h5>Материалы заказчика</h5>
        <table class="table table-bordered table-sm">
            <thead><tr><th>Наименование</th><th>Кол-во</th></tr></thead>
            <tbody>
            <?php foreach ($customerMaterials as $cm): ?>
                <tr>
                    <td><?= htmlspecialchars($cm['name']) ?></td>
                    <td><?= htmlspecialchars($cm['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h5>Файлы</h5>
        <ul>
            <?php foreach ($files as $f): ?>
                <li><a href="<?= htmlspecialchars($f['file_path']) ?>" target="_blank"><?= htmlspecialchars($f['file_name']) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <a href="/orders" class="btn btn-secondary mt-3">Назад к списку</a>
        <a href="/orders/edit/<?= $order['id'] ?>" class="btn btn-primary mt-3">Редактировать</a>
        <a href="/orders/download/<?= $order['id'] ?>" class="btn btn-outline-success mt-3">Скачать Word</a>
    </div>
  </div>
</div> 