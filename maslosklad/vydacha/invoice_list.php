<?php
    
session_start();

if (!isset($_SESSION["id"])) {
    header("location: /");
    exit;
}

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- AlertifyJS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    
    <title>ELDIR | МаслоСклад | Список счетов</title>
    
    <style>
        .card-header {
            cursor: pointer;
        }
        
        .invoice-header {
            transition: background-color 0.3s;
        }
        
        .invoice-header:hover {
            background-color: #f8f9fa;
        }
        
        .invoice-details {
            margin-top: 15px;
        }
        
        .table-sm td, .table-sm th {
            padding: 0.25rem 0.5rem;
        }
        
        .badge-total {
            font-size: 1rem;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }
        
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }
    </style>
</head>
<body>
    <? require_once('../../nav.php'); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1>ELDIR | МаслоСклад | Список счетов</h1>
                <p>На этой странице представлен список всех счетов с возможностью просмотра детализации</p>
            </div>
        </div>
        <? require_once('../nav_sklad.php'); ?>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Фильтры</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" id="filterForm">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label for="filter_invoice" class="form-label">Номер счёта</label>
                                    <input type="text" name="invoice_number" id="filter_invoice" class="form-control" value="<?= isset($_GET['invoice_number']) ? htmlspecialchars($_GET['invoice_number']) : '' ?>">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="filter_buyer" class="form-label">Покупатель</label>
                                    <select name="buyers_id" id="filter_buyer" class="form-select">
                                        <option value="">Все покупатели</option>
                                        <?php
                                            require __DIR__ . '/../../dbcon.php';

                                            $query = "SELECT mb.id, mb.name 
                                            FROM maslosklad_buyers mb
                                            WHERE mb.is_deleted = 0";
                                            $query_run = mysqli_query($con, $query);

                                            if(mysqli_num_rows($query_run) > 0)
                                            {
                                                foreach($query_run as $object)
                                                {
                                                    $selected = (isset($_GET['buyers_id']) && $_GET['buyers_id'] == $object['id']) ? 'selected' : '';
                                        ?>
                                                    <option value="<?=$object['id']?>" <?=$selected?>><?=$object['name']?></option>
                                        <?php
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="filter_date_from" class="form-label">Дата с</label>
                                    <input type="date" name="date_from" id="filter_date_from" class="form-control" value="<?= isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : '' ?>">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="filter_date_to" class="form-label">Дата по</label>
                                    <input type="date" name="date_to" id="filter_date_to" class="form-control" value="<?= isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : '' ?>">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary">Применить фильтры</button>
                                    <a href="invoice_list.php" class="btn btn-secondary">Сбросить</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="accordion" id="invoiceAccordion">
                <?php
                    require __DIR__ . '/../../dbcon.php';
                    
                    $buyers_id = isset($_REQUEST['buyers_id']) && $_REQUEST['buyers_id'] != '' ? mysqli_real_escape_string($con, $_REQUEST['buyers_id']) : '';
                    $invoice_number = isset($_REQUEST['invoice_number']) && $_REQUEST['invoice_number'] != '' ? mysqli_real_escape_string($con, $_REQUEST['invoice_number']) : '';
                    $date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? mysqli_real_escape_string($con, $_REQUEST['date_from']) : '';
                    $date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? mysqli_real_escape_string($con, $_REQUEST['date_to']) : '';
                    
                    // Запрос для получения уникальных номеров счетов с общей информацией
                    $invoices_query = "SELECT DISTINCT s.invoice_number, 
                                      MIN(s.date_operations) as first_date, 
                                      MAX(s.date_operations) as last_date,
                                      SUM(s.summa) as total_sum,
                                      COUNT(*) as items_count,
                                      MIN(mb.name) as buyer_name,
                                      MIN(s.buyers_id) as buyer_id
                                FROM `maslosklad` s 
                                LEFT JOIN `maslosklad_buyers` mb ON s.buyers_id = mb.id
                                WHERE s.is_deleted = 0 
                                AND s.sign_of_calculation = 'vydacha'
                                AND s.invoice_number IS NOT NULL 
                                AND s.invoice_number != ''";
                    
                    // Добавляем условия фильтрации
                    if ($buyers_id != '') {
                        $invoices_query .= " AND s.buyers_id = '$buyers_id'";
                    }
                    
                    if ($invoice_number != '') {
                        $invoices_query .= " AND s.invoice_number LIKE '%$invoice_number%'";
                    }
                    
                    if ($date_from != '') {
                        $invoices_query .= " AND s.date_operations >= '$date_from'";
                    }
                    
                    if ($date_to != '') {
                        $invoices_query .= " AND s.date_operations <= '$date_to'";
                    }
                    
                    $invoices_query .= " GROUP BY s.invoice_number ORDER BY first_date DESC";
                    
                    $invoices_result = mysqli_query($con, $invoices_query);
                    
                    if($invoices_result && mysqli_num_rows($invoices_result) > 0) {
                        $accordionIndex = 0;
                        
                        while($invoice = mysqli_fetch_assoc($invoices_result)) {
                            // Форматируем даты
                            $first_date = date('d.m.Y', strtotime($invoice['first_date']));
                            $last_date = date('d.m.Y', strtotime($invoice['last_date']));
                            
                            // Форматируем общую сумму
                            $total_sum = number_format($invoice['total_sum'], 2, '.', ' ');
                ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $accordionIndex ?>">
                            <button class="accordion-button <?= $accordionIndex > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $accordionIndex ?>" aria-expanded="<?= $accordionIndex === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $accordionIndex ?>">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <span class="me-3"><strong>Счет №<?= htmlspecialchars($invoice['invoice_number']) ?></strong></span>
                                        <span class="text-muted"><?= $first_date === $last_date ? $first_date : $first_date . ' - ' . $last_date ?></span>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary badge-total"><?= $total_sum ?> руб.</span>
                                        <span class="badge bg-secondary ms-2"><?= $invoice['items_count'] ?> позиций</span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $accordionIndex ?>" class="accordion-collapse collapse <?= $accordionIndex === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $accordionIndex ?>" data-bs-parent="#invoiceAccordion">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <p>
                                            <strong>Покупатель:</strong> <?= htmlspecialchars($invoice['buyer_name']) ?><br>
                                            <strong>Дата<?= $first_date !== $last_date ? 'ы' : '' ?>:</strong> <?= $first_date === $last_date ? $first_date : $first_date . ' - ' . $last_date ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="export_invoice.php?invoice_number=<?= htmlspecialchars($invoice['invoice_number']) ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-file-export"></i> Экспорт счета
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Дата</th>
                                                <th>Наименование</th>
                                                <th>Тип</th>
                                                <th>Кол-во</th>
                                                <th>Сумма</th>
                                                <th>Тип оплаты</th>
                                                <th>Статус</th>
                                                <th>Комментарий</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Запрос детализации по счету
                                            $invoice_num = $invoice['invoice_number'];
                                            $details_query = "SELECT s.id, s.date_operations, sp.name as property_name, 
                                                            spt.name as type_name, s.count, s.summa,
                                                            IF(s.status > 0, 'Оплачен', 'Не оплачен') AS status, 
                                                            IF(s.type_pay > 0, 'безнал', 'нал') AS type_pay,
                                                            s.comment
                                                        FROM `maslosklad` s 
                                                        LEFT JOIN `maslosklad_property` sp ON s.property_id = sp.id 
                                                        LEFT JOIN `maslosklad_property_type` spt ON sp.type = spt.id
                                                        WHERE s.is_deleted = 0 
                                                        AND s.sign_of_calculation = 'vydacha' 
                                                        AND s.invoice_number = '$invoice_num'
                                                        ORDER BY s.date_operations";
                                            
                                            $details_result = mysqli_query($con, $details_query);
                                            
                                            if($details_result && mysqli_num_rows($details_result) > 0) {
                                                while($detail = mysqli_fetch_assoc($details_result)) {
                                                    // Форматируем дату
                                                    $date_formatted = date('d.m.Y', strtotime($detail['date_operations']));
                                                    
                                                    // Форматируем сумму
                                                    $sum_formatted = number_format($detail['summa'], 2, '.', ' ');
                                            ?>
                                                <tr>
                                                    <td><?= $date_formatted ?></td>
                                                    <td><?= htmlspecialchars($detail['property_name']) ?></td>
                                                    <td><?= htmlspecialchars($detail['type_name']) ?></td>
                                                    <td><?= $detail['count'] ?></td>
                                                    <td><?= $sum_formatted ?> руб.</td>
                                                    <td><?= $detail['type_pay'] ?></td>
                                                    <td><?= $detail['status'] ?></td>
                                                    <td><?= htmlspecialchars($detail['comment']) ?></td>
                                                </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="8" class="text-center">Нет данных для отображения</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-primary">
                                                <td colspan="4" class="text-end"><strong>Итого:</strong></td>
                                                <td><strong><?= $total_sum ?> руб.</strong></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                            $accordionIndex++;
                        }
                    } else {
                        echo '<div class="alert alert-info">Счета не найдены. Попробуйте изменить параметры фильтрации.</div>';
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript - в правильном порядке -->
    <!-- jQuery - самым первым -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Select2 сразу после jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- Bootstrap после Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Остальные библиотеки -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <script>
        $(document).ready(function () {
            // Настройки для Select2
            var select2Options = {
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Выберите из списка',
                allowClear: true
            };
            
            // Инициализация Select2 для выпадающих списков
            if (typeof $.fn.select2 === 'function') {
                $('#filter_buyer').select2(select2Options);
            }
            
            // Открываем первый аккордеон по умолчанию
            //new bootstrap.Collapse(document.getElementById('collapse0'), { toggle: true });
            
            // Подсветка строк таблицы при наведении
            $('tbody tr').hover(
                function() { $(this).addClass('table-hover'); },
                function() { $(this).removeClass('table-hover'); }
            );
        });
    </script>
</body>
</html> 