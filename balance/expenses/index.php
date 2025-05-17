<?
    
session_start();

if ( !isset($_SESSION["id"]) || $_SESSION["id"] > 4) {
    header("location: /");
    exit;
}

function get_text_type_payment($number){
    if( $number == 1) return 'Оплата услуги';
    if( $number == 2) return 'Оплата товара';
    if( $number == 3) return 'Коммунальные платежи';
    if( $number == 4) return 'Налоги';
    if( $number == 5) return 'Зарплата';
    if( $number == 6) return 'Штрафы';
    if( $number == 7) return 'Другое';
}

function get_confirm($id)
{   
  
    if($id == 0)
    {
        return 'table-primary';
    }
    
    if($id == 1)
    {
        return 'table-success';
    }
        
    if($id == 2)
    {
        return 'table-danger';
    }
    
}

function get_confirm_word($id)
{   
  
    if($id == 0)
    {
        return 'На рассмотрение';
    }
    
    if($id == 1)
    {
        return 'Одобрена';
    }
        
    if($id == 2)
    {
        return 'Отклонена';
    }
    
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
    
    <!-- Bootstrap icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css"
    />
    
    <title>ELDIR | Модуль Касса | Доход </title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
</head>
<body>
    <? require_once('../../nav.php'); ?>
    
    <!-- Add Info Modal -->
    <div class="modal fade" id="infoAddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить данные о платеже</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saveForm">
                <div class="modal-body">

                    <div id="errorMessage" class="alert alert-warning d-none"></div>
                    
                    <div class="mb-3">
                        <label for="">Дата операции</label>
                        <input type="date" name="date_operations" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label for="summa">Сумма платежа</label>
                        <input type="text" name="summa" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label for="type_payment">Тип платежа</label>
                        <select name="type_payment" class="form-select">
                            <option value="0">Выберите тип платежа</option>
                            <option value="1">Оплата услуги</option>
                            <option value="2">Оплата товара</option>
                            <option value="3">Коммунальные платежи</option>  
                            <option value="4">Налоги</option>
                            <option value="5">Зарплата</option>
                            <option value="6">Штрафы</option>
                            <option value="7">Другое</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Назначение платежа</label>
                        <input type="text" name="purpose_payment" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label for="">Комментарий</label>
                        <input type="text" name="comment" class="form-control" />
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
                <h5 class="modal-title" id="exampleModalLabel">Изменить данные о платеже</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateInfo">
                <div class="modal-body">

                    <div id="errorMessageUpdate" class="alert alert-warning d-none"></div>

                    <input type="hidden" name="info_id" id="info_id" >
                    
                    <div class="mb-3">
                        <label for="">Дата операции</label>
                        <input type="date" id="date_operations" name="date_operations" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label for="summa">Сумма платежа</label>
                        <input type="text" id="summa" name="summa" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label for="type_payment">Тип платежа</label>
                        <select id="type_payment" name="type_payment" class="form-select">
                            <option value="0">Выберите тип платежа</option>
                            <option value="1">Оплата услуги</option>
                            <option value="2">Оплата товара</option>
                            <option value="3">Коммунальные платежи</option>  
                            <option value="4">Налоги</option>
                            <option value="5">Зарплата</option>
                            <option value="6">Штрафы</option>
                            <option value="7">Другое</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Назначение платежа</label>
                        <input type="text" id="purpose_payment" name="purpose_payment" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label for="">Комментарий</label>
                        <input type="text" id="comment" name="comment" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm">Статус заяки</label>
                        <select id="confirm" name="confirm" class="form-select">
                            <option value="0">На рассмотрение</option>
                            <option value="1">Одобрена</option>
                            <option value="2">Отклонена</option>
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
                <h1>ELDIR | Модуль Касса | Расход </h1>
            </div>
        </div>
        <?  require_once('../nav_balance.php'); ?>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#infoAddModal">
                    Добавить данные
                </button>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="table-responsive-sm">
                    <table id="myTable" class=" table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
								<th>Дата операции</th>
                                <th width="150px">Статус</th>
                                <th>Сумма платежа</th>
                                <th>Тип платежа</th>
                                <th>Назначение платежа</th>
                                <th>Комментарий</th>
                                <th width="150px">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require __DIR__ . '/../../dbcon.php';

                            $query = "SELECT * FROM balance_income_expenses WHERE sign_of_calculation = 'expenses' AND history = 0  AND is_deleted = 0";
                            $query_run = mysqli_query($con, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {
                                foreach($query_run as $payment)
                                {   
                                    ?>
                                    <tr class="<?= get_confirm($payment['confirm']) ?>">
										<td><?= $payment['date_operations'] ?></td>
                                        <td><?= get_confirm_word($payment['confirm']) ?></td>
                                        <td><?= $payment['summa'] ?></td>
                                        <td><?= get_text_type_payment($payment['type_payment']) ?></td>
                                        <td><?= $payment['purpose_payment'] ?></td>
                                        <td><?= $payment['comment'] ?></td>
                                        <td>
                                            <button type="button" value="<?=$payment['id'];?>" class="editInfoBtn btn btn-primary btn-sm"><i class="bi bi-plus-square-fill"></i></button>
                                            <button type="button" value="<?=$payment['id'];?>" class="deleteInfoBtn btn btn-danger btn-sm"><i class="bi bi-trash text-light"></i></button>
                                            <button type="button" value="<?=$payment['id'];?>" class="confirmBtn btn btn-success btn-sm"><i class="bi bi-check-square-fill"></i></button>
                                            <button type="button" value="<?=$payment['id'];?>" class="rejectBtn btn btn-danger btn-sm"><i class="bi bi-exclamation-square-fill"></i></button>
                                            
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
   

    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({
                "language": {
                    "lengthMenu": "Показать _MENU_ записей на странице",
                    "zeroRecords": "Записи не найдено",
                    "info": "Показана страница _PAGE_ из _PAGES_",
                    "infoEmpty": "Нет записей",
                    "infoFiltered": "(отфильтровано из _MAX_ всего записей)",
                    "loadingRecords": "Загрузка...",
                    "processing":     "",
                    "search":         "Поиск:",
                    "paginate": {
                        "first":      "Первый",
                        "last":       "Последний",
                        "next":       "Следующий",
                        "previous":   "Предыдущий"
                    },
                    "aria": {
                        "sortAscending":  ": Сортировать по возврастанию",
                        "sortDescending": ": Сортировать по убыванию"
                    }
                },
                "order": [[0, 'desc']],
                "columnDefs": [
                    {
                        targets: 0,
                        render: DataTable.render.date()
                    }
                ]
            });
        });
    
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

                        $('#info_id').val(res.data.id);

                        $('#date_operations').val(res.data.date_operations);
                        $('#summa').val(res.data.summa);
                        $('#type_payment').val(res.data.type_payment);
                        $('#purpose_payment').val(res.data.purpose_payment);
                        $('#comment').val(res.data.comment);
                        
                        $('#confirm').val(res.data.confirm);
                      
                        $('#infoEditModal').modal('show');
                    }

                }
            });

        });

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
        
        $(document).on('click', '.confirmBtn', function (e) {
            e.preventDefault();

            if(confirm('Одобрить заявку?'))
            {
                var info_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "code.php",
                    data: {
                        'confirm_claim': true,
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
        
        $(document).on('click', '.rejectBtn', function (e) {
            e.preventDefault();

            if(confirm('Отклонить заявку?'))
            {
                var info_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "code.php",
                    data: {
                        'reject_claim': true,
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
        

    </script>
</body>
</html>