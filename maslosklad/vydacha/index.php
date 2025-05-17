<?
    
session_start();

if ( !isset($_SESSION["id"])) {
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

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css"/>
    
    <!-- AlertifyJS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    
    <!-- Select2 CSS - последняя версия -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <title>ELDIR | МаслоСклад | Выдача имущества</title>
    
    <!-- Дополнительные стили -->
    <style>
        .dt-buttons {
            margin-bottom: 10px;
        }
        .buttons-columnVisibility {
            text-align: left;
            display: block;
            width: 100%;
        }
        .dt-button-collection {
            max-height: 300px;
            overflow-y: auto;
            padding: 8px;
        }
        .dataTables_wrapper .btn {
            margin: 2px;
        }
        .dataTables_filter {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <? require_once('../../nav.php'); ?>
    
    <!-- Add Info Modal -->
    <div class="modal fade" id="infoAddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Выдача имущества</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saveForm">
                <div class="modal-body">

                    <div id="errorMessage" class="alert alert-warning d-none"></div>
                    
                    <div class="mb-3">
                        <label for="">Дата выдачи</label>
                        <input type="date" name="date_operations" class="form-control" />
                    </div>
                    
                     <div class="mb-3">
                        <label for="type_id">Наименование покупателя</label>
                        <select name="buyers_id" class="form-select">
                            <option selected>Выберите покупателя из списка</option>
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
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Наименование имущества</label>
                        <select name="property_id" class="form-select">
                            <option selected>Выберите наименование имущества из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT sp.id, spt.name as type, sp.name 
                                FROM maslosklad_property sp
                                LEFT JOIN maslosklad_property_type spt ON sp.type = spt.id
                                WHERE sp.is_deleted = 0";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['type'].' - '.$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Количество</label>
                        <input type="text" name="count" class="form-control" />
                    </div>
                    
          
                    
                    <div class="mb-3">
                        <label for="status">Статус</label>
                        <select name="status" class="form-select">
                            <option selected>Выберите статус из списка</option>
                            <option value="0">Не оплачен</option>
                            <option value="1">Оплачен</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_pay">Тип оплаты</label>
                        <select name="type_pay" class="form-select">
                            <option selected>Выберите тип оплаты из списка</option>
                            <option value="0">Нал</option>
                            <option value="1">Безнал</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="summa">Сумма</label>
                        <input type="text" name="summa" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Комментарий</label>
                        <input type="text" name="comment" class="form-control" />
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="write_off">
                        <label class="form-check-label" for="write-off">Списание с баланса</label>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Edit Info Modal -->
    <div class="modal fade" id="infoEditModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Изменить данные о имуществе</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateInfo">
                <div class="modal-body">

                    <div id="errorMessageUpdate" class="alert alert-warning d-none"></div>

                    <input type="hidden" name="info_id" id="info_id" >
                    
                    <div class="mb-3">
                        <label for="">Дата выдачи</label>
                        <input type="date" name="date_operations" id="date_operations" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Наименование покупателя</label>
                        <select name="buyers_id" id="buyers_id" class="form-select">
                            <option selected>Выберите покупателя из списка</option>
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
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Наименование имущества</label>
                        <select name="name_property" id="name_property" class="form-select">
                            <option selected>Выберите наименование имущества из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT sp.id, spt.name as type, sp.name 
                                FROM maslosklad_property sp
                                LEFT JOIN maslosklad_property_type spt ON sp.type = spt.id
                                WHERE sp.is_deleted = 0";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['type'].' - '.$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Количество</label>
                        <input type="text" name="count" id="count" class="form-control" />
                    </div>
                   
                    
                    <div class="mb-3">
                        <label for="status">Статус</label>
                        <select name="status" id="status" class="form-select">
                            <option selected>Выберите статус из списка</option>
                            <option value="0">Не оплачен</option>
                            <option value="1">Оплачен</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_pay">Тип оплаты</label>
                        <select name="type_pay" id="type_pay" class="form-select">
                            <option selected>Выберите тип оплаты из списка</option>
                            <option value="0">Нал</option>
                            <option value="1">Безнал</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="invoice_number">Номер счёта</label>
                        <input type="text" name="invoice_number" id="invoice_number" class="form-control" />
                    </div>
                    
                    <div>
                        <label for="summa">Сумма</label>
                        <input type="text" name="summa" id="summa" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Комментарий</label>
                        <input type="text" name="comment" id="comment" class="form-control" />
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="write_off" id="write_off" value="">
                        <label class="form-check-label" for="write-off">Списание с баланса</label>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    
    <!-- Relocation Modal -->
    <div class="modal fade" id="relocationModal" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Перемещение имущества</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="relocationInfo">
                <div class="modal-body">

                    <div id="errorMessageRelocation" class="alert alert-warning d-none"></div>

                    <input type="hidden" name="info_relocation_id" id="info_relocation_id" >
                    
                    <div class="mb-3">
                        <label for="">Дата перемещения</label>
                        <input type="date" name="date_operations_relocation" id="date_operations_relocation" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Куда переместилась</label>
                        <select name="place_id" id="place_id" class="form-select">
                            <option selected>Выберите место из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT mpp.id, mpp.name 
                                FROM maslosklad_property_place mpp
                                WHERE mpp.is_deleted = 0";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
            </div>
        </div>
    </div>
  
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1>ELDIR | МаслоСклад | Выдача Имущества </h1>
            </div>
        </div>
        <? require_once('../nav_sklad.php'); ?>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#infoAddModal">
                    Выдача имущества
                </button>
                <a href="invoice_list.php" class="btn btn-info ms-2">
                    <i class="fas fa-file-invoice"></i> Список счетов
                </a>
            </div>
        </div>
        
        <!-- Форма с фильтрами -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Фильтры</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" id="filterForm">
                            <div class="row">
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
                                    <label for="filter_invoice" class="form-label">Номер счёта</label>
                                    <input type="text" name="invoice_number" id="filter_invoice" class="form-control" value="<?= isset($_GET['invoice_number']) ? htmlspecialchars($_GET['invoice_number']) : '' ?>">
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
                                    <a href="index.php" class="btn btn-secondary">Сбросить</a>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Блок для отображения итоговой суммы по счету -->
                        <?php if(isset($_GET['invoice_number']) && !empty($_GET['invoice_number'])): ?>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info d-flex justify-content-between align-items-center" id="invoice_total_info">
                                    <div>
                                    <?php
                                    $invoice_num = mysqli_real_escape_string($con, $_GET['invoice_number']);
                                    $total_query = "SELECT SUM(s.summa) as total_sum 
                                                  FROM `maslosklad` s 
                                                  WHERE s.is_deleted = 0 
                                                  AND s.sign_of_calculation = 'vydacha' 
                                                  AND s.invoice_number = '$invoice_num'";
                                    $total_result = mysqli_query($con, $total_query);
                                    if($total_result && $total_row = mysqli_fetch_assoc($total_result)) {
                                        echo '<strong>Итоговая сумма по счету №' . htmlspecialchars($invoice_num) . ':</strong> ' . 
                                              '<span class="fs-5">' . number_format($total_row['total_sum'], 2, '.', ' ') . ' руб.</span>';
                                    }
                                    ?>
                                    </div>
                                    <div>
                                        <a href="export_invoice.php?invoice_number=<?= htmlspecialchars($invoice_num) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-export"></i> Экспорт счета
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="table-responsive-sm">
                    <table id="myTable" class=" table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th style="display: none;">Дата (сортировка)</th>
                                <th>Номер счёта</th>
                                <th>Покупатель</th>
                                <th>Наименование</th>
                                <th>Тип</th>
                                <th>Кол-во</th>
                                <th>Сумма</th>
                                <th>Тип оплаты</th>
                                <th>Статус</th>
                                <th>Комментарий</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require __DIR__ . '/../../dbcon.php';
                            
                            $buyers_id = isset($_REQUEST['buyers_id']) && $_REQUEST['buyers_id'] != '' ? mysqli_real_escape_string($con, $_REQUEST['buyers_id']) : '';
                            $invoice_number = isset($_REQUEST['invoice_number']) && $_REQUEST['invoice_number'] != '' ? mysqli_real_escape_string($con, $_REQUEST['invoice_number']) : '';
                            $date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? mysqli_real_escape_string($con, $_REQUEST['date_from']) : '';
                            $date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? mysqli_real_escape_string($con, $_REQUEST['date_to']) : '';
                            
                            $query = "SELECT s.id as id, sp.article, s.relocation, s.write_off, sp.name as property_name, s.summa as summa, spt.name as type_name, s.count, s.date_operations, spp.name as place, s.comment, s.invoice_number, IF(s.status > 0, 'Оплачен', 'Не оплачен') AS status, IF(s.type_pay > 0, 'безнал', 'нал') AS type_pay, mb.name as buyers_name 
                                        FROM `maslosklad` s 
                                        LEFT JOIN `maslosklad_property` sp ON s.property_id = sp.id 
                                        LEFT JOIN `maslosklad_property_type` spt ON sp.type = spt.id
                                        LEFT JOIN `maslosklad_property_place` spp ON s.place_id = spp.id
                                        LEFT JOIN `maslosklad_buyers` mb ON s.buyers_id = mb.id
                                        WHERE s.is_deleted = 0 AND s.sign_of_calculation = 'vydacha'";
                                        
                            // Добавляем условия фильтрации
                            if ($buyers_id != '') {
                                $query .= " AND s.buyers_id = '$buyers_id'";
                            }
                            
                            if ($invoice_number != '') {
                                $query .= " AND s.invoice_number LIKE '%$invoice_number%'";
                            }
                            
                            if ($date_from != '') {
                                $query .= " AND s.date_operations >= '$date_from'";
                            }
                            
                            if ($date_to != '') {
                                $query .= " AND s.date_operations <= '$date_to'";
                            }
                            
                            // Добавляем сортировку по дате
                            $query .= " ORDER BY s.date_operations DESC";
                                        
                            $query_run = mysqli_query($con, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {
                                foreach($query_run as $property)
                                {   
                                    // Форматирование даты в удобный формат (ДД.ММ.ГГГГ)
                                    $date_formatted = date('d.m.Y', strtotime($property['date_operations']));
                                    ?>
                                    <tr <?= $property['relocation'] == 1 ? 'class="table-primary"': '' ?> <?= $property['write_off'] == 1 ? 'class="table-danger"': '' ?> >
                                        <td><?= $date_formatted ?></td>
                                        <td style="display: none;"><?= $property['date_operations'] ?></td>
                                        <td><?= $property['invoice_number'] ?></td>
                                        <td><?= $property['buyers_name'] ?></td>
                                        <td><?= $property['property_name'] ?></td>
                                        <td><?= $property['type_name'] ?></td>
                                        <td><?= $property['count'] ?></td>
                                        <td><?= $property['summa'] ?></td>
                                        <td><?= $property['type_pay'] ?></td>
                                        <td><?= $property['status'] ?></td>
                                        <td><?= $property['comment'] ?></td>
                                        <td>
                                            <button type="button" value="<?=$property['id'];?>" class="relocationInfoBtn btn btn-primary btn-sm">Перемещение</button>
                                            <button type="button" value="<?=$property['id'];?>" class="editInfoBtn btn btn-success btn-sm">Изменить</button>
                                            <button type="button" value="<?=$property['id'];?>" class="deleteInfoBtn btn btn-danger btn-sm">Удалить</button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
         
        </div>
    </div>
    
    <!-- JavaScript - в правильном порядке -->
    <!-- jQuery - самым первым -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Select2 сразу после jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    <!-- Bootstrap после Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Остальные библиотеки -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function () {
            // Настройки для Select2
            var select2Options = {
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Выберите из списка',
                allowClear: true
            };
            
            // Проверка, что Select2 загружен
            if (typeof $.fn.select2 === 'function') {
                console.log('Select2 загружен успешно');
            } else {
                console.error('Select2 не загружен!');
                return; // Не продолжаем, если Select2 не загружен
            }
            
            // Инициализация фильтров Select2
            $('#filter_buyer').select2(select2Options);
            
            // Инициализация DataTable
            var table = $('#myTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ru.json',
                    buttons: {
                        colvis: 'Выбрать столбцы',
                        colvisRestore: 'Восстановить'
                    }
                },
                pageLength: 25,
                order: [[1, 'desc']], // Сортировка по скрытой колонке с датой
                responsive: true,
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rtip', // Улучшенная структура DOM
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="fas fa-cog"></i> Настройка столбцов',
                        className: 'btn btn-outline-primary',
                        titleAttr: 'Настроить видимость столбцов',
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // Указываем индексы столбцов, которые можно скрыть
                        columnText: function(dt, idx, title) {
                            // Более понятные названия столбцов в меню
                            var customNames = {
                                0: 'Дата',
                                1: 'Дата (сортировка)',
                                2: 'Номер счёта',
                                3: 'Покупатель',
                                4: 'Наименование',
                                5: 'Тип',
                                6: 'Количество',
                                7: 'Сумма',
                                8: 'Тип оплаты',
                                9: 'Статус',
                                10: 'Комментарий'
                            };
                            return customNames[idx] || title;
                        },
                        postfixButtons: [
                            {
                                extend: 'colvisRestore',
                                text: 'Показать все',
                                className: 'btn-sm'
                            }
                        ]
                    }
                ],
                // Добавляем возможность сохранить настройки видимости столбцов в localStorage
                stateSave: true,
                stateDuration: 60 * 60 * 24 * 30, // 30 дней в секундах
                stateSaveCallback: function(settings, data) {
                    localStorage.setItem('masloskad_vydacha_table_state', JSON.stringify(data));
                },
                stateLoadCallback: function(settings) {
                    return JSON.parse(localStorage.getItem('masloskad_vydacha_table_state'));
                },
                columnDefs: [
                    { 
                        targets: 1, // Скрытая колонка с датой
                        visible: false,
                        searchable: false
                    },
                    { 
                        targets: 11, // Колонка с кнопками
                        orderable: false, 
                        searchable: false 
                    }
                ]
            });
            
            // Добавляем FontAwesome если не добавлен
            if (typeof FontAwesome === 'undefined') {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
                document.head.appendChild(link);
            }
            
            // Общие настройки для всех Select2
            var select2Options = {
                width: '100%',
                theme: 'bootstrap-5',
                placeholder: "Выберите из списка",
                allowClear: true,
                language: {
                    errorLoading: function () { return 'Результаты не могут быть загружены.'; },
                    inputTooLong: function (args) {
                        var overChars = args.input.length - args.maximum;
                        return 'Пожалуйста, удалите ' + overChars + ' символ(ов)';
                    },
                    inputTooShort: function (args) {
                        var remainingChars = args.minimum - args.input.length;
                        return 'Пожалуйста, введите еще ' + remainingChars + ' или более символов';
                    },
                    loadingMore: function () { return 'Загрузка данных…'; },
                    maximumSelected: function (args) {
                        return 'Вы можете выбрать не более ' + args.maximum + ' элемент(ов)';
                    },
                    noResults: function () { return 'Ничего не найдено'; },
                    searching: function () { return 'Поиск…'; },
                    removeAllItems: function () { return 'Удалить все элементы'; }
                }
            };

            // Инициализация Select2 для обычных выпадающих списков
            try {
                $('select').select2(select2Options);
                console.log('Select2 применен к селектам');
            } catch (e) {
                console.error('Ошибка при инициализации Select2:', e);
            }
            
            // Специальная обработка для модальных окон
            $(document).on('shown.bs.modal', function (e) {
                var modal = $(e.target);
                
                // Применяем Select2 к выпадающим спискам в модальном окне
                try {
                    modal.find('select').each(function() {
                        // Если Select2 уже инициализирован, уничтожаем его
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2('destroy');
                        }
                        
                        // Создаем копию основных настроек
                        var modalOptions = $.extend({}, select2Options, {
                            dropdownParent: modal
                        });
                        
                        // Заново инициализируем с правильным родителем
                        $(this).select2(modalOptions);
                    });
                } catch (e) {
                    console.error('Ошибка при инициализации Select2 в модальном окне:', e);
                }
            });
      
        
        /* Добавление Имущества */
        $(document).on('submit', '#saveForm', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("save_info", true);

            $.ajax({
                type: "POST",
                url: "code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    
                    var res = jQuery.parseJSON(response);
                    if(res.status == 422) {
                        $('#errorMessage').removeClass('d-none');
                        $('#errorMessage').text(res.message);

                    }else if(res.status == 200){

                        $('#errorMessage').addClass('d-none');
                        $('#infoAddModal').modal('hide');
                        $('#saveForm')[0].reset();

                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);

                        $('#myTable').load(location.href + " #myTable");
                        
                        location.reload();

                    }else if(res.status == 500) {
                        alert(res.message);
                    }
                }
            });

        });
        
        /* Перемещение Имущества */
        $(document).on('click', '.relocationInfoBtn', function () {

            var info_relocation_id = $(this).val();
       
            $.ajax({
                type: "GET",
                url: "code.php?info_relocation_id=" + info_relocation_id,
                success: function (response) {

                    var res = jQuery.parseJSON(response);
                    if(res.status == 404) {

                        alert(res.message);
                    }else if(res.status == 200){
                        
                        $('#date_operations_relocation').val(res.data.date_operations);
                        $('#info_relocation_id').val(res.data.id);
                        $('#place_id').val(res.data.place_id);
                        
                        $('#relocationModal').modal('show');
                    }

                }
            });

        });
        
        /* Обновление информации о Перемещение Имуществе */
        $(document).on('submit', '#relocationInfo', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("relocation_info", true);

            $.ajax({
                type: "POST",
                url: "code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    
                    var res = jQuery.parseJSON(response);
                    if(res.status == 422) {
                        $('#errorMessageRelocation').removeClass('d-none');
                        $('#errorMessageRelocation').text(res.message);

                    }else if(res.status == 200){

                        $('#errorMessageRelocation').addClass('d-none');

                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);
                        
                        $('#relocationModal').modal('hide');
                        $('#relocationInfo')[0].reset();

                        $('#myTable').load(location.href + " #myTable");
                        
                        location.reload();

                    }else if(res.status == 500) {
                        alert(res.message);
                    }
                }
            });

        });
        
        /* Редактирование Имущества */
        $(document).on('click', '.editInfoBtn', function () {

            var info_id = $(this).val();
          
            $.ajax({
                type: "GET",
                url: "code.php?info_id=" + info_id,
                success: function (response) {

                    var res = jQuery.parseJSON(response);
                    if(res.status == 404) {

                        alert(res.message);
                    }else if(res.status == 200){
                        console.log(res.data);
                        $('#info_id').val(res.data.id);
                        $('#date_operations').val(res.data.date_operations);
                        $('#buyers_id').val(res.data.buyers_id);
                        $('#name_property').val(res.data.property_id);
                        $('#count').val(res.data.count);
                        $('#comment').val(res.data.comment);
                        $('#summa').val(res.data.summa);
                        $('#status').val(res.data.status);
                        $('#type_pay').val(res.data.type_pay);
                        $('#invoice_number').val(res.data.invoice_number);
                        
                        if(res.data.write_off == 1){
                            $('#write_off').prop('checked',true); 
                        }else
                        {
                            $('#write_off').prop('checked',false);    
                        }
                        
                        $('#infoEditModal').modal('show');
                    }

                }
            });

        });
        
          /* Обновление информации о Имуществе */
        $(document).on('submit', '#updateInfo', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("update_info", true);

            $.ajax({
                type: "POST",
                url: "code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    
                    var res = jQuery.parseJSON(response);
                    if(res.status == 422) {
                        $('#errorMessageUpdate').removeClass('d-none');
                        $('#errorMessageUpdate').text(res.message);

                    }else if(res.status == 200){

                        $('#errorMessageUpdate').addClass('d-none');

                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);
                        
                        $('#infoEditModal').modal('hide');
                        $('#updateInfo')[0].reset();

                        $('#myTable').load(location.href + " #myTable");
                        
                        location.reload();

                    }else if(res.status == 500) {
                        alert(res.message);
                    }
                }
            });

        });
        
        $(document).on('click', '.deleteInfoBtn', function (e) {
            e.preventDefault();

            if(confirm('Вы действительно желаете удалить эти данные?'))
            {
                var info_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "code.php",
                    data: {
                        'delete_info': true,
                        'info_id': info_id
                    },
                    success: function (response) {

                        var res = jQuery.parseJSON(response);
                        if(res.status == 500) {

                            alert(res.message);
                        }else{
                            alertify.set('notifier','position', 'top-right');
                            alertify.success(res.message);

                            $('#myTable').load(location.href + " #myTable");
                            
                            location.reload();
                        }
                    }
                });
            }
        });
        
        // Функция для получения суммы по счету в реальном времени
        function getInvoiceTotal() {
            var invoiceNumber = $('#filter_invoice').val().trim();
            
            // Если поле пустое, скрываем блок информации
            if (invoiceNumber === '') {
                $('#invoice_total_info').closest('.row').hide();
                return;
            }
            
            // Отправляем AJAX запрос для получения суммы
            $.ajax({
                type: "GET",
                url: "get_invoice_total.php",
                data: {
                    invoice_number: invoiceNumber
                },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status === 200) {
                        // Показываем блок с информацией и обновляем сумму
                        var html = '<div class="d-flex justify-content-between align-items-center">';
                        html += '<div><strong>Итоговая сумма по счету №' + invoiceNumber + ':</strong> ';
                        html += '<span class="fs-5">' + res.total_sum + ' руб.</span></div>';
                        html += '<div><a href="export_invoice.php?invoice_number=' + invoiceNumber + '" class="btn btn-sm btn-outline-primary">';
                        html += '<i class="fas fa-file-export"></i> Экспорт счета</a></div>';
                        html += '</div>';
                        
                        $('#invoice_total_info').html(html);
                        $('#invoice_total_info').closest('.row').show();
                    } else {
                        // Если ничего не найдено, показываем соответствующее сообщение
                        $('#invoice_total_info').html('По счету №' + invoiceNumber + ' не найдено записей.');
                        $('#invoice_total_info').closest('.row').show();
                    }
                },
                error: function() {
                    console.error('Ошибка при получении суммы по счету');
                }
            });
        }
        
        // Обработчик события изменения поля номера счета с задержкой
        var invoiceTimer;
        $('#filter_invoice').on('input', function() {
            clearTimeout(invoiceTimer);
            invoiceTimer = setTimeout(getInvoiceTotal, 500); // 500ms задержка для уменьшения количества запросов
        });
        
        // Инициализация при загрузке страницы
        $(document).ready(function() {
            // Если в URL есть параметр invoice_number, вызываем функцию получения суммы
            <?php if(!isset($_GET['invoice_number']) || empty($_GET['invoice_number'])): ?>
            $('#invoice_total_info').closest('.row').hide();
            <?php endif; ?>
        });
        
    });
    </script>
</body>
</html>
        