<?php /** @var array $invoice, $items, $files */ ?>
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/invoices">Счета</a></li>
            <li class="breadcrumb-item active" aria-current="page">Счёт №<?= htmlspecialchars($invoice['number']) ?></li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Счёт №<?= htmlspecialchars($invoice['number']) ?> от <?= date('d.m.Y', strtotime($invoice['date'])) ?></h4>
                    <div>
                        <a href="/invoices/edit/<?= $invoice['id'] ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Редактировать</a>
                        <form action="/invoices/delete/<?= $invoice['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Удалить счёт?');">
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Удалить</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="mb-2"><b>Юр. лицо:</b> <?= htmlspecialchars($invoice['legal_entity_name']) ?></div>
                            <div class="mb-2"><b>Комментарий:</b> <?= htmlspecialchars($invoice['comment']) ?></div>
                        </div>
                        <div class="col-md-4">
                            <b>Статусы:</b><br>
                            <span class="badge bg-<?= $invoice['status_issued'] ? 'primary' : 'secondary' ?>">Выставлен</span>
                            <?php if (!empty($invoice['date_issued'])): ?>
                                <span class="text-muted small ms-1">(<?= date('d.m.Y', strtotime($invoice['date_issued'])) ?>)</span>
                            <?php endif; ?>
                            <br>
                            <span class="badge bg-<?= $invoice['status_shipped'] ? 'info' : 'secondary' ?>">Отгружен</span>
                            <?php if (!empty($invoice['date_shipped'])): ?>
                                <span class="text-muted small ms-1">(<?= date('d.m.Y', strtotime($invoice['date_shipped'])) ?>)</span>
                            <?php endif; ?>
                            <br>
                            <span class="badge bg-<?= $invoice['status_paid'] ? 'success' : 'secondary' ?>">Оплачен</span>
                            <?php if (!empty($invoice['date_paid'])): ?>
                                <span class="text-muted small ms-1">(<?= date('d.m.Y', strtotime($invoice['date_paid'])) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h5 class="mt-4 mb-3">Позиции по счёту</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Дата выдачи</th>
                                    <th>Имущество</th>
                                    <th>Количество</th>
                                    <th>Цена</th>
                                    <th>Сумма</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Нет позиций</td></tr>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= date('d.m.Y', strtotime($item['operation_date'])) ?></td>
                                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                                            <td><?php
                                                $price = ($item['quantity'] > 0) ? ($item['total_cost'] / $item['quantity']) : 0;
                                                echo number_format($price, 2, '.', ' ');
                                            ?></td>
                                            <td><?= number_format($item['total_cost'], 2, '.', ' ') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($items)): ?>
                        <div class="mb-2">
                            <b>Итого:</b> <?= count($items) ?> наименований, на сумму <?= number_format(array_sum(array_column($items, 'total_cost')), 2, '.', ' ') ?> руб.
                        </div>
                    <?php endif; ?>
                    <h5 class="mt-4 mb-3">Файлы по счёту</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <b>Скриншот счёта</b>
                            <form id="upload-invoice-file" action="/invoices/<?= $invoice['id'] ?>/upload-file" method="post" enctype="multipart/form-data" class="mb-2">
                                <input type="hidden" name="file_type" value="invoice">
                                <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Загрузить</button>
                            </form>
                            <div id="invoice-files-list">
                            <?php foreach ($files as $file): if ($file['file_type'] !== 'invoice') continue; ?>
                                <div class="mb-2 d-flex align-items-center">
                                    <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $file['file_name'])): ?>
                                        <a href="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" target="_blank">
                                            <img src="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" alt="Скриншот счёта" style="max-width: 80px; max-height: 80px; border:1px solid #ccc; margin-right:8px;">
                                        </a>
                                    <?php elseif (preg_match('/\.pdf$/i', $file['file_name'])): ?>
                                        <a href="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" target="_blank">
                                            <i class="bi bi-file-earmark-pdf" style="font-size:2rem;color:#b00;"></i> PDF
                                        </a>
                                    <?php endif; ?>
                                    <form class="delete-file-form ms-2" data-file-id="<?= $file['id'] ?>" data-invoice-id="<?= $invoice['id'] ?>" method="post">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <b>Скриншот письма</b>
                            <form id="upload-email-file" action="/invoices/<?= $invoice['id'] ?>/upload-file" method="post" enctype="multipart/form-data" class="mb-2">
                                <input type="hidden" name="file_type" value="email">
                                <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Загрузить</button>
                            </form>
                            <div id="email-files-list">
                            <?php foreach ($files as $file): if ($file['file_type'] !== 'email') continue; ?>
                                <div class="mb-2 d-flex align-items-center">
                                    <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $file['file_name'])): ?>
                                        <a href="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" target="_blank">
                                            <img src="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" alt="Скриншот письма" style="max-width: 80px; max-height: 80px; border:1px solid #ccc; margin-right:8px;">
                                        </a>
                                    <?php elseif (preg_match('/\.pdf$/i', $file['file_name'])): ?>
                                        <a href="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" target="_blank">
                                            <i class="bi bi-file-earmark-pdf" style="font-size:2rem;color:#b00;"></i> PDF
                                        </a>
                                    <?php endif; ?>
                                    <form class="delete-file-form ms-2" data-file-id="<?= $file['id'] ?>" data-invoice-id="<?= $invoice['id'] ?>" method="post">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <b>Накладная (файл выдачи товара)</b>
                            <form id="upload-waybill-file" action="/invoices/<?= $invoice['id'] ?>/upload-file" method="post" enctype="multipart/form-data" class="mb-2">
                                <input type="hidden" name="file_type" value="waybill">
                                <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Загрузить</button>
                            </form>
                            <div id="waybill-files-list">
                            <?php foreach ($files as $file): if ($file['file_type'] !== 'waybill') continue; ?>
                                <div class="mb-2 d-flex align-items-center">
                                    <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $file['file_name'])): ?>
                                        <a href="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" target="_blank">
                                            <img src="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" alt="Накладная" style="max-width: 80px; max-height: 80px; border:1px solid #ccc; margin-right:8px;">
                                        </a>
                                    <?php elseif (preg_match('/\.pdf$/i', $file['file_name'])): ?>
                                        <a href="/files/invoice/<?= $invoice['id'] ?>/<?= rawurlencode($file['file_name']) ?>" target="_blank">
                                            <i class="bi bi-file-earmark-pdf" style="font-size:2rem;color:#b00;"></i> PDF
                                        </a>
                                    <?php endif; ?>
                                    <form class="delete-file-form ms-2" data-file-id="<?= $file['id'] ?>" data-invoice-id="<?= $invoice['id'] ?>" method="post">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery (если не подключён) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // AJAX загрузка файлов
    $('#upload-invoice-file, #upload-email-file, #upload-waybill-file').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        var fileType = $(form).find('input[name="file_type"]').val();
        var filesListId = '';
        if (fileType === 'invoice') filesListId = '#invoice-files-list';
        if (fileType === 'email') filesListId = '#email-files-list';
        if (fileType === 'waybill') filesListId = '#waybill-files-list';
        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(filesListId).html(response.html);
                    form.reset();
                } else {
                    alert(response.error || 'Ошибка загрузки файла');
                }
            },
            error: function(xhr) {
                alert('Ошибка загрузки: ' + xhr.responseText);
            }
        });
    });
    // AJAX удаление файлов
    $(document).on('submit', '.delete-file-form', function(e) {
        e.preventDefault();
        if (!confirm('Удалить файл?')) return;
        var form = this;
        var fileId = $(form).data('file-id');
        var invoiceId = $(form).data('invoice-id');
        var parentDiv = $(form).closest('div');
        $.ajax({
            url: '/invoices/' + invoiceId + '/delete-file/' + fileId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    parentDiv.remove();
                } else {
                    alert(response.error || 'Ошибка удаления файла');
                }
            },
            error: function(xhr) {
                alert('Ошибка удаления: ' + xhr.responseText);
            }
        });
    });
});
</script> 